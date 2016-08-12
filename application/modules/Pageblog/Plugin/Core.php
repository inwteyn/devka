<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageblog_Plugin_Core
{
  public function removePage($event)
  {
	  $payload = $event->getPayload();
	  $page = $payload['page'];
	  
	  $table = Engine_Api::_()->getItemTable('pageblog');
	  $select = $table->select()->where('page_id = ?', $page->getIdentity());
	  $blogs = $table->fetchAll($select);
	  
	  foreach ($blogs as $blog){
	  	$blog->delete();
	  }
  }
    
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if ( $payload instanceof User_Model_User ){
      $table = Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
      $select = $table->select()->where('user_id = ?', $payload->getIdentity());
      foreach ( $table->fetchAll($select) as $blog ) {
        $blog->delete();
      }
    }
  }
}