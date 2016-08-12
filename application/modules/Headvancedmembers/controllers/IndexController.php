<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2015-10-06 16:58:20  $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Headvancedmembers_IndexController extends Core_Controller_Action_Standard
{

  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->mode =  $settings->getSetting('headvancedmembers.mode', 0);
  }
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }
  public function verifymeAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('verification','headvancedmembers');
    $data = array(
      'user_id' => $viewer->getIdentity(),
      'verified_id' => $viewer->getIdentity(),
      'date'=> date('Y:m:d H:m:s')
    );
    $ver = $table->createRow();
    $ver->user_id = $viewer->getIdentity();
    $ver->verified_id = $viewer->getIdentity();
    $ver->date = date('Y:m:d H:m:s');
    $ver->save();
    die('test');
  }
  public function verifyuserAction()
  {
    $user_id = $this->_getParam('user_id',0);
    if(!$user_id || $user_id == 0){
      die('false');
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('verification','headvancedmembers');
    $data = array(
      'user_id' => $viewer->getIdentity(),
      'verified_id' => $viewer->getIdentity(),
      'date'=> date('Y:m:d H:m:s')
    );
    $ver = $table->createRow();
    $ver->user_id = $user_id;
    $ver->verified_id = $viewer->getIdentity();
    $ver->date = date('Y:m:d H:m:s');
    $ver->save();
    die('test');
  }
  public function verifymembersAction()
  {
    $user_id = $this->_getParam('user_id',0);
    if(!$user_id || $user_id == 0){
      die('false');
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('verification','headvancedmembers');
    $data = array(
      'user_id' => $viewer->getIdentity(),
      'verified_id' => $viewer->getIdentity(),
      'date'=> date('Y:m:d H:m:s')
    );
    $ver = $table->createRow();
    $ver->user_id = $user_id;
    $ver->verified_id = $viewer->getIdentity();
    $ver->date = date('Y:m:d H:m:s');
    $ver->save();
    die('test');
  }
  public function browseAction()
  {

    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }
    if( !$this->_executeSearch() ) {
      // throw new Exception('error');
    }



    if( $this->_getParam('ajax') ) {

      if($this->view->mode == 1){

            echo $this->renderScript('_browseUsersLarge.tpl');

      }elseif($this->view->mode == 0){

           echo $this->renderScript('_browseUsers.tpl');

      }elseif($this->view->mode == 2){

          echo $this->renderScript('_browseMap.tpl');
      }
    }

    if( !$this->_getParam('ajax') ) {
      // Render
      $this->_helper->content
        ->setEnabled()
      ;
    }
  }
  public function searchAction(){
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');
    $displayname = $this->_getParam('search');
    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');
    $select = $table->select()
      ->from($userTableName)
      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
      //->group("{$userTableName}.user_id")
      ->where("{$userTableName}.search = ?", 1)
      ->where("{$userTableName}.enabled = ?", 1);

    $searchDefault = true;

    // Build the photo and is online part of query
    if( isset($has_photo) && !empty($has_photo) ) {
      $select->where($userTableName.'.photo_id != ?', "0");
      $searchDefault = false;
    }

    if( isset($is_online) && !empty($is_online) ) {
      $select
        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
        ->group("engine4_user_online.user_id")
        ->where($userTableName.'.user_id != ?', "0");
      $searchDefault = false;
    }

    // Add displayname
    if( !empty($displayname) ) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
      $searchDefault = false;
    }
    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(50);


    $this->view->users = $paginator;
    $this->view->totalUsers = $paginator->getTotalItemCount();
    $this->view->userCount = $paginator->getCurrentItemCount();
    if( $this->_getParam('search') ) {
      if($this->_getParam('type_view') == 1){
       // print_die($this->_getParam('type_view'));
        echo $this->renderScript('_browseUsersLarge.tpl');;
      }else{
        echo $this->renderScript('_browseUsers.tpl');
      }
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
      ->where("{$userTableName}.enabled = ?", 1);

    $searchDefault = true;

    // Build the photo and is online part of query
    if( isset($has_photo) && !empty($has_photo) ) {
      $select->where($userTableName.'.photo_id != ?', "0");
      $searchDefault = false;
    }

    if( isset($is_online) && !empty($is_online) ) {
      $select
        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
        ->group("engine4_user_online.user_id")
        ->where($userTableName.'.user_id != ?', "0");
      $searchDefault = false;
    }

    // Add displayname
    if( !empty($displayname) ) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
      $searchDefault = false;
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach( $searchParts as $k => $v ) {
      $select->where("`{$searchTableName}`.{$k}", $v);

      if(isset($v) && $v != ""){
        $searchDefault = false;
      }
    }

    if($searchDefault){
      $select->order("{$userTableName}.lastlogin_date DESC");
    } else {
      $select->order("{$userTableName}.displayname ASC");
    }

    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(12);
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
  public function mylocationAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('user_settings');

    $this->view->form = $form = new Headvancedmembers_Form_MyLocation();
    $user =  Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('markers', 'headvancedmembers');
    $select = $table->select()
        ->where('user_id = ?', $user->getIdentity())
    ;
    $user_location  = $table->fetchRow($select);
    if($user_location !== null){
      $form->populate($user_location->toArray());
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $values = $form->getValues();


    $ans = Engine_Api::_()->getApi('gmap', 'headvancedmembers')->getMarker($values);

    if( count($ans)>0){

      $select = $table->select()
          ->where('user_id = ?', $user->getIdentity())
          ->limit(1);

      $list = $table->fetchRow($select);

      if (null === $list) {
        $list = $table->createRow();
        $list->setFromArray(array_merge($ans,array('user_id' => $user->getIdentity())));
        $list->save();
        $this->view->saveSuccessful = true;

      }else{
        $list->setFromArray(array_merge($ans,array('user_id' => $user->getIdentity())));
        $list->save();
      }
      $this->view->saveSuccessful = true;

    }




  }
}
