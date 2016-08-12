<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ProfileController.php 2012-06-09 10:23 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_ProfileController extends Core_Controller_Action_Standard
{
  protected $subject;
  protected $viewer;

  public function init()
  {
    $page_enabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

    if (!$page_enabled) {
      $this->_forward('notfound', 'error', 'core');
      return;
    }

    $page_id = $this->_getParam('page_id', 0);
    $this->view->pageObject = $subject = ($page_id) ? Engine_Api::_()->getItem('page', $page_id) : null;

    if ($subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)) {
      $subject = null;
    }

    $this->subject = $subject;
    if ($subject) {
      Engine_Api::_()->core()->setSubject($subject);
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->viewer = ($viewer) ? $viewer : 0;
  }

  public function indexAction()
  {
    $filter = $this->_getParam('filter', 'upcoming');

    if (!$this->subject) {
      return false;
    }

    if ($filter != 'upcoming' && $filter != 'past' && $filter != 'manage')
      $filter = 'upcoming';

    $params = array(
      'page_id' => $this->subject->getIdentity(),
      'filter' => $filter,
      'user_id' => $this->viewer->getIdentity(),
      'page_num' => $this->_getParam('page_num', 1)
    );

    $table = Engine_Api::_()->getDbTable('offers', 'offers');

    $this->view->paginator = $table->getOffersPaginator($params);

    $this->view->html = $this->view->render('list.tpl');
    $this->view->count = $table->getCount(array('page_id' => $this->subject->getIdentity(), 'filter' => $filter));
  }

  public function createAction()
  {
    if (!$this->getRequest()->isPost()) {
      return 0;
    }

    $offersTbl = Engine_Api::_()->getDbTable('offers', 'offers');
    $params = $this->getRequest()->getParams();
    $message = '';

    if (empty($params['title'])) {
      $message .= $this->view->translate('OFFERS_pageoffers_create_title_empty');
    }

    if ((empty($params['discount'])) || ($params['discount'] <= 0)) {
      $message .= $this->view->translate('OFFERS_pageoffers_create_discount_empty');
    }

    if (($params['discount_type'] == 'percent') && ((int)$params['discount'] > 100)) {
      $message .= $this->view->translate('OFFERS_form_discount_incorrect');
    }

    if (empty($params['description'])) {
      $message .= $this->view->translate('OFFERS_form_description_empty');
    } else {
      unset($params['offer_description']);
    }

    if (!empty($params['enable_time_left'])) {
      if ((empty($params['starttime'])) || (empty($params['endtime']))) {
        $message .= '';
      } else if ($params['starttime'] > $params['endtime']) {
        $message .= $this->view->translate('OFFERS_form_error_limit_time');
      }
    }

    if (!empty($params['enable_coupon_count'])) {
      if (empty($params['coupons_count']) || $params['coupons_count'] <= 0) {
        $message .= $this->view->translate('OFFERS_form_coupons_count_incorrect');
      }
    }

    if ($params['type_code'] == 'offer_code') {
      if (empty($params['coupons_code'])) {
        $message .= $this->view->translate('OFFERS_form_coupons_code_empty');
      }

      if (!$offersTbl->checkCouponCodeOffer($params['coupons_code'])) {
        $message .= $this->view->translate('OFFERS_coupons_code_incorrect');
      }
    }

    if ($params['type'] == 'store' && empty($params['products_ids'])) {
      $message .= $this->view->translate('OFFERS_form_select_products_empty');
    }

    if ($params['type'] == 'reward' || $params['type'] == 'store') {
      if (!array_search(!0, $params['require'])) {
        $message .= $this->view->translate('OFFERS_form_require_empty');
      }
    }

    if (!empty($message)) {

      $message .= $this->view->translate('Please %s and check the form.', $this->view->htmlLink('javascript:void(0);', $this->view->translate('OFFERS_form_go_back'), array(
        'onclick' => 'Offers.goForm();'
      )));

      $this->view->result = false;
      $this->view->message = $message;
      $this->view->html = $this->view->render('message.tpl');
      return 0;
    }

    $this->view->offer_id = $offer_id = $offersTbl->setOffer($params, $this->viewer->getIdentity());

    if ($params['type'] == 'reward' || $params['type'] == 'store') {
      $requireCheckLike = new Offers_Plugin_Require_LikePage();
      $requireCheckLike->check($this->viewer, $offer_id, $params['page_id']);

      if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('rate')) {
        $requireCheckReview = new Offers_Plugin_Require_Review();
        $requireCheckReview->check($this->viewer, $offer_id, $params['page_id']);
      }
    }

    // Gallery
    if (isset($params['file']) && $params['file'][0] != '') {
      if (!is_array($params['file'])) {
        $params['file'] = explode(' ', trim($params['file']));
      }
      if (isset($params['file']) && count($params['file']) && !empty($params['file'])) {
        foreach ($params['file'] as $photo_id) {
          $photo = Engine_Api::_()->getItem("offersphoto", $photo_id);
          $photo->collection_id = $offer_id;
          $photo->save();
        }
      }
    }

    // Permission Activity feed and Wall Feed
    $offer = Engine_Api::_()->getItem('offer', $offer_id);
    $authorization = Engine_Api::_()->authorization()->context;
    $roles = array('everyone', 'registered');

    foreach ($roles as $role) {
      $authorization->setAllowed($offer, $role, 'view', true);
      $authorization->setAllowed($offer, $role, 'comment', true);
    }

    // Add activity
    $api = Engine_Api::_()->getDbtable('actions', 'activity');
    $action = $api->addActivity($this->viewer, $this->subject, 'page_offer_new', null, array('link' => 'javascript:void(0)'));
    $mainAction = $api->addActivity($this->viewer, $this->subject, 'new_offer_page', null, array('link' => 'javascript:void(0)'));

    if ($action) {
      $api->attachActivity($action, $offer);
    }

    if ($mainAction) {
      $api->attachActivity($mainAction, $offer);
    }

    $this->view->count = $offersTbl->getCount(array('page_id' => $this->subject->getIdentity(), 'filter' => 'upcoming'));
    $this->view->result = true;
    $this->view->message = $this->view->translate('OFFERS_create_success');
    $this->view->html = $this->view->render('message.tpl');
  }

  public function setContactsOfferAction()
  {
    if (!$this->getRequest()->isPost()) {
      return false;
    }

    $params = $this->getRequest()->getParams();
    $form = new Offers_Form_Contacts();

    if (!$form->isValid($params)) {
      return false;
    }
    $values = $form->getValues();

    if (!$form->isValid($values)) {
      return false;
    }
    Engine_Api::_()->getDbTable('contacts', 'offers')->setContacts($params['offer_id'], $values);

    $this->view->result = true;
    $this->view->message = $this->view->translate('OFFERS_add_contacts_success');
    $this->view->html = $this->view->render('message.tpl');
  }

  public function managePhotosAction()
  {
    $offer_id = $this->_getParam('offer_id');

    if (!$offer_id) {
      return false;
    }

    $offer = Engine_Api::_()->getItem('offer', $offer_id);

    $this->view->offer = $offer;

    $this->view->paginator = $paginator = $offer->getCollectiblesPaginator();

    if ($paginator->getTotalItemCount() > 0) {

      $paginator->setCurrentPageNumber($this->_getParam('page'));
      $paginator->setItemCountPerPage($paginator->getTotalItemCount());

      $this->view->form = $form = new Offers_Form_Photos();

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


      $this->view->html = $this->view->render('_manage_photos.tpl');
      $this->view->result = true;
      if (!$this->getRequest()->isPost()) {
        return;
      }

      $params = $this->_getAllParams();
      $offer->saveValues($params);

      if ($this->getRequest()->getParam('action') == 'save') {
        $this->view->result = true;
      }
    } else {
      $this->view->result = false;
    }
  }
}