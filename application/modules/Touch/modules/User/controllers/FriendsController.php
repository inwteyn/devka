<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: FriendsController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class User_FriendsController extends Core_Controller_Action_User
{
  var $friends_enabled = false;

  public function init()
  {
    // Try to set subject
    $user_id = $this->_getParam('user_id', null);
    if( $user_id && !Engine_Api::_()->core()->hasSubject() )
    {
      $user = Engine_Api::_()->getItem('user', $user_id);
      if( $user )
      {
        Engine_Api::_()->core()->setSubject($user);
      }
    }


    $this->eligible = Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible;
  }
  
  public function addAction()
  {
		if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }
    
    $viewer = $this->_helper->api()->user()->getViewer();
    $user = $this->_helper->api()->user()->getUser($user_id);

    // check that user is not trying to befriend 'self'
    if( $viewer->isSelf($user) ){
      $this->_forward('success', 'utility', 'touch', array(
				'status'=>false,
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('You cannot befriend yourself.'))
      ));
    }

    // check that user is already friends with the member
    if( $viewer->membership()->isMember($user)){
      $this->_forward('success', 'utility', 'touch', array(
				'status'=>false,
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are already friends with this member.'))
      ));
    }

    // check that user has not blocked the member
    if( $viewer->isBlocked($user)){
      $this->_forward('success', 'utility', 'touch', array(
				'status'=>false,
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Friendship request was not sent because you blocked this member.'))
      ));
    }
    

    // Make form
    $this->view->form = $form = new User_Form_Friends_Add();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $user->membership()->addMember($viewer)->setUserApproved($viewer);


      // if one way friendship and verification not required
      if(!$user->membership()->isUserApprovalRequired()&&!$user->membership()->isReciprocal()){
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends_follow', '{item:$object} is now following {item:$subject}.', array('is_mobile' => true));

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $viewer, 'friend_follow');

        $message = Zend_Registry::get('Zend_Translate')->_("You are now following this member.");
      }

      // if two way friendship and verification not required
      else if(!$user->membership()->isUserApprovalRequired()&&$user->membership()->isReciprocal()){
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');
        $message = Zend_Registry::get('Zend_Translate')->_("You are now friends with this member.");
      }

      // if one way friendship and verification required
      else if(!$user->membership()->isReciprocal()){
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_request');
        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
      }

      // if two way friendship and verification required
      else if($user->membership()->isReciprocal())
      {
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_request');
        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
      }

      $db->commit();

			$this->view->status = true;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been sent.');

			$this->_forward('success', 'utility', 'touch', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' =>array($this->view->message),
			));
    }
    catch( Exception $e )
    {
      $db->rollBack();

			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
			$this->view->exception = $e->__toString();

			$this->_forward('success', 'utility', 'touch', array(
				'status' => $this->view->status,
				'messages' => array($this->view->error, $this->view->exception),
			));
    }
  }

  public function cancelAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }
    
    // Make form
    $this->view->form = $form = new User_Form_Friends_Cancel();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }
    
    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      $user->membership()->removeMember($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $user, $viewer, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      // Set the request as handled if it was a follow request
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $user, $viewer, 'friend_follow_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

			$this->view->status = true;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been cancelled.');

			$this->_forward('success', 'utility', 'touch', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' =>array($this->view->message),
			));
    }


    catch( Exception $e )
    {
      $db->rollBack();

			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
			$this->view->exception = $e->__toString();

			$this->_forward('success', 'utility', 'touch', array(
				'status' => $this->view->status,
				'messages' => array($this->view->error, $this->view->exception),
			));
    }
  }

  public function confirmAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Confirm();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }
    
    $viewer = $this->_helper->api()->user()->getViewer();
    $user = $this->_helper->api()->user()->getUser($user_id);

    /*$friendship = $viewer->membership()->getRow($user);
    if ($friendship->active == 1){
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already friends');
      return;
    }*/

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $user->membership()->setUserApproved($viewer);

      // Add activity
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));
      
      // Add notification
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      // Increment friends counter
      // @todo make sure this works fine for following
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.friendships');
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('touch.user.friendships');

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You are now friends with %s');
      $message = sprintf($message, $user->__toString());

      $this->view->status = true;
      $this->view->message = $message;

			$this->_forward('success', 'utility', 'touch', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' =>array($this->view->message),
			));
    }
    catch( Exception $e )
    {
      $db->rollBack();

			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
			$this->view->exception = $e->__toString();

			$this->_forward('success', 'utility', 'touch', array(
				'status' => $this->view->status,
				'messages' => array($this->view->error, $this->view->exception),
			));
    }
  }
  
  public function rejectAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Reject();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      
      $user->membership()->removeMember($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      $db->commit();
      $message = Zend_Registry::get('Zend_Translate')->_('You ignored a friend request from %s');
      $message = sprintf($message, $user->__toString());

      $this->view->status = true;
      $this->view->message = $message;
      $this->_forward('success', 'utility', 'touch', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(
          $this->view->message
          //'You have ignored this friend request.'
        )
      ));

    }
    catch( Exception $e )
    {
      $db->rollBack();

			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
			$this->view->exception = $e->__toString();

			$this->_forward('success', 'utility', 'touch', array(
				'status' => $this->view->status,
				'messages' => array($this->view->error, $this->view->exception),
			));
    }
  }
  
  public function removeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);

    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }


    // Make form
    $this->view->form = $form = new User_Form_Friends_Remove();
    if( !$this->getRequest()->isPost() )
      {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      
      $user->membership()->removeMember($viewer);
      $user->lists()->removeFriendFromLists($viewer);

      // Set the request as handled - this may not be neccesary here
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $user, $viewer, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      $db->commit();

      $this->view->status = true;
      $this->view->message = $message = Zend_Registry::get('Zend_Translate')->_('This person has been removed from your friends.');
      $this->_forward('success', 'utility', 'touch', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array($message)
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
			$this->view->exception = $e->__toString();

			$this->_forward('success', 'utility', 'touch', array(
				'status' => $this->view->status,
				'messages' => array($this->view->error, $this->view->exception),
			));
    }
  }

  public function requestFriendAction()
  {
		$this->view->setScriptPath(Engine_Api::_()->touch()->getScriptPath('user'));
    $this->view->notification = $notification = $this->_getParam('notification');
  }
}
