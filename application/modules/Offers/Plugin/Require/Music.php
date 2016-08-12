<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Music.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Offers_Plugin_Require_Music extends Offers_Plugin_Require_Abstract
{


  public function check(Core_Model_Item_Abstract $owner, $new_item_id = null)
  {
    $count = $this->getCount($owner, $new_item_id);

    foreach ($this->getRequire() as $require){
      if (empty($require->params) || empty($require->params['count'])){
        continue ;
      }
      if ( $count >= $require->params['count'] ){
        $require->complete($owner);
      }
    }

  }

  public function getCount(Core_Model_Item_Abstract $owner, $new_item_id = null)
  {
    $table = Engine_Api::_()->getDbTable('playlistSongs', 'music');
    $playlistTable = Engine_Api::_()->getDbTable('playlists', 'music');

    $select = $table->select()
        ->from(array('s' => $table->info('name')), new Zend_Db_Expr('COUNT(*)'))
        ->join(array('p' => $playlistTable->info('name')), 'p.playlist_id = s.playlist_id', array())
        ->where('p.owner_type  = ?', $owner->getType())
        ->where('p.owner_id = ?', $owner->getIdentity());

    if (!empty($new_item_id)){
      $select->where('s.song_id != ?', $new_item_id);
    }

    // print_log($select . '');

    $count = $table->getAdapter()->fetchOne($select);

    // print_log($count);

    if (!empty($new_item_id)){
      $count++;
    }

    $this->getInfo($owner)->setFromArray(array(
      'music' => $count
    ))->save();


    return $count;

  }





}