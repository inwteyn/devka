<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: NotificationsController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Activity_NotificationsController extends Touch_Controller_Action_Standard
{

  public function init()
  {
    $this->_helper->requireUser();
  }

  public function indexAction()
  {
		// Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')->getNavigation('activity_main');

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->notifications = $notifications = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationsPaginator($viewer);
		$notifications->setItemCountPerPage(5);
    $notifications->setCurrentPageNumber($this->_getParam('page', 1));
  }

	public function requestsAction()
  {
		// Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')->getNavigation('activity_main');
		$viewer = Engine_Api::_()->user()->getViewer();
    $this->view->requests = $requests = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer);
		$requests->setItemCountPerPage(2);
    $requests->setCurrentPageNumber($this->_getParam('page', 1));
	}


  public function markreadAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $action_id = $request->getParam('actionid', 0);

    $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $db = $notificationsTable->getAdapter();
    $db->beginTransaction();

    try {
      $notification = Engine_Api::_()->getItem('activity_notification', $action_id);
      $notification->read = 1;
      $notification->save();
      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    if ($this->_helper->contextSwitch->getCurrentContext()  != 'json') {
      $this->_helper->viewRenderer->setNoRender();
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
      $this->view->notificationCount = (int) Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
     }

		return;
  }


  public function updateAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      $this->view->notificationOnly = $request->getParam('notificationOnly', false);
      if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('advmenusystem')){
        $notificationTb = Engine_Api::_()->getDbtable('notifications', 'activity');
        $tbName = $notificationTb->info('name');
        $db = $notificationTb->getAdapter();
        try{
          $notificationCount = $db->query("SELECT COUNT(*) AS total FROM $tbName WHERE `user_id` = {$viewer->getIdentity()} AND `read` = 0 AND `type` <> 'friend_request' AND `type` <> 'message_new'")->fetch();
          $notificationCount = $notificationCount['total'];
          $this->view->notificationOnly = true;
        }catch(Exception $e){
          $notificationCount = (int) Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
        }
      } else
        $notificationCount = (int) Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);

      $this->view->notificationCount = $notificationCount;
    }

    $this->view->text = $notificationCount;
  }
}