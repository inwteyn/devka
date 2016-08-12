<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminIndexController.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Avatarstyler_AdminIndexController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
//      get settings
    $settings = Engine_Api::_()->getDbtable('settings', 'core');
    $this->view->form = $form = new Avatarstyler_Form_Admin_Settings();
      //      get photos from ids
//      $photoStrIds = $settings->fetchRow($settings->select()->where('name=?','avatarstyler.photo.ids'));
//
//      $photoIDs = explode(",",$photoStrIds->value);
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
//     for($i=0;$i<=$count;$i++){
//    array_push($a,'avatar'.$i);}
//      $form->addDisplayGroup($a,'avatars',array('legend' => 'Available Avatars'));
//      $avatars = $form->getDisplayGroup('avatars');
//      $avatars->setDecorators(array(
//          'FormElements',
//          'Fieldset',
//          array('HtmlTag',array('tag'=>'div','style'=>'width:100%;display:inline-block'))
//      ));

      $photoStrIds = $settings->fetchRow($settings->select()->where('name=?','avatarstyler.photo.ids'));

      $photoIDs = explode(",",$photoStrIds->value);

      $this->view->photoIDs = $photoIDs;

      if (!$this->getRequest()->isPost()) {
          return;
      }
      if (!$form->isValid($this->getRequest()->getParams())) {
          return;
      }
      $params = $form->getValues();
      $settings->__set('avatarstyler.usage', $params['usage']);
      if ($form->Filedata->getValue() !== null) {
          $file = $this->setPhoto($form->Filedata);
          if ($file) {
              $photoStrIds->value = $photoStrIds->value.','.$file->file_id;
              $photoStrIds->save();
              $form->addNotice('Avatarstyler_Settings have been successfully saved');
              //      redirect to index page
              $this->redirect(
                  Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                      'module' => 'avatarstyler', 'controller' => 'index'
                  ), 'admin_default', 1)
              );

          }
      }



  }

  public function removePhotoAction()
  {

//      get id of removing photo
      $id = $this->_getParam('id');
//      remove this photo from settings
      $settings = Engine_Api::_()->getDbtable('settings', 'core');
      $photoStrIds = $settings->fetchRow($settings->select()->where('name=?','avatarstyler.photo.ids'));


      $photoStrIds->value = str_replace($id.',','',$photoStrIds->value);

      $photoStrIds->save();

//      Delete photo from Storage
      $tableS = Engine_Api::_()->getDbTable('files', 'storage');
      $rowS = $tableS->fetchRow($tableS->select()->where('file_id', $id));
      $rowS->delete();
//      redirect to index page
      $this->redirect(
      Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
        'module' => 'avatarstyler', 'controller' => 'index'
      ), 'admin_default', 1)
    );
  }

  public function setPhoto($photo)
  {
      $settings = Engine_Api::_()->getDbtable('settings', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if ($photo instanceof Storage_Model_File) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if (!$fileName) {
      $fileName = $file;
    }
    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';


    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();

    // Resize image (profile)
    $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($profilePath)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($normalPath)
      ->destroy();

    // Resize image (icon)
    $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($squarePath)
      ->destroy();

      $params = array(
          'parent_type' => $viewer->getType(),
          'parent_id' => $viewer->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'name' => basename($fileName),
      );
    // Store
    $iMain = $filesTable->createFile($mainPath, $params);
       // INSERT the new row to the database
    $iProfile = $filesTable->createFile($profilePath, $params);
    $iIconNormal = $filesTable->createFile($normalPath, $params);
    $iSquare = $filesTable->createFile($squarePath, $params);
    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($mainPath);
    @unlink($profilePath);
    @unlink($normalPath);
    @unlink($squarePath);
    return $iMain;
  }

}

