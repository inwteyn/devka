<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: OfferController.php 27.09.12 17:28 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_OfferController extends Core_Controller_Action_Standard
{
  protected $viewer = null;
  protected $subject = null;
  protected $offer_id = 0;

  public function init()
  {
    $this->offer_id = $this->_getParam('offer_id', 0);
    $this->viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject()) {
      if ($this->offer_id && is_numeric($this->offer_id) && $this->offer_id > 0) {
        $this->subject = Engine_Api::_()->getItem('offer', $this->offer_id);
        if ($this->subject) {
          Engine_Api::_()->core()->setSubject($this->subject);
        }
      } else {
        return $this->_helper->redirector->gotoRoute(array(), 'offers_upcoming', true);
      }
    }

    if (!$this->subject) {
      return $this->_helper->redirector->gotoRoute(array(), 'offers_upcoming', true);
    }

    $this->view->navigation_edit = Engine_Api::_()->getApi('menus', 'core')->getNavigation('offer_edit');
  }

  public function viewAction()
  {
    if (!$this->subject->isEnable()) {
      if (!$this->subject->isOwner()) {
        return $this->_helper->redirector->gotoRoute(array(), 'offers_upcoming', true);
      }
    }
    //Render Layout Offers Profile
    $this->_helper->content
      ->setNoRender()
      ->setEnabled();
  }

  public function editAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams($this->subject, $this->viewer, 'edit')->isValid() ) {
      return 0;
    }

    $this->view->form = $form = new Offers_Form_Edit($this->subject->page_id);

    $this->subject->price_offer = round($this->subject->price_offer, 2);
     $this->view->enable_unique_code = $this->subject->enable_unique_code;

      if(!(int) $this->subject->starttime || !(int) $this->subject->endtime){
          $this->subject->starttime = $this->subject->endtime = null;
      }

      if(!(int) $this->subject->redeem_starttime || !(int) $this->subject->redeem_endtime){
          $this->subject->redeem_starttime = $this->subject->redeem_endtime = null;
      }



