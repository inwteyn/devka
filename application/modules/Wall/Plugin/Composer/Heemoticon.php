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


class Wall_Plugin_Composer_Heemoticon extends Core_Plugin_Abstract
{
  public function onAttachHeemoticon_post($data)
  {
    if( !is_array($data) || empty($data['emoticon_id']) ) {
      return;
    }

    $emotable = Engine_Api::_()->getDbTable('useds','heemoticon');
    $select  = $emotable->select()->where('sticker_id = ?',$data['emoticon_id']);

    $rew = $emotable->fetchRow($select);
    if(!$rew->photo_id) return;
    $storage = Engine_Api::_()->getItemTable('storage_file');
    $emoticon = $storage->getFile($rew->photo_id);

    if(  !$emoticon->getIdentity() )
    {
      return;
    }

    return $emoticon;
  }
}