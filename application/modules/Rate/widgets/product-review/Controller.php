<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Rate_Widget_ProductReviewController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {

    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
      return $this->setNoRender();
    }

    if (Engine_Api::_()->core()->hasSubject()) {
      /**
       * @var $subject Store_Model_Product
       */
      $subject = Engine_Api::_()->core()->getSubject();
      if (!$subject instanceof Store_Model_Product) {
        return $this->setNoRender();
      }
    } else {
      return $this->setNoRender();
    }



    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->content = $content = $request->getParam('content');

    $content_id = $request->getParam('content_id');

    if($content == 'productreview' && $content_id) {
      $r = Engine_Api::_()->getItem($content, $content_id);
      if($r) {
        $this->view->init_js = "ProductReview.initView({$content_id});";
      }
    }

    $this->view->subject = $subject;
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    /*if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }*/

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/product-review-ajax';
    $this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/';
    $this->view->addScriptPath($path);

    $this->view->headTranslate(array('RATE_REVIEW_DELETE', 'RATE_REVIEW_DELETEDESC'));

    $this->view->id = $id = $subject->getIdentity();
      $getC = $subject->getCategory();

      $categories = Engine_Api::_()->getApi('core', 'rate')->getStoreCategories();
      foreach ($categories as $category) {
          if($getC['category']== $category->label){
                $option = $category->option_id;
          }

      }


      $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');
      $select = $tbl_type->select()
          ->where('category_id = ?', $option)
          ->where('type = ?', 'store')
          ->order('order');
      $types = $tbl_type->fetchAll($select);

    $this->view->types = $types = $types;
    $this->view->countOptions = count($types);

    $form = new Rate_Form_ProductReview_Create;
    $this->view->js = $form->addVotes($types);
    $this->view->form = $form;

    //$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pagereview');

    $p = $this->_getParam('page', 1);
    //$this->view->content_info = $content_info = $subject->getContentInfo();

    if (!empty($content_info['content'])) {
      if ($content_info['content'] == 'review') {
        if ($review = Engine_Api::_()->getDbTable('productreviews', 'rate')->fetchRow('productreview_id=' . $content_info['content_id'])
        ) {
          $this->view->init_js = "ProductReview.initView(" . $review->getIdentity() . ");";
        }
      } else if ($content_info['content'] == 'review_page') {
        $p = $content_info['content_id'];
      }
    }

    $tbl = Engine_Api::_()->getDbTable('productreviews', 'rate');
    $this->view->paginator = $paginator = $tbl->getPaginator($id, $viewer->getIdentity(), $p);
    // @TODO FIX
    $this->view->isAllowedPost = $tbl->isAllowedPost($id, $viewer);

    // is allowed remove
    /*$this->view->isAllowedRemove = Engine_Api::_()->getApi('core', 'rate')
      ->isAllowRemoveReview($id, $viewer);*/

    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $this->view->paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}