//      print_die($this->subject->toArray());
//      print_die((int) $this->subject->starttime);

    $form->populate($this->subject->toArray());

    if ($this->subject->type == 'store') {
      $products_ids = implode(',', Engine_Api::_()->offers()->getProductsOffer($this->offer_id));
      $form->getElement('products_ids')->setValue($products_ids);
    }

    $place = ($this->subject->page_id > 0) ? 'page' : 0;

    $form->setValuesRequire($this->subject->getRequireParams($place));

    $this->view->type = $this->subject->type;

    $products_ids = Engine_Api::_()->offers()->getProductsOffer($this->offer_id);

    if (!empty($products_ids)) {
      $this->view->products_ids = implode(',', $products_ids);
    }

    if (!$this->getRequest()->isPost()) {
      return false;
    }

    $params = $this->getRequest()->getParams();

    if (!$form->isValid($params)) {
      return 0;
    }

    if (($params['discount_type'] == 'percent') && ((int) $params['discount'] > 100)) {
      $form->addError('OFFERS_form_discount_incorrect');
      return 0;
    }

    if (!empty($params['enable_time_left'])) {
      if ((empty($params['starttime'])) || (empty($params['endtime']))) {
        $form->addError('OFFERS_form_time_left_empty');
        return 0;
      } else if ($params['starttime'] > $params['endtime']) {
        $form->addError('OFFERS_form_error_limit_time');
        return 0;
      }
    }

    if (!empty($params['enable_coupon_count']) && (empty($params['coupons_count']) || $params['coupons_count'] <= 0)) {
      $form->addError('OFFERS_form_coupons_count_incorrect');
      return 0;
    }

    if (!empty($params['enable_coupons_code']) && empty($params['coupons_code'])) {
      $form->addError('OFFERS_form_coupons_code_empty');
      return 0;
    }

    if ($params['type'] == 'reward') {
      if (!array_search(!0, $params['require'])) {
        $form->addError('OFFERS_form_require_empty');
        return 0;
      }
    }

      if ($params['type'] == 'store' && !isset($params['offers_require_enable'])) {
          foreach ($params['require'] as $key => $value) {
              $params['require'][$key] = 0;
          }
      }

    if ($params['type'] == 'store' && empty($params['products_ids'])) {
      $form->addError('OFFERS_form_select_products_empty');
      return 0;
    }

    $values = $form->getValues();
    if ($params['type'] == 'reward') {
      $values['require'] = $params['require'];
      if ($params['page_id']) {
        $requireCheckLike = new Offers_Plugin_Require_LikePage();
        $requireCheckLike->check($this->viewer, $this->offer_id, $params['page_id']);

        if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('rate')) {
          $requireCheckReview = new Offers_Plugin_Require_Review();
          $requireCheckReview->check($this->viewer, $this->offer_id, $params['page_id']);
        }
      }
    }

    Engine_Api::_()->getDbTable('offers', 'offers')->editOffer($values, $this->offer_id);

    $form->addNotice($this->view->translate('OFFERS_edit_success %s',
      $this->view->htmlLink($this->subject->getHref(), $this->view->translate('OFFERS_offer_view'))
    ));

    return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'offer_id' => $this->offer_id),
      'offers_specific',
      true);
  }

  public function deleteAction()
  {
    $this->subject = Engine_Api::_()->getItem('offer', $this->offer_id);
    if (!$this->_helper->requireAuth()->setAuthParams($this->subject, null, 'delete')->isValid()) return false;

    $this->view->form = new Offers_Form_Delete();

    if (!$this->subject) {
      $this->view->status = false;
      $this->view->editor = $this->view->translate('OFFERS_offer_no_to_delete');
    }

    if (!$this->getRequest()->isPost()) {
      return false;
    }

    $db = $this->subject->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $this->subject->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => false,
      'parentRedirect' => $this->view->url(array(), 'offers_upcoming', true),
      'messages' => array($this->view->translate("OFFERS_offer_deleted"))
    ));
  }

  public function addPhotosAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams($this->subject, $this->viewer, 'edit')->isValid() ) {
      return 0;
    }

    $this->view->offer = $this->subject;

    $this->view->form = $form = new Offers_Form_Upload($this->offer_id);

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
        $photos = array();
        foreach ($files_ids as $photo_id) {
          if ($photo_id == '') continue;
          $photo = Engine_Api::_()->getItem("offersphoto", $photo_id);
          if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) continue;

          $photo->collection_id = $this->subject->getIdentity();
          $photo->save();

          if ($this->subject->photo_id == 0) {
            $this->subject->photo_id = $photo->photo_id;
            $this->subject->save();
          }
          $photos[$count] = Engine_Api::_()->getDbTable('offersphotos', 'offers')->findRow($photo_id);
          $count++;
        }

        $db->commit();

        $this->_redirectCustom($this->view->url(array('action' => 'manage-photos', 'offer_id' => $this->offer_id), 'offers_specific', true));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } else {
      $this->_redirectCustom($this->view->url(array('action' => 'manage-photos', 'offer_id' => $this->offer_id), 'offers_specific', true));
    }
  }

  public function followAction()
  {
    $follow_status = $this->_getParam('follow_status');
    $this->view->form = $form = new Offers_Form_Follow($follow_status);

    if (!$this->getRequest()->isPost()) {
      return false;
    }

    $user_id = $this->viewer->getIdentity();
    $followsTbl = Engine_Api::_()->getDbTable('follows', 'offers');
    $followsTbl->setFollowStatus($this->offer_id, $user_id, 'active');

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format' => 'smoothbox',
      'messages' => array($this->view->translate("OFFERS_FOLLOW_The offer has been followed successfully.")),
    ));
  }

  public function managePhotosAction()
  {
    $this->view->offer = $this->subject;
    $this->view->paginator = $paginator = $this->subject->getCollectiblesPaginator();
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());
    $this->view->form = $form = new Offers_Form_Photos();
    $photo_ids = array();

    foreach ($paginator as $photo) {
      $photo_ids[$photo->getGuid()] = $photo->getIdentity();
      $subform = new Offers_Form_PhotoEdit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());

      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $form->populate($this->_getAllParams());
    $params = $form->getValues();
    /**
     * @var $offersTable Offers_Model_DbTable_Offers
     */
    $offersTable = Engine_Api::_()->getDbTable('offers', 'offers');
    $db = $offersTable->getAdapter();
    $db->beginTransaction();

    try {
      if (!empty($params['cover'])) {
        $this->subject->photo_id = $params['cover'];
        $this->subject->save();
      }

      if (count($params) > 2) {
        foreach ($params as $key => $values) {
          if ($photo_ids[$key]) {
            $photo = Engine_Api::_()->getItem("offersphoto", $photo_ids[$key]);
            if (isset($values['delete']) && $values['delete'] == '1') {
              $photo->delete();
            } else {
              $photo->collection_id = $this->subject->offer_id;
              $photo->description = $values['description'];
              $photo->title = $values['title'];
              $photo->save();
            }
          }
        }
      }

      $db->commit();

      $this->_redirectCustom($this->view->url(array('action' => 'manage-photos', 'offer_id' => $this->offer_id), 'offers_specific', true));
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }
  }

  public function editContactsAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams($this->subject, $this->viewer, 'edit')->isValid() ) {
      return 0;
    }

    $this->view->form = $form = new Offers_Form_Contacts();
    $tblContacts = Engine_Api::_()->getDbTable('contacts', 'offers');
    $contacts = $tblContacts->getContacts($this->offer_id);
    $form->populate($contacts->toArray());

    if (!$this->getRequest()->isPost()) {
      return false;
    }

    $params = $this->getRequest()->getParams();

    if (!$form->isValid($params)) {
      return false;
    }

    $values = $form->getValues();

    $tblContacts->editContacts($this->offer_id, $values);
  }

  public function printAction()
  {
    $contactsTbl = Engine_Api::_()->getDbTable('contacts', 'offers');
    $this->view->offer = $offer = $this->subject;
    $this->view->contacts = $contactsTbl->getContacts($offer->offer_id);
    $this->view->page = ($page = $offer->getPage()) ? $page : false;
    $this->view->currentDate = $currentDate = date('Y-m-d h:i:s', strtotime(Engine_Api::_()->offers()->getDatetime()));
  }

  public function emailAction()
  {
    $offer_id = $this->subject->offer_id;
    $this->view->form = $form = new Offers_Form_Email();

    if (!$this->getRequest()->isPost()) {
      return false;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return false;
    }

    $viewer = $this->viewer;
    $viewer_id = $viewer->getIdentity();

    $emailContent = $this->view->getEmailContent($offer_id, $viewer_id);
    $remove = array("\n", "\r\n", "\r");
    $emailContent = str_replace($remove, ' ', $emailContent);

    $validateEmail = new Zend_Validate_EmailAddress();
    $emails = $this->_getParam('email_address');

    $emails = explode(',',$emails);
    $i = 0;
    foreach($emails as $email) {
      $emails[$i] = trim($email);
      $i++;
    }

    $senderName = '';
    $senderEmail = '';

    if ($viewer_id != 0) {
      $senderName = $viewer['displayname'];
      $senderEmail = $viewer['email'];
    }

    foreach ($emails as $email) {
      if (!$validateEmail->isValid($email)) {
        return $form->addError($this->view->translate("OFFERS_%s is not valid email address, please correct and try again.", $email));
      }

      $mail_settings = array(
        'date' => time(),
        'email_content' => $emailContent,
        'recipient_email' => $email,
        'sender_name' => $senderName,
        'sender_email' => $senderEmail
      );

      // send email
      Engine_Api::_()->getApi('mail', 'core')->sendSystemRaw(
        $email,
        'offers_email_template',
        $mail_settings
      );
    }

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => false,
      'format'=> 'smoothbox',
      'messages' => array($this->view->translate('OFFERS_EMAIL_The offer has been sent successfully')),
    ));
  }
  
  public function largeMapAction()
  {
    if ($this->offer_id === 0) {
      return false;
    }

    $contactsRow = Engine_Api::_()->offers()->getContactsOffer($this->subject->getIdentity());
    $contacts = array();

    foreach($contactsRow as $key => $value){
      $contacts[$key] = $value;
    }

    $this->view->subject = $this->subject;
    $this->view->contacts = $contacts;
  }

  public function showAllParticipantsAction()
  {
    $users = Engine_Api::_()->offers()->getUsersSubscription($this->offer_id, null, true);

    $this->view->title = $this->subject->getTitle();
    $this->view->users = $users;
  }

  public function favoriteAction()
  {
    $offer_id = $this->_getParam('offer_id');
    $status = Engine_Api::_()->getDbTable('offers', 'offers')->getOfferById($offer_id)->favorite;

    $favoriteStatus = 'non_active';
    if ($status) {
      $favoriteStatus = 'active';
    }
    $this->view->form = $form = new Offers_Form_Favorite($favoriteStatus);

    if (!$this->getRequest()->isPost()) {
      return false;
    }

    if ($favoriteStatus == 'non_active') {
      Engine_Api::_()->getDbTable('offers', 'offers')->setFavoriteOffer($offer_id, 1);
      $message = 'OFFERS_FAVORITE_The offer has been made as favorite successfully';
    }
    else {
      Engine_Api::_()->getDbTable('offers', 'offers')->setFavoriteOffer($offer_id, 0);
      $message = 'OFFERS_FAVORITE_The offer has been made as simple successfully';
    }

    $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'format'=> 'smoothbox',
          'messages' => array($this->view->translate($message)),
    ));
  }
}