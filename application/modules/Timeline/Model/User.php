<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: User.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Model_User extends User_Model_User
{
    protected $_type = 'user';

    protected $_photo_types = array('cover', 'born');

    public function isPhotoTypeSupported($type)
    {
        return in_array($type, $this->_photo_types);
    }

    public function setTimelinePhoto($photo = null)
    {
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
            throw new User_Model_Exception('invalid argument passed to setTimelinePhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        // @TODO user_id - is null correct?
        $params = array(
            'parent_type' => 'user',
            'parent_id' => $this->getIdentity(),
            'user_id' => 0,
            'name' => basename($fileName),
        );

        /**
         * Save
         * @var $filesTable Storage_Model_DbTable_Files
         */
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
//      ->resize(850, 315)
            ->write($mainPath)
            ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        // Store
        $iMain = $filesTable->createFile($mainPath, $params);

        // Remove temp files
        @unlink($mainPath);

        return $iMain;
    }

//    public function setTimelinePhoto($photo, $type = 'cover')
//    {
//        if (!$this->isPhotoTypeSupported($type)) {
//            throw new User_Model_Exception('The photo type "' . $type . '" is not supported in setTimelinePhoto');
//        }
//
//        if ($photo instanceof Zend_Form_Element_File) {
//            $file = $photo->getFileName();
//            $fileName = $file;
//        } else if ($photo instanceof Storage_Model_File) {
//            $file = $photo->temporary();
//            $fileName = $photo->name;
//        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
//            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
//            $file = $tmpRow->temporary();
//            $fileName = $tmpRow->name;
//        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
//            $file = $photo['tmp_name'];
//            $fileName = $photo['name'];
//        } else if (is_string($photo) && file_exists($photo)) {
//            $file = $photo;
//            $fileName = $photo;
//        } else {
//            throw new User_Model_Exception('invalid argument passed to setTimelinePhoto');
//        }
//
//        if (!$fileName) {
//            $fileName = $file;
//        }
//
//        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
//        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
//        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
//        $params = array(
//            'parent_type' => $this->getType(),
//            'parent_id' => $this->getIdentity(),
//            'user_id' => $this->getIdentity(),
//            'name' => basename($fileName),
//        );
//
//        /**
//         * Save
//         *
//         * @var $filesTable Storage_Model_DbTable_Files
//         */
//        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
//
//        // Resize image (main)
//        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
//        $image = Engine_Image::factory();
//        $image->open($file)
////      ->resize(850, 315)
//            ->write($mainPath)
//            ->destroy();
//
//        // Resize image (icon)
//        $image = Engine_Image::factory();
//        $image->open($file);
//
//        // Store
//        $iMain = $filesTable->createFile($mainPath, $params);
//
//        // Remove temp files
//        @unlink($mainPath);
//
//        // Update row
//        $this->modified_date = date('Y-m-d H:i:s');
//
//        $row_name = $type . '_id';
//        $this->$row_name = $iMain->file_id;
//        $this->save();
//        if ($type == 'cover') {
//            /**
//             * Save mini cover
//             *
//             * @var $filesTable Storage_Model_DbTable_Files
//             */
//            $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
//
//            // Resize image (main)
//            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
//            $image = Engine_Image::factory();
//            $image->open($file)
//                ->resize(350, 350)
//                ->write($mainPath)
//                ->destroy();
//
//            // Resize image (icon)
//            $image = Engine_Image::factory();
//            $image->open($file);
//
//            // Store
//            $iMain = $filesTable->createFile($mainPath, $params);
//
//            // Remove temp files
//            @unlink($mainPath);
//
//            // Update row
//            $this->modified_date = date('Y-m-d H:i:s');
//
//            $this->mini_cover_id = $iMain->file_id;
//            $this->save();
//        }
//
//
//        return $this;
//    }

    public function getTimelinePhoto($type = 'cover', $alt = "", $attribs = array())
    {
        $coverPhoto = Engine_Api::_()->timeline()->getTimelinePhoto($this->getIdentity(), 'user', 'cover');
        $position = $coverPhoto['position'];
        try {
            $position = json_decode($position);
            $position = array(
                'left' => $position->left,
                'top' => $position->top
            );
        } catch (Exception $e) {
            $position = array(
                'left' => 0,
                'top' => 0
            );
        }

        $attribs['style'] = 'top:' . $position['top'] . 'px;left:' . $position['left'] . 'px;';

        // User image
        $attribs = array_merge(array('id' => $type . '-photo'), $attribs);

        if ($coverPhoto['photoSrc']) {
            return Zend_Registry::get('Zend_View')->htmlImage($coverPhoto['photoSrc'], $alt, $attribs);
        }

        return '';
    }

    public function hasTimelinePhoto($type = 'cover')
    {
        $row_name = $type . '_id';
        return (boolean)$this->$row_name;
    }

    public function getBirthdate()
    {
        $profileTypeId = $this->getProfileType();
        $birthdayFieldId = $this->getBirthdayFieldId($profileTypeId);

        $fieldsValuesTable = new Fields_Model_DbTable_Values("user", "values");
        $myBirthdaySql = $fieldsValuesTable->select()->where("field_id = ?", $birthdayFieldId)->where("item_id = ?", $this->getIdentity());
        $myBirthdayRow = $fieldsValuesTable->fetchAll($myBirthdaySql)->toArray();
        if($myBirthdayRow) {
            return $myBirthdayRow[0]['value'];
        }

        return false;
    }

    public function getProfileType()
    {
      $fieldsSearchTable = new Fields_Model_DbTable_Search("user", "search");
      $profileTypeSql = $fieldsSearchTable->select()->where("item_id = ?", $this->getIdentity());
      $profileTypeRow = $fieldsSearchTable->fetchRow($profileTypeSql);

      if(!$profileTypeRow || !is_object($profileTypeRow))
        return "";
      $profileType = $profileTypeRow->toArray();
      return $profileType['profile_type'];
    }


  public function getBirthdayFieldId($profileTypeId = 0)
    {
        if (!$profileTypeId)
            return false;
        $fieldsMapsTable = new Fields_Model_DbTable_Meta("user", "maps");
        $fieldsMetaTable = new Fields_Model_DbTable_Meta("user", "meta");

        $select = $fieldsMetaTable->select()
            ->setIntegrityCheck(false)
            ->from(array("meta" => $fieldsMetaTable->info('name')), array("meta.field_id"))
            ->where("type = ?", "birthdate")
            ->joinLeft(array("maps" => $fieldsMapsTable->info('name')), "meta.field_id = maps.child_id", array())
            ->where("option_id = ?", $profileTypeId);

        $result = $fieldsMetaTable->fetchAll($select)->toArray();

        if (!empty($result)) {
            return $result[0]['field_id'];
        }
        return false;
    }

    public function setBirthdate($date = null)
    {
      if (!$date) {
        $date = date("Y-n-j", null);
      }

      $optionId = $this->getProfileType();

      $birthdayFieldId = $this->getBirthdayFieldId($optionId);

      $fieldsValuesTable = new Fields_Model_DbTable_Values("user", "values");
      $myBirthdaySql = $fieldsValuesTable->select()->where("field_id = ?", $birthdayFieldId)->where("item_id = ?", $this->getIdentity());
      $myBirthdayRows = $fieldsValuesTable->fetchAll($myBirthdaySql);

      if (!count($myBirthdayRows)) {
        $fieldsValuesTable->getAdapter()->beginTransaction();
        try {
          $fieldsValuesTable->insert(array(
            'item_id' => $this->getIdentity(),
            'field_id' => $birthdayFieldId,
            'index' => 0,
            'value' => $date
          ));
          $fieldsValuesTable->getAdapter()->commit();
        } catch (Exception $e) {
          $fieldsValuesTable->getAdapter()->rollBack();
        }
        return true;
      } else if (empty($myBirthdayRows[0]->value)) {
        $fieldsValuesTable->update(
          array('value' => $date),
          array('item_id = ?' => $myBirthdayRows[0]->item_id,
            'field_id = ?' => $myBirthdayRows[0]->field_id)
        );
      }
      return false;
    }

    public function getTimelineAlbumPhoto($type = 'cover')
    {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return null;
        }

        /**
         * @var $table Timeline_Model_DbTable_Settings
         */
        $table = Engine_Api::_()->getDbTable('settings', 'hecore');
        $photo_id = $table->getSetting($this, 'timeline-' . $type . '-photo-id');

        if ($photo_id == null) return null;

        return Engine_Api::_()->getItem('album_photo', $photo_id);
    }
}
