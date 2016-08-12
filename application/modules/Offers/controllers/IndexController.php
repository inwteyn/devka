<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2012-06-07 11:40 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) $this->_forward('upload-photo', null, null, array('format' => 'json'));
    if (isset($_GET['rp'])) $this->_forward('remove-photo', null, null, array('format' => 'json'));
  }

  public function browseAction()
  {
    //Render Layout Offers
    $this->_helper->content
      ->setNoRender()
      ->setEnabled()
    ;
  }

  public function changeStatusCouponAction()
  {

    $offer_id = $this->_getParam('offer_id');
    $user_id = $this->_getParam('user_id');
    $offerTbl = Engine_Api::_()->getDbTable('subscriptions', 'offers');

    $new_status = $offerTbl->changeStatusCoupon($offer_id, $user_id);
    $this->view->new_status = $new_status;

  }

  public function uploadPhotoAction()
  {
    try{
    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }
    $db = Engine_Api::_()->getDbtable('offersphotos', 'offers')->getAdapter();
    $db->beginTransaction();
    }catch (Exception $e) {print_log($e->getMessage());}
    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();

      $params = array('owner_id' => $viewer->getIdentity());

      $photo_id = Engine_Api::_()->offers()->createPhoto($params, $_FILES['Filedata'])->getIdentity();

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo_id;

      $db->commit();
    }
    catch( Exception $e )
    {

      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');

      return;
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

      $this->view->form = $form = new Offers_Form_Photos();

      if ($this->_getParam('created') == '1') {
        $this->view->message = Zend_Registry::get('Zend_Translate')->_("OFFERS_Offer was successfully created.");
        $this->view->created = 1;
      }

      foreach( $paginator as $photo ) {
        $subform = new Offers_Form_PhotoEdit(array('elementsBelongTo' => $photo->getGuid()));
        $subform->populate($photo->toArray());
        $form->addSubForm($subform, $photo->getGuid());
        $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
      }
    }

    if( !$this->getRequest()->isPost() ) {
      return;
    }
    $params = $this->_getAllParams();
    $offer->saveValues($params);

    if ($this->_getParam('created') == '1') {
      return $this->_helper->redirector->gotoRoute(array('action'=>'set-contacts-offer', 'offer_id'=>$this->offer_id), 'offer_admin_manage', true);
    }
    else {
      $this->view->paginator = $paginator = $offer->getCollectiblesPaginator();

      if ($paginator->getTotalItemCount() > 0) {

        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());

        foreach( $paginator as $photo ) {
          $subform = new Offers_Form_Admin_Manage_PhotoEdit(array('elementsBelongTo' => $photo->getGuid()));
          $subform->populate($photo->toArray());
          $form->addSubForm($subform, $photo->getGuid());
          $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
        }
      }
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("OFFERS_Changes were successfully saved.");
      return $this->_helper->redirector->gotoRoute(array('action'=>'set-contacts-offer', 'offer_id'=>$this->offer_id), 'offer_admin_manage', true);
    }
  }

  public function removePhotoAction()
  {
    $photo_id = $this->_getParam('photo_id');

    if ($photo_id == null){
      return ;
    }

    $photo = Engine_Api::_()->getItem('offersphoto', $photo_id);
    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->delete();
      $db->commit();
      $this->view->success = true;
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Unknown database error');
      throw $e;
    }
  }


  public function generateCouponsCodeAction()
  {
    $this->view->code = Engine_Api::_()->offers()->generateCouponsCode();
  }

  protected function getTable()
  {
    return Engine_Api::_()->getDbTable('offers', 'offers');
  }

}