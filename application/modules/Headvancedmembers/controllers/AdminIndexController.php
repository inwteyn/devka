<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexControlle.php 2015-10-06 16:58:20  $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Headvancedmembers_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {

  }

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('headvancedmembers_admin_main', array(), 'headvancedmembers_admin_general');
        $this->view->form =$form =  new Headvancedmembers_Form_Admin_General();
    if (!$this->getRequest()->isPost()) {
      return;
    }
    $params = $this->getRequest()->getParams();
    if (!$form->isValid($params)) {
      return;
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->__set('headvancedmembers.mode', (int)($form->getValue('view')));
    $settings->__set('headvancedmembers.verification', (int)$form->getValue('verification'));

    $form->addNotice('Settings have been successfully saved');
  }

  public function membersAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('headvancedmembers_admin_main', array(), 'headvancedmembers_admin_members');

    $this->view->formFilter = $formFilter = new User_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select();

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'user_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    // Set up select info
    $select->order(( !empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if( !empty($values['displayname']) ) {
      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
    }
    if( !empty($values['username']) ) {
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }
    if( !empty($values['email']) ) {
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }
    if( !empty($values['level_id']) ) {
      $select->where('level_id = ?', $values['level_id'] );
    }
    if( isset($values['enabled']) && $values['enabled'] != -1 ) {
      $select->where('enabled = ?', $values['enabled'] );
    }
    if( !empty($values['user_id']) ) {
      $select->where('user_id = ?', (int) $values['user_id']);
    }

    // Filter out junk
    $valuesCopy = array_filter($values);
    // Reset enabled bit
    if( isset($values['enabled']) && $values['enabled'] == 0 ) {
      $valuesCopy['enabled'] = 0;
    }

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
    $this->view->formValues = $valuesCopy;

    $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
    //$this->view->formDelete = new User_Form_Admin_Manage_Delete();

    $this->view->openUser = (bool) ( $this->_getParam('open') && $paginator->getTotalItemCount() == 1 );
  }
  public function verifyAction()
  {
    $id = $this->_getParam('user_ud', null);

    $table = Engine_Api::_()->getDbTable('status', 'headvancedmembers');
    $select  = $table->select()->where('user_id = ?',$id);
    $row = $table->fetchRow($select);
    if($row){
      if($row->status==1){
        $row->status = 0;
        $row->save();
      }else{
        $row->status = 1;
        $row->save();
      }
    }else{
      $data = array(
        'user_id' => $id,
        'status' => 1
      );
      $insert = $table->createRow($data);
      $new = $insert->save();
    }
    $user = Engine_Api::_()->getItem('user', $id);
 if (Engine_Api::_()->headvancedmembers()->isActive($user)){
              echo   '<img class="irc_mi" style="margin-bottom: -5px;cursor: pointer;"
                     src="'.$this->view->advmembersBaseUrl().'application/modules/Headvancedmembers/externals/images/icon_verified.png"
                     width="24" height="24" title="verified">';
             } else {
                   echo    '<img class="irc_mi" style="margin-bottom: -5px;cursor: pointer;" src="'.$this->view->advmembersBaseUrl().'application/modules/Headvancedmembers/externals/images/icon_not_verified.png" width="24" height="24" title="not verified">';

}
die();


  }
  public function verifymembersAction()
  {
    $user_id = $this->_getParam('user_ud',0);
    if(!$user_id || $user_id == 0){
      die('Error');
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('verification','headvancedmembers');
   $select = $table->select()->where('user_id = ?',$user_id);
$reo = $table->fetchAll($select);


    $this->view->items = $reo;
  }
  public function multiModifyAction()
  {
    if( $this->getRequest()->isPost() ) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key=>$value) {
        if( $key == 'modify_' . $value ) {
          $user = Engine_Api::_()->getItem('user', (int) $value);
        if( $values['submit_button'] == 'verify' ) {
          $table = Engine_Api::_()->getDbTable('status', 'headvancedmembers');
          $select  = $table->select()->where('user_id = ?',$user->getIdentity());
          $row = $table->fetchRow($select);
          if($row){
            if($row->status==1){
              $row->status = 0;
              $row->save();
            }else{
              $row->status = 1;
              $row->save();
            }
          }else{
            $data = array(
              'user_id' => $user->getIdentity(),
              'status' => 1
            );
            $insert = $table->createRow($data);
            $new = $insert->save();
          }
          }
        }
      }
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'members'));
  }
}