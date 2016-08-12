<?php
/** 
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: IndexController.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Avatarstyler_IndexController extends Core_Controller_Action_Standard
{
  private $formObject ;
  private $img_id ;
  public function init()
  {
    $this->view->viewer = $this->viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    if ($settings->getSetting('avatarstyler.usage', 'allow') != 'allow') {
      return $this->_helper->redirector->gotoRoute(
        array('id' => $this->viewer->getIdentity()),
        'user_profile', 1
      );
    }
      $this->formObject = new Avatarstyler_Form_Photo();
      $this->img_id =  $this->_getParam('imgId',0);
  }

  public function indexAction()
  {


      $settings = Engine_Api::_()->getDbtable('settings', 'core');
    if (!$this->viewer || !$this->viewer->getIdentity()) {
      return $this->_helper->redirector->gotoRoute(
        array(), 'default', 1
      );
    }

    $this->view->form = $form = $this->formObject;

    $storageFiles = Engine_Api::_()->getDbTable('files', 'storage');
    $file = $storageFiles->getFile($this->viewer->photo_id);

    if ($file) {
      $form->current->setImage($file->storage_path);

    } else {
      $form->current->setImage('application/modules/User/externals/images/nophoto_user_thumb_profile.png');
      $form->preview->setImage($this->getPreview());
    }

//***************************************************************************************************************************//
      //      get photos from ids
      $photoStrIds = $settings->fetchRow($settings->select()->where('name=?','avatarstyler.photo.ids'));

      $photoIDs = explode(",",$photoStrIds->value);

           $this->view->photoIDs = $photoIDs;
//      $count = 0;
//      foreach($photoIDs as $photoID){
//          $avatar = $form->createElement('image', 'avatar'.$count,array(
//              'ignore' => false,
//              'style'=>'max-width:200px;cursor:default'));
//          $form->addElement($avatar);
//          $name ='avatar'.$count;
//          $form->$name->src = Engine_Api::_()->avatarstyler()->getLayer($photoID);
//          $form->$name->id = $photoID;
//          $count++;
//      }
//      $a = array();
//      for($i=0;$i<=$count;$i++){
//          array_push($a,'avatar'.$i);}
//      $form->addDisplayGroup($a,'avatars',array('legend' => 'Available Avatars'));
//      $avatars = $form->getDisplayGroup('avatars');
//      $avatars->setDecorators(array(
//          'FormElements',
//          'Fieldset',
//          array('HtmlTag',array('tag'=>'div','style'=>'width:100%;display:inline-block'))
//      ));

