<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminManageController.php 2012-06-07 11:40 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_AdminManageController extends Core_Controller_Action_Admin
{
    protected $offer_id;
    protected $_subject;

    public function init()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('offer_admin_main', array(), 'offer_admin_main_manage');

        $this->offer_id = $this->_getParam('offer_id', 0);

        if ($this->offer_id > 0) {
            $this->_subject = Engine_Api::_()->getItem('offer', $this->offer_id);
        }
    }

    public function indexAction()
    {
        $this->view->filterForm = $filterForm = new Offers_Form_Admin_Manage_Filter();
        $values = array();
        // if demoadmin
        $this->view->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();


        if ($filterForm->isValid($this->_getAllParams())) {
            $values = $filterForm->getValues();
        }

        $page = $this->_getParam('page', 1);

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array(
            'order' => 'offer_id',
            'order_direction' => 'DESC',
        ), $values);

        $this->view->assign($values);

        $offersTbl = Engine_Api::_()->getDbTable('offers', 'offers');
        $categoriesTbl = Engine_Api::_()->getDbTable('categories', 'offers');
        $select = $offersTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('offers' => $offersTbl->info('name')))
            ->join(array('categories' => $categoriesTbl->info('name')), 'categories.category_id=offers.category_id', array('category_title' => 'title'));

        $select->order((!empty($values['order']) ? $values['order'] : ' offer_id') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC'));
        if (!empty($values['title'])) {
            $select->where('offers.title LIKE ?', '%' . $values['title'] . '%');
        }

        if (!empty($values['category']) && $values['category'] != -1) {
            $select->where('offers.category_id = ?', $values['category']);
        }

        if (!empty($values['type']) && $values['type'] != -1) {
            $select->where('offers.type = ?', $values['type']);
        }

        if (isset($values['enabled']) && $values['enabled'] != -1) {
            $select->where('offers.enabled = ?', $values['enabled']);
        }

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);
        $this->view->formValues = array_filter($values);
    }

    public function createAction()
    {
        if ($this->_getParam('get_products') == 1) {
            $data = $this->getParam('ids');
            $data = implode(',',$data);

            $storeTbl = Engine_Api::_()->getDbTable('products', 'store');
            $select = $storeTbl->select()
                ->where('product_id in ('.$data.')');

            $this->view->products = $storeTbl->fetchAll($select);

            $this->view->body = $this->view->render('application/modules/Offers/views/scripts/admin-manage/products.tpl');
            return;
        }


        $this->view->form = $form = new Offers_Form_Admin_Manage_Create();

        if (!$this->getRequest()->isPost()) {
            return false;
        }

        $params = $this->getRequest()->getParams();
        $offersTbl = Engine_Api::_()->getDbTable('offers', 'offers');

        if (!$form->isValid($params)) {
            $form->populate($form->getValues());
            return 0;
        }

        if (($params['discount_type'] == 'percent') && ((int)$params['discount'] > 100)) {
            $form->addError('OFFERS_form_discount_incorrect');
            $form->populate($form->getValues());
            return 0;
        }

        if (!empty($params['enable_time_left'])) {
            if ((empty($params['starttime'])) || (empty($params['endtime']))) {
                $form->addError('OFFERS_form_time_left_empty');
                $form->populate($form->getValues());
                return 0;
            } else if ($params['starttime'] > $params['endtime']) {
                $form->addError('OFFERS_form_error_limit_time');
                $form->populate($form->getValues());
                return 0;
            }
        }

        if (!empty($params['enable_coupon_count'])) {
            if (empty($params['coupons_count']) || $params['coupons_count'] <= 0) {
                $form->addError('OFFERS_form_coupons_count_incorrect');
                $form->populate($form->getValues());
                return 0;
            }
        }

        if ($params['type_code'] == 'offer_code') {
            if (empty($params['coupons_code'])) {
                $form->addError('OFFERS_form_coupons_code_empty');
                $form->populate($form->getValues());
                return 0;
            }

            if (!$offersTbl->checkCouponCodeOffer($params['coupons_code'])) {
                $form->addError('OFFERS_coupons_code_incorrect');
                $form->populate($form->getValues());
                return 0;
            }
        }

        if ($params['type'] == 'reward') {
            if (!array_search(!0, $params['require'])) {
                $form->addError('OFFERS_form_require_empty');
                $form->populate($form->getValues());
                return 0;
            }
        }

        if ($params['type'] == 'store' && empty($params['products_ids'])) {
            $form->addError('OFFERS_form_select_products_empty');
            $form->populate($form->getValues());
            return 0;
        }

        if ($params['type'] == 'store' && !isset($params['offers_require_enable'])) {
            foreach ($params['require'] as $key => $value) {
                $params['require'][$key] = 0;
            }
        }

        $values = $form->getValues();

        if ($params['type'] == 'reward') {
            if (!array_search(!0, $params['require'])) {
                $form->addError('OFFERS_form_require_empty');
            }
            $values['require'] = $params['require'];
        }

        $this->offer_id = $offersTbl->setOffer($values, Engine_Api::_()->user()->getViewer()->getIdentity());

        // Permission Activity feed and Wall Feed
        $offer = Engine_Api::_()->getItem('offer', $this->offer_id);
        $authorization = Engine_Api::_()->authorization()->context;
        $roles = array('everyone', 'registered');

        foreach ($roles as $role) {
            $authorization->setAllowed($offer, $role, 'view', true);
            $authorization->setAllowed($offer, $role, 'comment', true);
        }

        // Add activity
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $offer, 'offer_new');

        if ($action) {
            $api->attachActivity($action, $offer);
        }

        if ($values['file'][0] == '') {
            // Redirect
            return $this->_helper->redirector->gotoRoute(array('action' => 'set-contacts-offer', 'offer_id' => $this->offer_id), 'offer_admin_manage', true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage-photos', 'offer_id' => $this->offer_id, 'created' => 1), 'offer_admin_manage', true);
        }
    }

    public function managePhotosAction()
    {
        $offer_id = $this->_getParam('offer_id');
        $offersTbl = Engine_Api::_()->getDbTable('offers', 'offers');
        $select = $offersTbl->select()->where('offer_id = ?', $offer_id);
        $offer = $offersTbl->fetchRow($select);

        $this->view->offer = $offer;

        $this->view->paginator = $paginator = $offer->getCollectiblesPaginator();

        if ($paginator->getTotalItemCount() > 0) {

            $paginator->setCurrentPageNumber($this->_getParam('page'));
            $paginator->setItemCountPerPage($paginator->getTotalItemCount());

            $this->view->form = $form = new Offers_Form_Admin_Manage_Photos();

            if ($this->_getParam('created') == '1') {
                $this->view->message = Zend_Registry::get('Zend_Translate')->_("OFFERS_Offer was successfully created.");
                $this->view->created = 1;
            }

            foreach ($paginator as $photo) {
                $subform = new Offers_Form_PhotoEdit(array('elementsBelongTo' => $photo->getGuid()));
                $subform->populate($photo->toArray());
                $form->addSubForm($subform, $photo->getGuid());
                $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
            }
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }
        $params = $this->_getAllParams();
        $offer->saveValues($params);

        if ($this->_getParam('created') == '1') {
            return $this->_helper->redirector->gotoRoute(array('action' => 'set-contacts-offer', 'offer_id' => $offer_id), 'offer_admin_manage', true);
        } else {
            $this->view->paginator = $paginator = $offer->getCollectiblesPaginator();

            if ($paginator->getTotalItemCount() > 0) {

                $paginator->setCurrentPageNumber($this->_getParam('page'));
                $paginator->setItemCountPerPage($paginator->getTotalItemCount());

                foreach ($paginator as $photo) {
                    $subform = new Offers_Form_PhotoEdit(array('elementsBelongTo' => $photo->getGuid()));
                    $subform->populate($photo->toArray());
                    $form->addSubForm($subform, $photo->getGuid());
                    $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
                }
            }
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("OFFERS_Changes were successfully saved.");
            return $this->_helper->redirector->gotoRoute(array('action' => 'set-contacts-offer', 'offer_id' => $offer_id), 'offer_admin_manage', true);
        }
    }

    public function setContactsOfferAction()
    {
        $this->view->form = new Offers_Form_Contacts();

        if (!$this->getRequest()->isPost()) {
            return false;
        }

        $params = $this->getRequest()->getParams();

        $form = new Offers_Form_Contacts();

        if (!$form->isValid($params)) {
            return false;
        }

        $values = $form->getValues();

        Engine_Api::_()->getDbTable('contacts', 'offers')->setContacts($this->_getParam('offer_id'), $values);

        return $this->_helper->redirector->gotoRoute(array('action' => 'index'), 'offer_admin_manage', true);
    }

    public function addPhotosAction()
    {
        $offer_id = $this->_getParam('offer_id');
        $offer = Engine_Api::_()->getItem("offer", $offer_id);
        $this->view->offer = $offer;

        $this->view->form = $form = new Offers_Form_Upload($offer_id);

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        $files_ids = $values['file'];

        $table = Engine_Api::_()->getItemTable('offersphoto');
        $db = $table->getAdapter();
        $db->beginTransaction();

        if (count($files_ids) > 0) {
            try {
                // Do other stuff
                $count = 0;
                foreach ($files_ids as $photo_id) {
                    if ($photo_id == '') continue;
                    $photo = Engine_Api::_()->getItem("offersphoto", $photo_id);
                    if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) continue;

                    $photo->collection_id = $offer->offer_id;
                    $photo->save();

                    if ($offer->photo_id == 0) {
                        $offer->photo_id = $photo->photo_id;
                        $offer->save();
                    }
                    $count++;
                }

                $db->commit();
                $this->_redirectCustom($this->view->url(array(
                        'action' => 'manage-photos',
                        'offer_id' => $offer->getIdentity()),
                    'offer_admin_manage', true));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else {
            $this->_redirectCustom($this->view->url(array(
                    'controller' => 'manage',
                    'action' => 'manage-photos',
                    'offer_id' => $offer->getIdentity()),
                'offer_admin_manage', true));
        }
    }

    public function deleteAction()
    {
        $offer_id = (int)$this->_getParam('offer_id');

        if ($offer_id) {
            $this->getTable()->deleteOffer($offer_id);
        }

        $this->redirect();
    }

    public function deleteAllAction()
    {
        $offers_ids = $this->_getParam('delete');

        if (!isset($offers_ids) || empty($offers_ids)) {
            $this->redirect();
            return 0;
        }

        $this->getTable()->deleteOffer($offers_ids);

        $this->redirect();
    }

    public function disableAction()
    {
        $offer_id = (int)$this->_getParam('offer_id');
        $isEnabled = (int)$this->_getParam('isEnabled');

        if ($offer_id) {
            $offersTable = Engine_Api::_()->getDbTable('offers', 'offers');
            if ($isEnabled) {
                $offersTable->update(array('enabled' => 0), array('offer_id = ?' => $offer_id));
            } else {
                $offersTable->update(array('enabled' => 1), array('offer_id = ?' => $offer_id));
            }
        }

        $this->redirect();
    }

    public function changeStatusFeatureAction()
    {
        Engine_Api::_()->getDbTable('offers', 'offers')->setFeature($this->offer_id);
        $this->redirect();
    }

    public function redirect($url = null, array $options = array())
    {
        return $this->_redirectCustom($this->view->url(array('action' => 'index'), 'offer_admin_manage', true));
    }

    protected function getTable()
    {
        return Engine_Api::_()->getDbTable('offers', 'offers');
    }
}