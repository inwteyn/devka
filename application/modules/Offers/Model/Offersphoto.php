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

class Offers_Model_Offersphoto extends Core_Model_Item_Collectible
{
  protected $_type = 'offers';
  protected $_collection_type = "offer";

  public function getPhotoUrl($type = null)
  {
    $photo_id = $this->file_id;
    if( !$photo_id ) {
      return null;
    }

    $file = Engine_Api::_()->getApi('storage', 'storage')->get($photo_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }
}