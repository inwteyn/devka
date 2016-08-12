<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IndexController.php 9301 2011-09-21 21:34:34Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Chat_IndexController extends Core_Controller_Action_User
{
  public function indexAction()
  {
    // Get rooms
    $roomTable = Engine_Api::_()->getDbtable('rooms', 'chat');
    $select = $roomTable->select()
      ->where('public = ?', 1);

    $rooms = array();
    foreach( $roomTable->fetchAll($select) as $room ) {
      $rooms[$room->room_id] = $room->toRemoteArray();
    }
    $this->view->rooms = $rooms;
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->isOperator = $viewer->authorization()->isAllowed('admin');

    $this->view->canChat = $canChat = Engine_Api::_()->authorization()->isAllowed('chat', $viewer, 'chat');
    $this->view->canIM = $canIM = Engine_Api::_()->authorization()->isAllowed('chat', $viewer, 'im');

    $this->view->chatContainer = preg_replace('/[^a-z0-9]+/', '', $this->_getParam('tmpId'));
    
    if( !$this->_getParam('no-content') ) {
      // Render
      $this->_helper->content
          //->setNoRender()
          ->setEnabled()
          ;
    }
  }

  public function banAction()
  {
    
  }
}