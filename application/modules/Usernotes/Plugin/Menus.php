<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_Plugin_Menus
{
  public function onMenuInitialize_CoreMainUsernotes()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity() || !Engine_Api::_()->authorization()->isAllowed('usernotes', null, 'enabled'))
    {
      return false;
    }

    return true;
  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete notes
      $userNotesTbl = Engine_Api::_()->getDbTable('usernote', 'usernotes');

      $userNotesTbl->delete(array("user_id = {$payload->getIdentity()}"));
      $userNotesTbl->delete(array("owner_id = {$payload->getIdentity()}"));
    }
  }
}