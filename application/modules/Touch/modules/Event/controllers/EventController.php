<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: EventController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Event_EventController extends Touch_Controller_Action_Standard
{
  public function init()
  {
    $id = $this->_getParam('event_id', $this->_getParam('id', null));
    if( $id )
    {
      $event = Engine_Api::_()->getItem('event', $id);
      if( $event )
      {
        Engine_Api::_()->core()->setSubject($event);
      }
    }
  }


  public function editAction()
  {
    $event_id = $this->getRequest()->getParam('event_id');
    $event = Engine_Api::_()->getItem('event', $event_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !($this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() || $event->isOwner($viewer)) ) {
      return;
    }

    // Create form
    $event = Engine_Api::_()->core()->getSubject();
    $form = new Touch_Form_Event_Edit(array('parent_type'=>$event->parent_type, 'parent_id'=>$event->parent_id));
    $form->removeElement('photo_id');
    $form->removeElement('photo');
    $this->view->form =/*  for a while in v 4.1.9 */$form;

    // Populate form options
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'event');
    foreach( $categoryTable->fetchAll($categoryTable->select()->order('title ASC')) as $category ) {
      $form->category_id->addMultiOption($category->category_id, $category->title);
    }

    if( !$this->getRequest()->isPost() )
    {
      // Populate auth
      $auth = Engine_Api::_()->authorization()->context;

      if( $event->parent_type == 'group' ) {
        $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      foreach( $roles as $role ) {
	if( isset($form->auth_view->options[$role]) && $auth->isAllowed($event, $role, 'view') ) {
          $form->auth_view->setValue($role);
	}
	if( isset($form->auth_comment->options[$role]) && $auth->isAllowed($event, $role, 'comment') ) {
          $form->auth_comment->setValue($role);
	}
	if( isset($form->auth_photo->options[$role]) && $auth->isAllowed($event, $role, 'photo') ) {
          $form->auth_photo->setValue($role);
	}
      }
      $form->auth_invite->setValue($auth->isAllowed($event, 'member', 'invite'));
      $form->populate($event->toArray());

      // Convert and re-populate times
      $start = strtotime($event->starttime);
      $end = strtotime($event->endtime);
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $start = date('Y-m-d H:i:s', $start);
      $end = date('Y-m-d H:i:s', $end);
      date_default_timezone_set($oldTz);

      $form->populate(array(
        'starttime' => $start,
        'endtime' => $end,
      ));
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }


    // Process
    $values = $form->getValues();

    // Convert times
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    // Check parent
    if( !isset($values['host']) && $event->parent_type == 'group' && Engine_Api::_()->hasItemType('group') ) {
     $group = Engine_Api::_()->getItem('group', $event->parent_id);
     $values['host']  = $group->getTitle();
    }

    // Process
    $db = Engine_Api::_()->getItemTable('event')->getAdapter();
    $db->beginTransaction();

    try
    {
      // Set event info
      $event->setFromArray($values);
      $event->save();

      if( !empty($values['photo']) ) {
        $event->setPhoto($form->photo);
      }


      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;

      if( $event->parent_type == 'group' ) {
        $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($event, $role, 'view',    ($i <= $viewMax));
        $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($event, $role, 'photo',   ($i <= $photoMax));
      }

      $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

      // Commit
      $db->commit();
    }

    catch( Engine_Image_Exception $e )
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($event) as $action ) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $redirect_url = ($this->_getParam('ref') === 'profile')
        ? $event->getHref()
        : $this->view->url(array('route' => 'event_general', 'action' => 'manage'));


    return $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_EVENT_FORM_EDIT_SUCCESS')),
      'parentRedirect' => $redirect_url,
    ));

  }


  public function deleteAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $event = Engine_Api::_()->getItem('event', $this->getRequest()->getParam('event_id'));
    if( !$this->_helper->requireAuth()->setAuthParams($event, null, 'delete')->isValid()) return;


    // Make form
    $this->view->form = $form = new Event_Form_Delete();


    if( !$event )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Event doesn't exists or not authorized to delete");
      return $this->_forward('success', 'utility', 'touch', array(
        'status' => $this->view->status,
        'messages' => array($this->view->error, $this->view->exception),
      ));

    }

    if( !$this->getRequest()->isPost() ){
      return ;
    }

    $db = $event->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $event->delete();
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected event has been deleted.');

     return $this->_forward('success', 'utility', 'touch', array(
      'messages' =>array($this->view->message),
      'parentRedirect' => $this->view->url(array('action' => 'manage'), 'event_general', true),
    ));

  }


}