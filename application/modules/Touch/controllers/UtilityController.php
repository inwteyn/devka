<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: UtilityController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_UtilityController extends Touch_Controller_Action_Standard
{

	public function init()
	{

    if($this->_getParam('format')== 'json' || $this->is_iPhoneUploading())
      return;
	  // Smoothbox Operation
    $this->view->smoothboxClose = $this->_getParam('smoothboxClose',  false);
		$this->view->smoothboxCloseTime = $this->_getParam('smoothboxCloseTime');
    $this->view->redirect = $this->_getParam('redirect', false);
    $this->view->redirectTime = $this->_getParam('redirectTime');

		//Ajax Operation
    $this->view->parentRefresh = $this->_getParam('parentRefresh',  false);
		$this->view->parentRefreshTime = $this->_getParam('parentRefreshTime');
    $this->view->parentRedirect = $this->_getParam('parentRedirect',  false);
    $this->view->parentRedirectTime = $this->_getParam('parentRedirectTime');


		//location Operation
		$this->view->locationHref = $this->_getParam('locationHref', false);
		$this->view->locationHrefTime = $this->_getParam('locationHrefTime');
		$this->view->locationReload = $this->_getParam('locationReload', false);
		$this->view->locationReloadTime = $this->_getParam('locationReloadTime');
	}

  public function successAction()
  {
    // Get messages
    $messages = array();
    $messages = array_merge($messages, (array) $this->_getParam('messages', null));

    // Default message "success"
    if( empty($messages) )
    {
      $messages[] = Zend_Registry::get('Zend_Translate')->_('Success');
    }

		//Assign
		$this->view->messages = $messages;
		$this->view->status = ($this->_getParam('status', true))? 1:0;
  }

	public function simpleAction()
	{

	}

  public function uploadprofilephotoAction(){
    if($this->is_iPhoneUploading()){
      if (isset($_FILES['picup-image-upload']['name'])) {
        $user = Engine_Api::_()->getItem('user', $this->_getParam('owner_id', null));
        $this->view->user_id = $user->getIdentity();
        $this->view->photo_name = $_FILES['picup-image-upload']['name'];
        $photo = $this->uploadPhoto($_FILES['picup-image-upload'], $user);
        $photo_id = $user->photo_id;
        $this->view->profile_photo = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, 'thumb.profile')->map();
        $this->view->icon_photo = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, 'thumb.icon')->map();
      }
      return;
    }
    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;

  }

  private function onUserProfilePhotoUpload($viewer, $file){
    if(!Engine_Api::_()->touch()->isModuleEnabled('album'))
      return false;
    // Get album
    $table = Engine_Api::_()->getDbtable('albums', 'album');
    $album = $table->getSpecialAlbum($viewer, 'profile');

    $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
    $photo = $photoTable->createRow();
    $photo->setFromArray(array(
      'owner_type' => 'user',
      'owner_id' => $viewer->getIdentity()
    ));
    $photo->save();
    $photo->setPhoto($file);

    $photo->album_id = $album->album_id;
    $photo->save();

    if( !$album->photo_id ) {
      $album->photo_id = $photo->getIdentity();
      $album->save();
    }

    $auth = Engine_Api::_()->authorization()->context;
    $auth->setAllowed($photo, 'everyone', 'view',    true);
    $auth->setAllowed($photo, 'everyone', 'comment', true);
    $auth->setAllowed($album, 'everyone', 'view',    true);
    $auth->setAllowed($album, 'everyone', 'comment', true);

    return $photo;
  }

  protected function uploadPhoto($fileElement, $user, $form = null){
    $db = $user->getTable()->getAdapter();
    $db->beginTransaction();
    try {

      $user->setPhoto($fileElement);

      $iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);

      // Insert activity
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update',
        '{item:$subject} added a new profile photo.', null, array('is_mobile' => true));

      if( $action ) {
        $attachment = $this->onUserProfilePhotoUpload($user,$iMain);
        if( !$attachment ) $attachment = $iMain;

        // We have to attach the user himself w/o album plugin
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
      }

      $db->commit();
      return $attachment;
    }

    // If an exception occurred within the image adapter, it's probably an invalid image
    catch( Engine_Image_Adapter_Exception $e )
    {
      $db->rollBack();
      if($form)
        $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
    }

    // Otherwise it's probably a problem with the database or the storage system (just throw it)
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

  }

  public function refreshCaptchaAction()
  {
    $this->view->start = true;
    $this->view->json = false;
    $this->view->hascaptcha = false;
    if($this->_getParam('format') != 'json')
      return;
    $class_name = $this->_getParam('class_name', 'Touch_Form_Signup_Account');

    $this->view->json = true;
    $this->view->class_name = $class_name;
    $this->view->class_exists = class_exists($class_name);
    $form = new $class_name();

    $captcha = $form->getElement('captcha')->getCaptcha();
    if(!$captcha)
      return;
    $this->view->hascaptcha = true;
    $this->view->id  = $captcha->generate();
    $this->view->src = $captcha->getImgUrl() .
                   $captcha->getId() .
                   $captcha->getSuffix();
  }
  public function loadJsAction(){
    $src = $this->_getParam('src');
    $useragent = $this->_getParam('useragent');

    $curl_handle = curl_init($src);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl_handle, CURLOPT_USERAGENT, $useragent);
    $content = curl_exec($curl_handle);
    curl_close($curl_handle);
    $this->view->script = $content;
  }
}