//**************************************************************************************************************************//

  }

  public function showpreviewAction(){
       $form = $this->formObject;

        die($this->getPreview());
    }

  public function updateAction(){
      if ($this->getRequest()->isPost()) {
          $file = $this->updateAvatar($this->viewer);

          // Insert activity
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($this->viewer, $this->viewer, 'profile_photo_update',
              '{item:$subject} added a new profile photo.');

          // Hooks to enable albums to work
          if ($action) {
              $event = Engine_Hooks_Dispatcher::_()
                  ->callEvent('onUserProfilePhotoUpload', array(
                      'user' => $this->viewer,
                      'file' => $file,
                  ));


              $attachment = $event->getResponse();
              if (!$attachment) $attachment = $file;

              // We have to attach the user himself w/o album plugin
              Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
          }
          return $this->_helper->redirector->gotoRoute(
              array('id' => $this->viewer->getIdentity()),
              'user_profile', 1
          );
      }
  }

  public function getPreviewName()
  {
    $tmpPath = 'public/temporary';
    $tmpName = 'avatarstyler_tmp_file_' . $this->viewer->getIdentity() . '.png';
    $tmpFile = $tmpPath . '/' . $tmpName;
    return $tmpFile;
  }

  public function getPreview()
  {

//      first we should remove preview to get new
      $this->removePreview();
      $storageFiles = Engine_Api::_()->getDbTable('files', 'storage');
      $file = $storageFiles->getFile($this->viewer->photo_id);
      $tmpFile = $this->getPreviewName();

    if (!$file) {
        $dstFile = $this->getAvatarLayer();
    } else {
        $dstFile = $this->imageCreate($file);
    }
    $srcFile = $this->getAvatarLayer();

    if (!$srcFile || !$dstFile) {
      return null;
    }

    $tmpImg = imagecreatetruecolor(imagesx($dstFile), imagesy($dstFile));
    imagecopyresized($tmpImg, $srcFile, 0, 0, 0, 0, imagesx($dstFile), imagesy($dstFile), imagesx($srcFile), imagesy($srcFile));
    imagecopymerge($dstFile, $tmpImg, 0, 0, 0, 0, imagesx($dstFile), imagesy($dstFile), 50);
    imagepng($dstFile, $tmpFile);
    imagedestroy($srcFile);
    imagedestroy($dstFile);
    return $tmpFile;
  }

  public function removePreview()
  {
    $file = $this->getPreviewName();

    if (file_exists($file)) {
      try {
        unlink($file);
      } catch (Exception $e) {
      }
    }
  }

  public function updateAvatar($user)
  {
    $storageFiles = Engine_Api::_()->getDbTable('files', 'storage');
    $file = $storageFiles->getFile($user->photo_id);

    if ($file) {
      $fileName = $file->storage_path;
      $extension = ltrim(strrchr(basename($fileName), '.'), '.');
      $empty = false;
    } else {
      $fileName = $this->getPreviewName();
      $extension = 'png';
      $empty = true;
    }

    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $user->getType(),
      'parent_id' => $user->getIdentity(),
      'user_id' => $user->getIdentity(),
      'name' => basename($fileName),
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($fileName)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();

    // Resize image (profile)
    $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
    $image = Engine_Image::factory();
    $image->open($fileName)
      ->resize(200, 400)
      ->write($profilePath)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($fileName)
      ->resize(140, 160)
      ->write($normalPath)
      ->destroy();

    // Resize image (icon)
    $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
    $image = Engine_Image::factory();
    $image->open($fileName);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($squarePath)
      ->destroy();

    // Store
    $iMain = $filesTable->createFile($mainPath, $params);
    $iProfile = $filesTable->createFile($profilePath, $params);
    $iIconNormal = $filesTable->createFile($normalPath, $params);
    $iSquare = $filesTable->createFile($squarePath, $params);

    if (!$empty) {
      $iMain = $this->addLayer($iMain);
      $iProfile = $this->addLayer($iProfile);
      $iIconNormal = $this->addLayer($iIconNormal);
      $iSquare = $this->addLayer($iSquare);
    }

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($mainPath);
    @unlink($profilePath);
    @unlink($normalPath);
    @unlink($squarePath);

    // Update row
    $user->modified_date = date('Y-m-d H:i:s');
    $user->photo_id = $iMain->file_id;
    $user->save();

    return $iMain;
  }

  public function changeExt($fileName)
  {
    $fileName = explode('.', $fileName);
    return $fileName[0] . '.png';
  }

  public function addLayer($file)
  {
    $srcFile = $this->getAvatarLayer();
    $dstFile = $this->imageCreate($file);

    if (!$srcFile || !$dstFile) {
      return null;
    }

    try {
		$tmpImg = imagecreatetruecolor(imagesx($dstFile), imagesy($dstFile));
		imagecopyresized($tmpImg, $srcFile, 0, 0, 0, 0, imagesx($dstFile), imagesy($dstFile), imagesx($srcFile), imagesy($srcFile));

      //imagecopyresized($srcFile, $srcFile, 0, 0, 0, 0, imagesx($dstFile), imagesy($dstFile), imagesx($srcFile), imagesy($srcFile));

      $newFileName = $this->changeExt($file->storage_path);

      imagecopymerge($dstFile, $tmpImg, 0, 0, 0, 0, imagesx($dstFile), imagesy($dstFile), 50);
      imagepng($dstFile, $newFileName);
      imagedestroy($dstFile);
      imagedestroy($srcFile);
	  imagedestroy($tmpImg);

      $file->storage_path = $newFileName;
      $file->mime_major = 'png';
      $file->extension = 'png';
      $file->save();

      return $file;
    } catch (Exception $e) {
      return null;
    }
  }

  public function imageCreate($file, $ext = null)
  {
    if ($ext) {
      $path = $file;
    } else {
      $ext = mb_strtolower($file->extension);
      $path = $file->storage_path;
    }


    switch ($ext) {
      case 'png':
        return imagecreatefrompng($path);
        break;
      case 'gif':
        return imagecreatefromgif($path);
        break;
      case 'bmp':
        return imagecreatefrombmp($path);
        break;
      case 'jpg':

        return imagecreatefromjpeg($path);
        break;
      case 'jpeg':
        return imagecreatefromjpeg($path);
        break;
      default:
        return false;
    }
  }

  public function getAvatarLayer()
  {

//      if(gettype($id) == 'string'){
//          $id = intval($id);
//      }
    $storageFiles = Engine_Api::_()->getDbTable('files', 'storage');
      if(!$this->img_id){
          return;
      }
    $file = $storageFiles->getFile($this->img_id);
    if ($file) {
      return $this->imageCreate($file);
    } else {
      return $this->imageCreate(Engine_Api::_()->avatarstyler()->getLayer(), 'png');
    }
  }
}

