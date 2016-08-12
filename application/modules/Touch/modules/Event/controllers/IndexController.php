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

class Event_IndexController extends Touch_Controller_Action_Standard
{

  protected $_navigation;


  public function init() 
  {

    if( !$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid() ) return;

    //$this->getNavigation();
    
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

  public function browseAction()
  {
    $filter = $this->_getParam('filter', 'future');
    if( $filter != 'past' && $filter != 'future' ) $filter = 'future';
    $this->view->filter = $filter;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('event_main');

		foreach ($navigation->getPages() as $page)
    {
      if( ($page->label == "Upcoming Events" && $filter == "future") || ($page->route == "event_past" && $filter == "past")) {
			$page->active = true;
      }
    }

    // Create form
    $this->view->formFilter = $formFilter = new Touch_Form_Search();
    $defaultValues = $formFilter->getValues();

    // Populate form data
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    $this->view->assign($values);

    // Prepare data
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->formValues = $values = $formFilter->getValues();
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

    $values['search'] = 1;

    if( $filter == "past" )
    {
      $values['past'] = 1;
    } else {
      $values['future'] = 1;
    }

     // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if( $user_id ) $values['user_id'] = $user_id;

    $eventApi = Engine_Api::_()->getApi('core', 'event');
    $eventsTbl = Engine_Api::_()->getDbTable('events', 'event');
    
    $select = (method_exists($eventApi, 'getEventSelect'))
      ? $eventApi->getEventSelect($values)
      : $eventsTbl->getEventSelect($values);

    $this->view->search = $search = $this->_getParam('search');

    if (!empty($search)){
      $select->where('title LIKE ? OR description = ?', '%'.$search.'%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

  }

  public function manageAction()
  {
    // Create form
    if( !$this->_helper->requireAuth()->setAuthParams('event', null, 'edit')->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('event_main');

    $this->view->formFilter = $formFilter = new Touch_Form_Search();
    $defaultValues = $formFilter->getValues();

    // Populate form data
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }
    $this->view->assign($values);

    $viewer = $this->_helper->api()->user()->getViewer();
    $table = $this->_helper->api()->getDbtable('events', 'event');
    $tableName = $table->info('name');

    $membership = Engine_Api::_()->getDbtable('membership', 'event');
    $select = $membership->getMembershipsOfSelect($viewer);

    //$select->where("endtime > FROM_UNIXTIME(?)", time());

    $select->order('starttime ASC');

    $this->view->search = $search = $this->_getParam('search');

    if (!empty($search)){
      $select->where("$tableName.title LIKE ? OR $tableName.description = ?", '%'.$search.'%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));


    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
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


    if( !$this->_helper->requireUser->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('event', null, 'create')->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('event_main');

    $viewer = Engine_Api::_()->user()->getViewer();
    $parent_type = $this->_getParam('parent_type');
    $parent_id = $this->_getParam('parent_id', $this->_getParam('subject_id'));

    if( $parent_type == 'group' && Engine_Api::_()->hasItemType('group') ) {
      $this->view->group = $group = Engine_Api::_()->getItem('group', $parent_id);
      if( !$this->_helper->requireAuth()->setAuthParams($group, null, 'event')->isValid() ) {
        return;
      }
    } else {
      $parent_type = 'user';
      $parent_id = $viewer->getIdentity();
    }

    // Create form
    $this->view->parent_type = $parent_type;
    $this->view->form = $form = new Touch_Form_Event_Create(array(
      'parent_type' => $parent_type,
      'parent_id' => $parent_id
    ));

    // Populate form options
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'event');
    foreach( $categoryTable->fetchAll($categoryTable->select()->order('title ASC')) as $category ) {
      $form->category_id->addMultiOption($category->category_id, $category->title);
    }

    // Not post/invalid
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

      

    // Process
    $values = $form->getValues();

    $values['user_id'] = $viewer->getIdentity();
    $values['parent_type'] = $parent_type;
    $values['parent_id'] =  $parent_id;
    if( $parent_type == 'group' && Engine_Api::_()->hasItemType('group') && empty($values['host']) ) {
      $values['host'] = $group->getTitle();
    }

    // Convert times
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    $db = Engine_Api::_()->getDbtable('events', 'event')->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create event
      $table = $this->_helper->api()->getDbtable('events', 'event');
      $event = $table->createRow();

      $event->setFromArray($values);
      $event->save();

      // Add owner as member
      $event->membership()->addMember($viewer)
        ->setUserApproved($viewer)
        ->setResourceApproved($viewer);

      // Add owner rsvp
      $event->membership()
        ->getMemberInfo($viewer)
        ->setFromArray(array('rsvp' => 2))
        ->save();

      if ($values['photo_id']){
        $photo = Engine_Api::_()->storage()->get($values['photo_id']);
        if ($photo){
          $event->setPhoto($photo->storage_path);
        }
      }

      // Add photo
      if( !empty($values['photo']) ) {
        $event->setPhoto($form->photo);
      }

      // Set auth
      $auth = Engine_Api::_()->authorization()->context;

      if( $values['parent_type'] == 'group' ) {
        $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }

      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'everyone';
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

      // Add an entry for member_requested
      $auth->setAllowed($event, 'member_requested', 'view', 1);

      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

      $action = $activityApi->addActivity($viewer, $event, 'event_create', null, array('is_mobile' => true));

      if( $action ) {
        $activityApi->attachActivity($action, $event);
      }
      // Commit
      $db->commit();

      return $this->_forward('success', 'utility', 'touch', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_EVENT_FORM_CREATE_SUCCESS')),
        'parentRedirect' => $this->view->url(array('id' => $event->getIdentity()), 'event_profile', true),
      ));

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