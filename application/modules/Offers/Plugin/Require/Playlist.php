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



class Offers_Plugin_Require_Playlist extends Offers_Plugin_Require_Abstract
{


  public function check(Core_Model_Item_Abstract $owner, $new_item_id = null, $page_id)
  {
    $count = $this->getCount($owner, $new_item_id, $page_id);

    foreach ($this->getRequire() as $require){
      if (empty($require->params) || empty($require->params['count'])){
        continue ;
      }

      if ( $count >= $require->params['count'] ){
        $require->complete($owner, $page_id, $new_item_id->getIdentity());
      }
    }

  }

  public function getCount(Core_Model_Item_Abstract $owner, $new_item_id = null, $page_id)
  {
    $table = Engine_Api::_()->getDbTable('songs', 'pagemusic');
    $playlistTable = Engine_Api::_()->getDbTable('playlists', 'pagemusic');

    $select = $table->select()
        ->from(array('s' => $table->info('name')), new Zend_Db_Expr('COUNT(*)'))
        ->join(array('p' => $playlistTable->info('name')), 'p.playlist_id = s.playlist_id', array())
        ->where('p.owner_type  = ?', $owner->getType())
        ->where('p.owner_id = ?', $owner->getIdentity())
        ->where('s.page_id = ?', $page_id);

    if (!empty($new_item_id)){
      $select->where('s.song_id != ?', $new_item_id->getIdentity());
    }

    $count = $table->getAdapter()->fetchOne($select);


    if (!empty($new_item_id)){
      $count++;
    }

    $this->getInfoPage($owner, $page_id)->setFromArray(array(
      'playlist' => $count
    ))->save();


    return $count;

  }





}