<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Plugin_Core
{
  public function removePage($event)
  {
	  $payload = $event->getPayload();
	  $page = $payload['page'];
	  
	  $table = Engine_Api::_()->getItemTable('pagevideo');
	  $select = $table->select()->where('page_id = ?', $page->getIdentity());
	  $videos = $table->fetchAll($select);
	  
	  foreach ($videos as $video){
	  	$video->delete();
	  }
  }
    
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if ( $payload instanceof User_Model_User ){
      $table = Engine_Api::_()->getDbTable('pagevideos', 'pagevideo');
      $select = $table->select()->where('user_id = ?', $payload->getIdentity());
      foreach ( $table->fetchAll($select) as $video ) {
        $video->delete();
      }
    }
  }
}