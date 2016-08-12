<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photo.php 2012-06-27 10:50 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_Model_Photo extends Core_Model_Item_Abstract
{
    protected $_searchColumns = array('title', 'description');

    protected $_type = 'photo';

    protected $_owner_type = 'user';

    protected $_parent_type = 'offers';

    protected $_collection_type = "offers";

    public function setPhoto($photo)
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
            throw new Classified_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = basename($file);
        }

        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $viewer = ENgine_Api::_()->user()->getViewer();

        $params = array(
            'parent_type' => $this->getType(),
            'parent_id' => $this->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getItemTable('storage_file');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(720, 720)
            ->write($mainPath)
            ->destroy();

        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(210, 240)
            ->write($normalPath)
            ->destroy();

        // Store
        $iMain = $filesTable->createFile($mainPath, $params);
        $iIconNormal = $filesTable->createFile($normalPath, $params);

        $iMain->bridge($iIconNormal, 'thumb.normal');

        // Remove temp files
        @unlink($mainPath);
        @unlink($normalPath);

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->file_id = $iMain->file_id;
        $this->save();

        return $this;
    }

    public function getHref()
    {
        return "javascript://";
    }
}