<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Group_IndexController extends Touch_Controller_Action_Standard
{

  public function init()
  {
    if( !(
      $this->_helper->requireAuth()->setAuthParams('group', null, 'view')->isValid() ||
      $this->is_iPhoneUploading())
    ){
				return;
		}

    $id = $this->_getParam('group_id', $this->_getParam('id', null));
    if( $id ) {
      $group = Engine_Api::_()->getItem('group', $id);
      if( $group ) {
        Engine_Api::_()->core()->setSubject($group);
      }
    }
  }

  public function browseAction()
  {
    // Navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
            ->getNavigation('group_main');

    // Form
    $this->view->formFilter = $formFilter = new Touch_Form_Search();

    $formFilter->getElement('search')->setValue($this->_getParam('search'));

 		$table = Engine_Api::_()->getItemTable('group');
    $select = $table->select();

    // Search
    $select->where('search = ?', 1);

		if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%'.$this->_getParam('search').'%');
    }
		$select->order('creation_date DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  public function manageAction()
  {
    // Navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
          ->getNavigation('group_main');

    // Form
    $this->view->formFilter = $formFilter = new Touch_Form_Search();

    $formFilter->getElement('search')->setValue($this->_getParam('search'));

    $viewer = $this->_helper->api()->user()->getViewer();
    $membership = Engine_Api::_()->getDbtable('membership', 'group');
    $select = $membership->getMembershipsOfSelect($viewer);

    $table = Engine_Api::_()->getItemTable('group');
    $tName = $table->info('name');

    if( $this->_getParam('search', false) ) {
      $select->where(
          $table->getAdapter()->quoteInto("`{$tName}`.`title` LIKE ?", '%' . $this->_getParam('search') . '%') . ' OR ' .
          $table->getAdapter()->quoteInto("`{$tName}`.`description` LIKE ?", '%' . $this->_getParam('search') . '%')
      );
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->search = $this->_getParam('search');
		$paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  public function createAction()
  {
    if ($this->is_iPhoneUploading()){
      if (!isset($_FILES['picup-image-upload'])){
        return ;
      }
      $file = $_FILES['picup-image-upload'];
      $file = $this->fileUpload($file, $this->_getParam('owner_id'));
      $this->view->photo_name = (isset($file['name'])) ? $file['name'] : '';
      $this->view->photo_id = $file->file_id;
      return;
    }

    if( !$this->_helper->requireUser->isValid() )
        return;
    if( !$this->_helper->requireAuth()->setAuthParams('group', null, 'create')->isValid() )
        return;

    // Navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('group_main');

    // Create form
    $this->view->form = $form = new Touch_Form_Group_Create();

    // Populate with categories
    foreach( Engine_Api::_()->getDbtable('categories', 'group')->fetchAll() as $row ) {
      $form->category_id->addMultiOption($row->category_id, $row->title);
    }

    if( count($form->category_id->getMultiOptions()) <= 1 ) {
      $form->removeElement('category_id');
    }

    // Check method/data validitiy
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
    $viewer = $this->_helper->api()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();

    $db = Engine_Api::_()->getDbtable('groups', 'group')->getAdapter();
    $db->beginTransaction();

    try {
      // Create group
      /**
       * @var $group Group_Model_Group
       */
      $table = $this->_helper->api()->getDbtable('groups', 'group');
      $group = $table->createRow();
      $group->setFromArray($values);
      $group->save();

      // Add owner as member
      $group->membership()->addMember($viewer)
          ->setUserApproved($viewer)
          ->setResourceApproved($viewer);

      if ($values['photo_id']){
        $photo = Engine_Api::_()->storage()->get($values['photo_id']);
        if ($photo){
          $group->setPhoto($photo->storage_path);
        }
      }
      // Set photo
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

      $officerList = $group->getOfficerList();

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

      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $group, 'group_create', null, array('is_mobile' => true));
      if( $action ) {
        $activityApi->attachActivity($action, $group);
      }

      // Commit
      $db->commit();

      return $this->_forward('success', 'utility', 'touch', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_GROUP_FORM_CREATE_SUCCESS')),
        'parentRedirect' => $this->view->url(array('id' => $group->getIdentity()), 'group_profile', true),
      ));

    } catch( Engine_Image_Exception $e ) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }


  public function fileUpload($file, $user_id)
  {
    $user = Engine_Api::_()->getItem('user', $user_id);
    if (!$user){
      return ;
    }
    try {
      $params = array(
        'parent_type' => 'temporary',
        'parent_id' => 0,
        'user_id' => $user->getIdentity()
      );
      return Engine_Api::_()->storage()->create($file, $params);

    } catch (Exception $e){
      return ;
    }

  }

}