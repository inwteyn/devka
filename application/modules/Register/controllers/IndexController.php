<?php

class Register_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $usersTbl = Engine_Api::_()->getItemTable('user');
    $users = $usersTbl->fetchAll();
    $add_friend = false;
    if ($users->count() > 1) {
      $add_friend = true;
    }
    $this->view->add_friend = $add_friend;
  }

  // ADD USER
  public function addUsersAction()
  {
    $this->view->form = $form = new Register_Form_Create();

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    $count = $this->_getParam('count', 10);
    if ($count > 0 && $count < 11) {
      $result = Engine_Api::_()->register()->processAddUser($count);
      Engine_Api::_()->register()->printResult($result);
    }
  }
  // ADD USER END

  // ADD FRIEND
  public function addFriendsAction()
  {
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }
    if( !$this->_executeSearch() ) {
      // throw new Exception('error');
    }
    if( $this->_getParam('ajax') ) {
      $this->renderScript('_browseUsers.tpl');
    }
  }

  public function addAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if( null == ($user_id = $this->_getParam('user_id')) ||
        null == ($user = Engine_Api::_()->getItem('user', $user_id)) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // check that user is not trying to befriend 'self'
    if( $viewer->isSelf($user) ) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You cannot befriend yourself.'))
      ));
    }

    // check that user is already friends with the member
    if( $user->membership()->isMember($viewer) ) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are already friends with this member.'))
      ));
    }

    // check that user has not blocked the member
    if( $viewer->isBlocked($user) ) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Friendship request was not sent because you blocked this member.'))
      ));
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Add();

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      // send request
      $user->membership()
        ->addMember($viewer)
        ->setUserApproved($viewer);

      $user->membership()->setResourceApproved($viewer);

      $db->commit();

      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been sent.');
      $message = Zend_Registry::get('Zend_Translate')->_("You are now friends with this member.");

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array($message)
      ));

    } catch( Exception $e ) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
      return;
    }
  }

  protected function _executeSearch()
  {
    // Check form
    $form = new User_Form_Search(array(
      'type' => 'user'
    ));

    if( !$form->isValid($this->_getAllParams()) ) {
      $this->view->error = true;
      $this->view->totalUsers = 0;
      $this->view->userCount = 0;
      $this->view->page = 1;
      return false;
    }

    $this->view->form = $form;

    // Get search params
    $page = (int)  $this->_getParam('page', 1);
    $ajax = (bool) $this->_getParam('ajax', false);
    $options = $form->getValues();

    // Process options
    $tmp = array();
    $originalOptions = $options;
    foreach( $options as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $options = $tmp;

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');

    //extract($options); // displayname
    $profile_type = @$options['profile_type'];
    $displayname = @$options['displayname'];
    if (!empty($options['extra'])) {
      extract($options['extra']); // is_online, has_photo, submit
    }

    // Contruct query
    $select = $table->select()
      //->setIntegrityCheck(false)
      ->from($userTableName)
      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
      //->group("{$userTableName}.user_id")
      ->where("{$userTableName}.search = ?", 1)
      ->where("{$userTableName}.enabled = ?", 1)
      ->order("{$userTableName}.displayname ASC");

    // Build the photo and is online part of query
    if( isset($has_photo) && !empty($has_photo) ) {
      $select->where($userTableName.'.photo_id != ?', "0");
    }

    if( isset($is_online) && !empty($is_online) ) {
      $select
        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
        ->group("engine4_user_online.user_id")
        ->where($userTableName.'.user_id != ?', "0");
    }

    // Add displayname
    if( !empty($displayname) ) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach( $searchParts as $k => $v ) {
      $select->where("`{$searchTableName}`.{$k}", $v);
    }

    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);

    $this->view->page = $page;
    $this->view->ajax = $ajax;
    $this->view->users = $paginator;
    $this->view->totalUsers = $paginator->getTotalItemCount();
    $this->view->userCount = $paginator->getCurrentItemCount();
    $this->view->topLevelId = $form->getTopLevelId();
    $this->view->topLevelValue = $form->getTopLevelValue();
    $this->view->formValues = array_filter($originalOptions);

    return true;
  }
  // ADD FRIEND END


  // ADD BLOGS
  public function addBlogsAction()
  {
    $this->view->form = $form = new Register_Form_Create();
    $form->setTitle('Set count of blogs');
    $form->getElement('submit')->setLabel('Create Random Blogs');

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    $count = $this->_getParam('count', 10);
    if ($count > 0 && $count < 11) {
      $result = Engine_Api::_()->register()->addBlogs($count);
      Engine_Api::_()->register()->printResult($result);
    }
  }
}