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

class Classified_PhotoController extends Touch_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('classified_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($classified_id = (int) $this->_getParam('classified_id')) &&
          null !== ($classified = Engine_Api::_()->getItem('classified', $classified_id)) )
      {
        Engine_Api::_()->core()->setSubject($classified);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'upload-photo', // Not sure if this is the right
      'edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'classified',
      'upload' => 'classified',
      'view' => 'classified_photo',
      'edit' => 'classified_photo',
    ));
  }


  public function editAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();

    if( !$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid() ) {
      return;
    }

    $this->view->form = $form = new Touch_Form_Classified_Photo_Edit();

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
    $db = Engine_Api::_()->getDbtable('photos', 'classified')->getAdapter();
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



  public function listAction()
  {
    $this->view->classified = $classified = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $classified->getSingletonAlbum();


    $photosUrl = $this->view->url(array(
      'route' => 'classified_extended',
      'controller' => 'photo',
      'action' => 'list',
      'subject' => $classified->getGuid()
    ));

    $this->view->navigation = new Zend_Navigation(array(

      new Zend_Navigation_Page_Uri(array(
        'uri' => $photosUrl,
        'label' => $this->view->translate("Photos"),
        'active' => true
      )),
      new Zend_Navigation_Page_Uri(array(
        'uri' => $classified->getHref(),
        'label' => $this->view->translate('TOUCH_BACK_TO_ITEM', $this->view->touchSubstr($classified->getTitle())),
        'active' => false
      ))
    ));

    if( !$this->_helper->requireAuth()->setAuthParams($classified, null, 'view')->isValid() ) {
      return;
    }

    $select = $album->getCollectiblesSelect();

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(20);

    $this->view->canUpload = $classified->authorization()->isAllowed(null, 'photo');
  }

  public function viewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->classified = $classified = $photo->getClassified();
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'edit');

    if( !$this->_helper->requireAuth()->setAuthParams($classified, null, 'view')->isValid() ) {
      return;
    }

  }


  public function uploadAction()
  {
    if ($this->is_iPhoneUploading()){
      if (!isset($_FILES['picup-image-upload'])){
        return ;
      }
      $classified = Engine_Api::_()->core()->getSubject();;
      $viewer = Engine_Api::_()->getItem('user', (int)$this->_getParam('owner_id'));
      if (!$classified || !$viewer){
        return ;
      }
      $album = $classified->getSingletonAlbum();
      Engine_Api::_()->user()->setViewer($viewer);

      $params = array(
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),

        'classified_id' => $classified->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      $file = $_FILES['picup-image-upload'];
      $this->view->photo_name = (isset($file['name'])) ? $file['name'] : '';
      $this->view->photo_id = $this->uploadPhoto($file, $params);

      return;
    }



    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->classified = $classified = Engine_Api::_()->core()->getSubject();
    $album = $classified->getSingletonAlbum();

    if( !$this->_helper->requireAuth()->setAuthParams($classified, null, 'photo')->isValid() ) {
      return;
    }

    $this->view->form = $form = new Touch_Form_Classified_Photo_Upload();
    $form->file->setAttrib('data', array('classified_id' => $classified->getIdentity()));


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

      'classified_id' => $classified->getIdentity(),
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
    $table = Engine_Api::_()->getItemTable('classified_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'classified_id' => $classified->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $classified, 'classified_photo_upload', null, array('is_mobile' => true));

      // Do other stuff
      $count = 0;
      foreach( $photo_ids as $photo_id )
      {
        $photo = Engine_Api::_()->getItem("classified_photo", $photo_id);
        if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if ($classified->photo_id == 0) {
          $classified->photo_id = $photo->file_id;
          $classified->save();
        }

        if( $action instanceof Activity_Model_Action && $count < 8 )
        {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }

      if ($action){
        $action->setFromArray(array('params' => array('count' => $count)))->save();
      }


      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_PHOTOS_UPLOAD')),
      'parentRedirect' => $classified->getHref(),
    ));

  }

  public function uploadPhoto($file, $params = array())
  {
    $photo_id = 0;
    if( !isset($file) || !is_uploaded_file($file['tmp_name']) ){
      return;
    }

    $photoTable = Engine_Api::_()->getDbtable('photos', 'classified');
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
      return;
    }
    return $photo_id;
  }



  public function deleteAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();
    $classified = $photo->getParent('classified');

    if( !$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid() ) {
      return;
    }

    $this->view->form = $form = new Touch_Form_Classified_Photo_Delete();


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
    $db = Engine_Api::_()->getDbtable('photos', 'classified')->getAdapter();
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
      'subject' => $classified->getGuid()
    ));

    $this->_forward('success', 'utility', 'touch', array(
      'messages' => array($this->view->message),
      'parentRedirect' => $photosUrl,
    ));


  }
}