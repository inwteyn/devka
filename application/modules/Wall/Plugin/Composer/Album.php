<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Album.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Composer_Album extends Core_Plugin_Abstract
{
  public function onAttachPhoto($data)
  {

    if( !is_array($data) || empty($data['photo_id']) ) {
      return;
    }
    $photo = array();
    $ids = explode(',',$data['photo_id']);
    if($ids){
      $i = 0;
      foreach($ids as $id){
        $photo[$i] = Engine_Api::_()->getItem('album_photo', $id);
        $i++;
      }
    }




    if( count($photo)<=0 )
    {
      return;
    }

    return $photo;
  }
}