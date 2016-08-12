<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagealbum_Plugin_Core
{
  public function removePage($event)
  {
	  $payload = $event->getPayload();
	  $page = $payload['page'];
	  
	  $table = Engine_Api::_()->getItemTable('pagealbum');
	  $select = $table->select()->where('page_id = ?', $page->getIdentity());
	  $albums = $table->fetchAll($select);
	  
	  foreach ($albums as $album){
	  	$album->delete();
	  }
  }
    
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if ( $payload instanceof User_Model_User ){
      $table = Engine_Api::_()->getItemTable('pagealbum');
      $select = $table->select()->where('user_id = ?', $payload->getIdentity());
      foreach ( $table->fetchAll($select) as $album ) {
        $album->delete();
      }
    }
  }
}