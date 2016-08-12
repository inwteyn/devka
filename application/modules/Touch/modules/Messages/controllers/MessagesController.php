<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MessagesController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Messages_MessagesController extends Touch_Controller_Action_Standard
{
  protected $_navigation;

  protected $_form;

  public function init()
  {
    $this->_helper->requireUser();
    $this->_helper->requireAuth()->setAuthParams('messages', null, 'create');
  }
  
  public function inboxAction()
  {
    $this->view->navigation = $this->getNavigation();
		$viewer = $this->_helper->api()->user()->getViewer();
    $this->view->paginator = $this->getPaginator($viewer, 1, 'inbox');
  }

  public function outboxAction()
  {
    $this->view->navigation = $this->getNavigation();
		$viewer = $this->_helper->api()->user()->getViewer();
	    $this->view->paginator = $this->getPaginator($viewer, 1, 'outbox');
  }

  public function viewAction()
  {
    $this->view->navigation = $navigation =  $this->getNavigation();
    $id = $this->_getParam('id');
    $viewer = $this->_helper->api()->user()->getViewer();

    // Get conversation info
    $this->view->conversation = $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

		$label = ($conversation->getTitle())?$conversation->getTitle():$this->view->translate('(No Subject)');
		$viewPage = array(
			'label' => $label,
			'route' => 'messages_general',
			'action' => 'view',
			'controller' => 'messages',
			'module' => 'messages',
			'params'=>array(
				'id'=>$conversation->getIdentity(),
			),
			'active'=>true,
		);
		$navigation->addPage($viewPage);

    // Make sure the user is part of the conversation
    if( !$conversation || !$conversation->hasRecipient($viewer) ) {
      return $this->_forward('inbox');
    }
    
    $this->view->recipients = $recipients = $conversation->getRecipients();

    $blocked = false;
    $blocker = "";
    foreach($recipients as $recipient){
      if ($viewer->isBlockedBy($recipient)){
        $blocked = true;
        $blocker = $recipient;
      }
    }

    $this->view->blocked = $blocked;
    $this->view->blocker = $blocker;

    // Process form
    $this->view->form = $form = new Messages_Form_Reply();
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $db = $this->_helper->api()->getDbtable('messages', 'messages')->getAdapter();
      $db->beginTransaction();

      try
      {
        $values = $form->getValues();
        $values['conversation'] = (int) $id;

        $conversation->reply($viewer, $values['body'], false);

        // Send notifications
        foreach( $recipients as $user )
        {
          if( $user->getIdentity() == $viewer->getIdentity() )
          {
            continue;
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
            $user,
            $viewer,
            $conversation,
            'message_new'
          );
        }

        // Increment messages counter
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('touch.messages.creations');

        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

			$this->_forward('success', 'utility', 'touch', array(
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
				'parentRefresh' => true,
			));
    }

    // Make sure to load the messages after posting :P
    $this->view->messages = $messages = $conversation->getMessages($viewer);

    $conversation->setAsRead($viewer);
  }

  public function composeAction()
  {
    $this->view->navigation = $this->getNavigation();
		$viewer = $this->_helper->api()->user()->getViewer();

		$this->view->form = $form = new Touch_Form_Messages_Compose();
		
		// Get params
    $multi = $this->_getParam('multi');
    $to = $this->_getParam('to');
    $toObject = null;

    // Build
    $isPopulated = false;
    if( !empty($to) && (empty($multi) || $multi == 'user') ) {
      $multi = null;
      // Prepopulate user
      $toUser = Engine_Api::_()->getItem('user', $to);
      if( $toUser instanceof User_Model_User &&
          !$viewer->isBlockedBy($toUser) ) {
        $this->view->toObject = $toObject = $toUser;
        $form->toValues->setValue($toUser->getGuid());
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    } else if( !empty($to) && !empty($multi) ) {
      // Prepopulate group/event/etc
      $item = Engine_Api::_()->getItem($multi, $to);
      if( $item instanceof Core_Model_Item_Abstract && (
            $item->isOwner($viewer) ||
            $item->authorization()->isAllowed($viewer, 'edit')
          )) {
        $this->view->toObject = $toObject = $item;
        $form->toValues->setValue($item->getGuid());
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    }
    $this->view->isPopulated = $isPopulated;

    // Build normal
    if( !$isPopulated ) {
//       Apparently this is using AJAX now?
//      $friends = $viewer->membership()->getMembers();
//      $data = array();
//      foreach( $friends as $friend ) {
//        $data[] = array(
//          'label' => $friend->getTitle(),
//          'id' => $friend->getIdentity(),
//          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
//        );
//      }
//      $this->view->friends = Zend_Json::encode($data);
    }

    // Get config
    $this->view->maxRecipients = $maxRecipients = 10;


    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values = $form->getValues();

      // Prepopulated
      if( $toObject instanceof User_Model_User ) {
        $recipientsUsers = array($toObject);
        $recipients = $toObject;
      } else if( $toObject instanceof Core_Model_Item_Abstract &&
          method_exists($toObject, 'membership') ) {
        $recipientsUsers = $toObject->membership()->getMembers();
//        $recipients = array();
//        foreach( $recipientsUsers as $recipientsUser ) {
//          $recipients[] = $recipientsUser->getIdentity();
//        }
        $recipients = $toObject;
      }
      // Normal
      else {
        $recipients = preg_split('/[,. ]+/', $values['toValues']);
        // clean the recipients for repeating ids
        // this can happen if recipient is selected and then a friend list is selected
        $recipients = array_unique($recipients);
        // Slice down to 10
        $recipients = array_slice($recipients, 0, $maxRecipients);
        $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
      }

      // Create conversation
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
        $viewer,
        $recipients,
        $values['title'],
        $values['body']
      );

      // Send notifications
      foreach( $recipientsUsers as $user ) {
        if( $user->getIdentity() == $viewer->getIdentity() ) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
          $user,
          $viewer,
          $conversation,
          'message_new'
        );
      }

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('touch.messages.creations');

      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
      'parentRedirect' => $conversation->getHref(), //$this->getFrontController()->getRouter()->assemble(array('action' => 'inbox'))
    ));
  }
  
  public function deleteAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    $message_id = $this->view->message_id = $this->getRequest()->getParam('message_id');

    if (!$this->getRequest()->isPost())
    return;

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $db = $this->_helper->api()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();
    try {
      $recipients = Engine_Api::_()->getItem('messages_conversation', $message_id)->getRecipientsInfo();
      //$recipients = Engine_Api::_()->getApi('core', 'messages')->getConversationRecipientsInfo($message_id);
      foreach ($recipients as $r) {
        if ($viewer_id == $r->user_id) {
          $r->inbox_deleted  = true;
          $r->outbox_deleted = true;
          $r->save();
        }
      }
      $this->_forward('success', 'utility', 'touch', array(
        'messages'=>Zend_Registry::get('Zend_Translate')->_('Message has been successfully deleted.'),
				'parentRefresh'=>true
      ));
      
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }
  }
  public function getNavigation()
  {
		$viewer = $this->_helper->api()->user()->getViewer();

		$inbox = 'Inbox';
		if (($unread = $this->_helper->api()->messages()->getUnreadMessageCount($viewer))){
			$inbox = $this->view->translate('Inbox') . '(' . $unread . ')';
		}

    if( is_null($this->_navigation) )
    {
      $this->_navigation = new Zend_Navigation();
      $this->_navigation->addPages(array(
        array(
          'label' => $inbox,
          'route' => 'messages_general',
          'action' => 'inbox',
          'controller' => 'messages',
          'module' => 'messages'
        ),
        array(
          'label' => 'Sent Messages',
          'route' => 'messages_general',
          'action' => 'outbox',
          'controller' => 'messages',
          'module' => 'messages'
        ),
        array(
          'label' => 'Compose Message',
          'route' => 'messages_general',
          'action' => 'compose',
          'controller' => 'messages',
          'module' => 'messages'
        )
      ));
    }

    return $this->_navigation;

  }
  protected function getPaginator(User_Model_User $viewer, $page = 1, $box_type)
  {
    $table = $this->getTable();
    $viewer_id = $viewer->getIdentity();
    $this->view->form_filter = $form = new Touch_Form_Search();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->view->form_value = $this->_getParam('search');
  }
    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $cName = $table->info('name');
    if($box_type != 'inbox' && $box_type != 'outbox'){
      throw new Exception('Wrong parameter');
    }
    $select = $table->select()
      ->from($cName)
      ->joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
      ->where("`{$rName}`.`user_id` = ?", $viewer_id)
      ->where("`{$rName}`.`".$box_type."_deleted` = ?", 0);
    

    if ($this->_getParam('search', false)) {
      $select->where($cName.'.title LIKE ? ', '%' . $this->_getParam('search') . '%');
    }
    $select->order(new Zend_Db_Expr($box_type.'_updated DESC'));
    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    return $paginator;

  }
  protected function getTable()
  {
      return Engine_Api::_()->getItemTable('messages_conversation');
  }

}