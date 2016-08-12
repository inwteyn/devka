<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Creditbadge.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Model_Creditbadge extends Core_Model_Item_Abstract
{


  public function setPhoto($photo)
  {
    if ($photo instanceof Zend_Form_Element_File){
      $file = $photo->getFileName();
    } else if (is_array($photo) && !empty($photo['tmp_name'])){
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)){
      $file = $photo;
    } else {
      throw new Event_Model_Exception('invalid argument passed to setPhoto');
    }

    if ($this->photo_id){
      $this->removePhoto();
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_id' => $this->getIdentity(),
      'parent_type' => 'hebadge_creditbadge'
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
        ->resize(720, 720)
        ->write($path . '/m_' . $name)
        ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
        ->resize(128, 128)
        ->write($path . '/p_' . $name)
        ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 36, 36)
        ->write($path . '/is_' . $name)
        ->destroy();

    // Store
    $iMain = $storage->create($path . '/m_' . $name, $params);
    $iProfile = $storage->create($path . '/p_' . $name, $params);
    $iSquare = $storage->create($path . '/is_' . $name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($path . '/p_' . $name);
    @unlink($path . '/m_' . $name);
    @unlink($path . '/is_' . $name);

    $this->photo_id = $iMain->file_id;
    $this->save();

    return $this;
  }


  public function removePhoto()
  {
    try {
      // TODO
    } catch (Exception $e){

    }
    $this->photo_id = 0;
    $this->save();
  }


  public function setIcon($photo)
  {
    if ($photo instanceof Zend_Form_Element_File){
      $file = $photo->getFileName();
    } else if (is_array($photo) && !empty($photo['tmp_name'])){
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)){
      $file = $photo;
    } else {
      throw new Event_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_id' => $this->getIdentity(),
      'parent_type' => 'hebadge_creditbadge'
    );

    $storage = Engine_Api::_()->storage();

    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 36, 36)
        ->write($path . '/is_' . $name)
        ->destroy();

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id);

    if ($file){
      $iSquare = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.icon');
      $iSquare->remove();
      $iSquare = $storage->create($path . '/is_' . $name, $params);
      $file->bridge($iSquare, 'thumb.icon');
    }

    @unlink($path . '/is_' . $name);
  }


  public function removeIcon()
  {
    $file = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application/modules/Hebadge/externals/images/nophoto_badge_thumb_icon.png';
    $this->setIcon($file);
  }


  public function getPhotoUrl($type = null)
  {
    if (empty($this->photo_id)){
      return null;
    }

    $table = Engine_Api::_()->getItemTable('storage_file');

    $file = null;
    if ($type){
      $select = $table->select()
          ->where('parent_file_id = ?', $this->photo_id)
          ->where('type = ?', $type)
          ->limit(1);

      $file = $table->fetchRow($select);
    }

    if (null === $file){
      $file = Engine_Api::_()->getItem('storage_file', $this->photo_id);
    }

    if (!$file){
      return null;
    }

    return $file->map();
  }


  public function delete()
  {
    parent::delete();

    foreach (Engine_Api::_()->getDbTable('creditmembers', 'hebadge')->fetchAll(array('creditbadge_id = ?' => $this->getIdentity())) as $item){
      $item->delete();
    }
  }


  public function getHref($params = array())
  {
   $params = array_merge(array(
      'route' => 'hebadge_credit_profile',
      'reset' => true,
      'id' => $this->getIdentity(),
      'slug' => $this->getSlug()
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, $reset) ;
  }

  public function getMember(Core_Model_Item_Abstract $owner)
  {
    $table = Engine_Api::_()->getDbTable('creditmembers', 'hebadge');
    $select = $table->select()
        ->where('creditbadge_id = ?', $this->getIdentity())
        ->where('object_type = ?', $owner->getType())
        ->where('object_id = ?', $owner->getIdentity());

    return $table->fetchRow($select);
  }

  public function addMember($owner)
  {
    $membersTable = Engine_Api::_()->getDbTable('creditmembers', 'hebadge');
    $select = $membersTable->select()
        ->where('creditbadge_id = ?', $this->getIdentity())
        ->where('object_type = ?', $owner->getType())
        ->where('object_id = ?', $owner->getIdentity());

    $member = $membersTable->fetchRow($select);

    if (!$member){

      $member = $membersTable->createRow();

      $member->setFromArray(array(
        'creditbadge_id' => $this->getIdentity(),
        'object_type' => $owner->getType(),
        'object_id' => $owner->getIdentity(),
        'approved' => 1,
        'creation_date' => date('Y-m-d H:i:s')
      ));

      $member->save();

      if ($member->approved){
        $this->member_count++;
        $this->save();
      }
    }

  }

  public function removeMember(Core_Model_Item_Abstract $owner)
  {
    $member = $this->getMember($owner);
    if ($member){
      if ($member->approved){
        $this->member_count--;
        $this->save();
      }
      $member->delete();
    }
  }

  public function setApprovedMember(Core_Model_Item_Abstract $owner, $approved = 1)
  {
    $member = $this->getMember($owner);
    if ($member){
      $member->approved = $approved;
      $member->save();
      if ($member->approved){
        $this->member_count++;
        $this->save();
      }
    }
  }

  public function getParent($recurseType = null)
  {}

  public function getTitle() {
    $title = parent::getTitle();
    $view = Zend_Registry::get('Zend_View');
    return $view->translate($title);
  }
  public function getDescription() {
    $descr = parent::getDescription();
    $view = Zend_Registry::get('Zend_View');
    return $view->translate($descr);
  }

}