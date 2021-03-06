<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ProductController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_ProductController extends Store_Controller_Action_Standard
{

    public function init()
    {

        // he@todo this may not work with some of the content stuff in here, double-check
        /**
         * @var $subject Store_Model_Product
         */
        $subject = null;

        if (!Engine_Api::_()->core()->hasSubject('store_product')) {
            $id = $this->_getParam('product_id');

            if (null !== $id) {
                $subject = Engine_Api::_()->getItem('store_product', $id);
                if ($subject && null != ($page = $subject->getStore())) {
                    $approved = $page->approved;
                } else {
                    $approved = 1;
                }

                if ($subject && $approved) {
                    Engine_Api::_()->core()->setSubject($subject);
                } else {
                    if ($this->_getParam('format') == 'json') {
                        $this->view->status = 0;
                        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product doesn\'t exist');
                        return 0;
                    }
                    $this->_redirectCustom(
                        $this->view->url(
                            array(
                                'action' => 'index'
                            ), 'store_general', true
                        )
                    );
                }
            }
        }

        $this->_helper->requireSubject('store_product');
    }

  public function quickAction()
  {
    $id = $this->_getParam('product_id');
    if (!$id) {
      $this->view->status = false;
      $this->view->code = 1;
      return;
    }

    $this->view->product = $product = Engine_Api::_()->getItem('store_product', $id);
    if (!$product) {
      $this->view->status = false;
      $this->view->code = 1;
      return;
    }

    /**
     * @var $viewer     User_Model_User
     * @var $cartTb     Store_Model_DbTable_Carts
     * @var $cart       Store_Model_Cart
     */

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $cartTb = Engine_Api::_()->getItemTable('store_cart');
    $this->view->cart = $cart = $cartTb->getCart($viewer->getIdentity());
    $this->view->item_id = ($cart) ? ($item = $cart->getRowByProduct($product->getIdentity())) ? $item->getIdentity() : 0 : 0;
    $this->view->allowOrder = Engine_Api::_()->store()->allowOrder($viewer);

    $this->view->hostUrl = (_ENGINE_SSL ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->view->allowFree = $allowFree = $settings->getSetting('store.free.products', 0);

    //reviews
    $isRateEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('rate');
    if($isRateEnabled) {
      $tbl = Engine_Api::_()->getDbTable('productreviews', 'rate');
      $this->view->reviews = $reviews = $tbl->getPaginator($id, $viewer->getIdentity(), 1, 2);
      $this->view->types = $types = Engine_Api::_()->getApi('core', 'rate')->getProductTypes($id);
      $this->view->countOptions = count($types);
    }
  }

    public function indexAction()
    {

        /**
         * @var $subject Store_Model_Product
         */
        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->content = $this->_getParam('content', null);

        // Increment view count

        if (!$subject->isProductStoreEnabled() || !$subject->getQuantity()) {
            $this->_redirectCustom($this->view->url(
                array('action' => 'index'),
                'store_general', true));
        }

        if (!$subject->getOwner()->isSelf($viewer)) {
            $subject->view_count++;
            $subject->save();
        }

        // Get styles
        $table = Engine_Api::_()->getDbtable('styles', 'core');
        $select = $table->select()
            ->where('type = ?', $subject->getType())
            ->where('id = ?', $subject->getIdentity())
            ->limit();

        $row = $table->fetchRow($select);


        if (null !== $row && !empty($row->style)) {
            $this->view->headStyle()->appendStyle($row->style);
        }

        // Render
        $this->_helper->content
            ->setNoRender()
            ->setEnabled();
    }

    public function deleteAction()
    {
        $product_id = $this->_getParam('product_id');

        if (null == ($product = Engine_Api::_()->getItem('store_product', $product_id))) {
            return false;
        };
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $product->owner_id)
            return;

        $this->view->form = $form = new Store_Form_Product_Delete();
        $form->getElement('product_id')->setValue($product_id);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $productTable = Engine_Api::_()->getDbTable('products', 'store');
        $db = $productTable->getAdapter();
        $db->beginTransaction();

        try {

            $product->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }


        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected product has been deleted.');
        $this->_forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance()
                        ->getRouter()->assemble(
                            array(
                                'module' => 'store',
                                'controller' => 'products',
                                'action' => 'index'
                            ),
                            'default', true
                        ),
                'messages ' => Array($this->view->message)
            )
        );
    }

    public function wishAction()
    {
        $do = $this->_getParam('do', 'add');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!in_array($do, array('add', 'remove')) || !$viewer->getIdentity()) {
            $this->view->status = false;
            return;
        }

        /**
         * @var $table   Store_Model_DbTable_Wishes
         * @var $product Store_Model_Product
         */
        $this->view->status = 1;
        $product = Engine_Api::_()->core()->getSubject();
        $table = Engine_Api::_()->getDbTable('wishes', 'store');
        if ($product->isWished() && $do == 'remove') {
            $select = $table->select()
                ->where('user_id = ?', $viewer->getIdentity())
                ->where('product_id = ?', $product->getIdentity())
                ->limit(1);
            $row = $table->fetchRow($select);
            $row->delete();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product successfully removed from Wishlist.');
        } elseif (!$product->isWished() && $do == 'add') {
            $row = $table->createRow();
            $row->product_id = $product->getIdentity();
            $row->user_id = $viewer->getIdentity();
            $row->save();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product successfully added to Wishlist.');
        }
        $this->view->count = $table->getWishesCount($product->getIdentity());
    }

    public function detailsAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->item = $product = Engine_Api::_()->core()->getSubject();
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $cartTb = Engine_Api::_()->getItemTable('store_cart');
        $cart = $cartTb->getCart($viewer->getIdentity());
        $this->view->item_id = ($cart) ? ($item = $cart->getRowByProduct($product->getIdentity())) ? $item->getIdentity() : 0 : 0;
        //$this->view->allowOrder = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order');
        $this->view->allowOrder = Engine_Api::_()->store()->allowOrder($viewer);
    }
}