<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GroupController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Group_GroupController extends Touch_Controller_Action_Standard
{

  public function init()
  {
    if( 0 !== ($group_id = (int) $this->_getParam('group_id')) &&
        null !== ($group = Engine_Api::_()->getItem('group', $group_id)) ) {
      Engine_Api::_()->core()->setSubject($group);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('group');
  }

  public function editAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }

    $group = Engine_Api::_()->core()->getSubject();
    $officerList = $group->getOfficerList();
    $form = new Touch_Form_Group_Edit();
    $form->removeElement('photo_id');
    $form->removeElement('photo');
    $this->view->form =/*  for a while in v 4.1.9 */$form;

    // Populate with categories
    foreach( Engine_Api::_()->getDbtable('categories', 'group')->fetchAll() as $row ) {
      $form->category_id->addMultiOption($row->category_id, $row->title);
    }

    if( count($form->category_id->getMultiOptions()) <= 1 ) {
      $form->removeElement('category_id');
    }

    if( !$this->getRequest()->isPost() ) {
      // Populate auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('officer', 'member', 'registered', 'everyone');
      $actions = array('view', 'comment', 'invite', 'photo', 'event');
      $perms = array();
      foreach( $roles as $roleString ) {
        $role = $roleString;
        if( $role === 'officer' ) {
          $role = $officerList;
        }
        foreach( $actions as $action ) {
          if( $auth->isAllowed($group, $role, $action) ) {
            $perms['auth_' . $action] = $roleString;
          }
        }
      }

      $form->populate($group->toArray());
      $form->populate($perms);
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getItemTable('group')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();

      // Set group info
      $group->setFromArray($values);
      $group->save();

      if( !empty($values['photo']) ) {
        $group->setPhoto($form->photo);
      }

      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;

      $roles = array('officer', 'member', 'registered', 'everyone');

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }

      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);
      $eventMax = array_search($values['auth_event'], $roles);
      $inviteMax = array_search($values['auth_invite'], $roles);

      foreach( $roles as $i => $role ) {
        if( $role === 'officer' ) {
          $role = $officerList;
        }
        $auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
        $auth->setAllowed($group, $role, 'event', ($i <= $eventMax));
        $auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
      }

      // Create some auth stuff for all officers
      $auth->setAllowed($group, $officerList, 'photo.edit', 1);
      $auth->setAllowed($group, $officerList, 'topic.edit', 1);

      // Add auth for invited users
      $auth->setAllowed($group, 'member_requested', 'view', 1);

      // Commit
      $db->commit();
    } catch( Engine_Image_Exception $e ) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($group) as $action ) {
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
        ? $group->getHref()
        : $this->view->url(array('route' => 'group_general', 'action' => 'manage'));

    return $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_GROUP_FORM_EDIT_SUCCESS')),
      'parentRedirect' => $redirect_url,
    ));



  }

  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = Engine_Api::_()->getItem('group', $this->getRequest()->getParam('group_id'));
    if( !$this->_helper->requireAuth()->setAuthParams($group, null, 'delete')->isValid()) return;

    // Make form
    $this->view->form = $form = new Touch_Form_Group_Delete();


    if( !$group )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Group doesn't exists or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    $db = $group->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $group->delete();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected group has been deleted.');

    return $this->_forward('success', 'utility', 'touch', array(
      'messages' =>array($this->view->message),
      'parentRedirect' => $this->view->url(array('action' => 'manage'), 'group_general', true),
    ));


  }

}