<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PhotoController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Event_PhotoController extends Touch_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('event_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($event_id = (int) $this->_getParam('event_id')) &&
          null !== ($event = Engine_Api::_()->getItem('event', $event_id)) )
      {
        Engine_Api::_()->core()->setSubject($event);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'upload-photo', // Not sure if this is the right
      'edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'event',
      'view' => 'event_photo',
      'edit' => 'event_photo',
    ));
  }

  public function listAction()
  {
    $this->view->event = $event = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $event->getSingletonAlbum();


    $photosUrl = $this->view->url(array(
      'route' => 'event_extended',
      'controller' => 'photo',
      'action' => 'list',
      'subject' => $event->getGuid()
    ));

    $this->view->navigation = new Zend_Navigation(array(

      new Zend_Navigation_Page_Uri(array(
        'uri' => $photosUrl,
        'label' => $this->view->translate("Photos"),
        'active' => true
      )),
      new Zend_Navigation_Page_Uri(array(
        'uri' => $event->getHref(),
        'label' => $this->view->translate('TOUCH_BACK_TO_ITEM', $this->view->touchSubstr($event->getTitle())),
        'active' => false
      ))
    ));

    if( !$this->_helper->requireAuth()->setAuthParams($event, null, 'view')->isValid() ) {
      return;
    }

    $select = $album->getCollectiblesSelect();

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(20);

    $this->view->canUpload = $event->authorization()->isAllowed(null, 'photo');
  }
  
  public function viewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->event = $event = $photo->getEvent();
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'edit');

    if( !$this->_helper->requireAuth()->setAuthParams($event, null, 'view')->isValid() ) {
      return;
    }

    if( !$viewer || !$viewer->getIdentity() || $photo->user_id != $viewer->getIdentity() ) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }
  }


  public function uploadAction()
  {
    if ($this->is_iPhoneUploading()){
      if (!isset($_FILES['picup-image-upload'])){
        return ;
      }
      $event = Engine_Api::_()->core()->getSubject();;
      $viewer = Engine_Api::_()->getItem('user', (int)$this->_getParam('owner_id'));
      if (!$event || !$viewer){
        return ;
      }
      $album = $event->getSingletonAlbum();
      Engine_Api::_()->user()->setViewer($viewer);

      $params = array(
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),

        'event_id' => $event->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      $file = $_FILES['picup-image-upload'];
      $this->view->photo_name = (isset($file['name'])) ? $file['name'] : '';
      $this->view->photo_id = $this->uploadPhoto($file, $params);

      return;
    }

    if( !$this->_helper->requireUser->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->event = $event = Engine_Api::_()->core()->getSubject();
    $album = $event->getSingletonAlbum();

    if( !$this->_helper->requireAuth()->setAuthParams($event, null, 'photo')->isValid() ) {
      return;
    }

    $this->view->form = $form = new Touch_Form_Event_Photo_Upload();
    $form->file->setAttrib('data', array('event_id' => $event->getIdentity()));

    $posts = $this->getRequest()->getPost();
    $photo_ids = array();
    if(array_key_exists('photos', $posts)){
      $photo_ids = explode(',', $posts['photos']);
    }

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    $params = array(
      // We can set them now since only one album is allowed
      'collection_id' => $album->getIdentity(),
      'album_id' => $album->getIdentity(),

      'event_id' => $event->getIdentity(),
      'user_id' => $viewer->getIdentity(),
    );

		if( !empty($_FILES['file']) ) {

			if (is_array($_FILES['file']['tmp_name'])){
				foreach($_FILES['file']['tmp_name'] as $k=>$v){
					$file['name'] = $_FILES['file']['name'][$k];
					$file['type'] = $_FILES['file']['type'][$k];
					$file['tmp_name'] = $_FILES['file']['tmp_name'][$k];
					$file['error'] = $_FILES['file']['error'][$k];
					$file['size'] = $_FILES['file']['size'][$k];
          $photo_ids[] = $this->uploadPhoto($file, $params);
				}
			} else {
        $photo_ids[] = $this->uploadPhoto($_FILES['file'], $params);
			}
		};

    foreach ($photo_ids as $key => $photo_id){
      if (!$photo_id){
        unset($photo_ids[$key]);
      }
    }
		if (count($photo_ids) > 0){
			$form->getElement('photos')->setValue($photo_ids);
		} else {
      $form->getElement('photos')->addError('TOUCH_NO_PHOTOS');
      return ;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('event_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'event_id' => $event->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $event, 'event_photo_upload', null, array('is_mobile' => true));

      // Do other stuff
      $count = 0;
      foreach( $photo_ids as $photo_id )
      {
        $photo = Engine_Api::_()->getItem("event_photo", $photo_id);
        if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if( $action instanceof Activity_Model_Action && $count < 8 )
        {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }

      $action->setFromArray(array('params' => array('count' => $count)))->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_PHOTOS_UPLOAD')),
      'parentRedirect' => $event->getHref(),
    ));

  }

  public function uploadPhoto($file, $params = array())
  {
    $photo_id = 0;

    if( !isset($file) || !is_uploaded_file($file['tmp_name']) ){
      return $photo_id;
    }

    $photoTable = Engine_Api::_()->getDbtable('photos', 'event');
    $db = $photoTable->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo = $photoTable->createRow();
      $photo->setFromArray($params);
      $photo->save();

      $photo->setPhoto($file);

      $photo_id = $photo->photo_id;

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      // throw $e;
    }
    return $photo_id;
  }

  public function editAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();

    if( !$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid() ) {
      return;
    }

    $this->view->form = $form = new Touch_Form_Event_Photo_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved')),
      'parentRefresh' => true,
      'closeSmoothbox' => true,
    ));
  }




  public function deleteAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();
    $event = $photo->getParent('event');

    if( !$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid() ) {
      return;
    }

    $this->view->form = $form = new Touch_Form_Event_Photo_Delete();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo deleted');


    $photosUrl = $this->view->url(array(
      'route' => 'event_extended',
      'controller' => 'photo',
      'action' => 'list',
      'subject' => $event->getGuid()
    ));

    $this->_forward('success', 'utility', 'touch', array(
      'messages' => array($this->view->message),
      'parentRedirect' => $photosUrl,
    ));




  }
}