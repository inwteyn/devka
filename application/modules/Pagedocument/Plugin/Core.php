<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Plugin_Core
{
  public function removePage($event)
  {
    /*$payload = $event->getPayload();
    $page = $payload['page'];
    
    $table = Engine_Api::_()->getItemTable('pagedocument');
    $select = $table->select()->where('page_id = ?', $page->getIdentity());
    $documents = $table->fetchAll($select);
    
    foreach ($documents as $document) {
      $document->delete();
    }*/
  }

  public function onUserDeleteBefore($event)
  {
    /*$payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {
      $table = Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');
      $select = $table->select()->where('user_id = ?', $payload->getIdentity());
      foreach ($table->fetchAll($select) as $document ){
        $document->delete();
      }
    }*/
  }
}