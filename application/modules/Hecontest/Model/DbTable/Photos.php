<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Events.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 25.09.13
 * Time: 11:16
 * To change this template use File | Settings | File Templates.
 */
class Hecontest_Model_DbTable_Photos extends Engine_Db_Table
{
    protected $_rowClass = "Hecontest_Model_Photo";

    public function getPhoto($photo_id) {
        $select = $this->select()->where('photo_id=?', $photo_id);
        $photo = $this->fetchRow($select);
        return $photo;
    }

    public function addPhoto($photo, $parent_id)
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
            throw new User_Model_Exception('invalid argument passed to addPhoto');
        }


        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'hecontest',
            'parent_id' => $parent_id,
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);
        $w = $image->getWidth();
        $h = $image->getHeight();
        $ratio = (float) $w / $h;
        $newW = 612;
        $newH = (int) $newW / $ratio;

        $image->resize($newW, $newH)
            ->write($mainPath)
            ->destroy();

        // Resize image (normal)
        /*if($w > $h) {
            $newW = 240;
            $newH = $newW / $ratio;
        } else {
            $newH = 180;
            $newW = $newH * $ratio;
        }
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize($newW, $newH, false)
            ->write($normalPath)
            ->destroy();*/


        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);
        $defRatio = 4 / 3;
        $imgRatio = $image->width / $image->height;
        $size = array('w' => $image->width, 'h' => $image->height);
        $x = 0;
        $y = 0;
        if ($defRatio < $imgRatio) {
            $size['w'] = $image->height * $defRatio;
            $x = ($image->width - $size['w']) / 2;
        } else {
            $size['h'] = $image->width / $defRatio;
            $y = ($image->height - $size['h']) / 2;
        }
        $image->resample($x, $y, $size['w'], $size['h'], 240, 180)
            ->write($normalPath)
            ->destroy();



        $storage = Engine_Api::_()->storage();
        // Store
        try {
            $iMain = $storage->create($mainPath, $params);
            $iIconNormal = $storage->create($normalPath, $params);
            //$iMain = $filesTable->createFile($mainPath, $params);
            //$iIconNormal = $filesTable->createFile($normalPath, $params);

            $iMain->bridge($iIconNormal, 'thumb.normal');
        } catch (Exception $e) {
            // Remove temp files
            @unlink($mainPath);
            @unlink($normalPath);
            // Throw
            if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                throw new Album_Model_Exception($e->getMessage(), $e->getCode());
            } else {
                throw $e;
            }
        }

        // Remove temp files
        @unlink($mainPath);
        @unlink($normalPath);

        // Delete the old file?
        if (!empty($tmpRow)) {
            $tmpRow->delete();
        }

        return $iMain->file_id;

        /*$name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $params = array(
            'parent_id' => $parent_id,
            'extension' => $extension,
            'parent_type' => 'hecontest_photo'
        );
        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(1092, 301)
            ->write($path . '/m_' . $name)
            ->destroy();

        // Resize image (2xtile)
        $image = Engine_Image::factory();
        $image->open($file);
        $defRatio = 4 / 3;
        $imgRatio = $image->width / $image->height;
        $size = array('w' => $image->width, 'h' => $image->height);
        $x = 0;
        $y = 0;
        if ($defRatio < $imgRatio) {
            $size['w'] = $image->height * $defRatio;
            $x = ($image->width - $size['w']) / 2;
        } else {
            $size['h'] = $image->width / $defRatio;
            $y = ($image->height - $size['h']) / 2;
        }
        $image->resample($x, $y, $size['w'], $size['h'], 480, 360)
            ->write($path . '/p_' . $name)
            ->destroy();

        // Resize image (tile)
        $image = Engine_Image::factory();
        $image->open($file);

        $image->resample($x, $y, $size['w'], $size['h'], 240, 180)
            ->write($path . '/in_' . $name)
            ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);
        $image->width / $image->height;
        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
            ->write($path . '/is_' . $name)
            ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        //    $iMain->bridge($iProfile, 'thumb.2xtile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        //    $iMain->bridge($iIconNormal, 'thumb.tile');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        // Update row

        return $this;*/
    }

}
