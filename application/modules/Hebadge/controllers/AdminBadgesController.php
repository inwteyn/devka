<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminBadgesController.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_AdminBadgesController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');
    $table = Engine_Api::_()->getDbTable('badges', 'hebadge');
    $params =array();
    $params['levels'] = null;
    
    $this->view->paginator = $paginator = $table->getPaginator($params, null, true);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page'));


  }

  public function createAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');

    $this->view->form = $form = new Hebadge_Form_Admin_Badge_Create();

    // populate icon
    $form->getElement('photo')->getDecorator('hebadgePhoto')->setOptions(array('type' => 'thumb.profile'));
    $form->getElement('icon')->getDecorator('hebadgePhoto')->setOptions(array('type' => 'thumb.icon'));

    if (!$this->getRequest()->isPost()){
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())){
      return;
    }
    /*if (!$form->isValidRequire($this->getRequest()->getPost())){
      $form->addError('Invalid data');
      return;
    }*/

    $values = $form->getValues();
    $values['level_type'] = 0;

    $table = Engine_Api::_()->getDbTable('badges', 'hebadge');

    $badge = $table->createRow();
    $badge->setFromArray($values);
    $badge->save();

    $badge->setRequire($form->getValuesRequire());

    if (!empty($values['photo'])){
      $badge->setPhoto($form->photo);
    }
    if (!empty($values['icon'])){
      $badge->setIcon($form->icon);
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'badges', 'action' => 'edit', 'badge_id' => $badge->badge_id), 'admin_default', true);

  }

  public function editAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');

    $this->view->form = $form = new Hebadge_Form_Admin_Badge_Edit();

    $badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('badge_id'));

    if (!$badge){
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'badges', 'action' => 'index'), 'admin_default', true);
    }

    $form->populate($badge->toArray());
    $form->setValuesRequire($badge->getRequireParams());

    // populate icon
    if ($badge->photo_id){
      $form->getElement('photo')->getDecorator('hebadgePhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgePhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.icon'));
    } else {
      $form->getElement('photo')->getDecorator('hebadgePhoto')->setOptions(array('type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgePhoto')->setOptions(array('type' => 'thumb.icon'));
    }

    if (!$this->getRequest()->isPost()){
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())){
      return;
    }
    if (!$form->isValidRequire($this->getRequest()->getPost())){
      $form->addError('Invalid data');
      return;
    }

    $values = $form->getValues();
    $values['level_type'] = 0;

    $badge->setFromArray($values);
    $badge->save();

    $badge->setRequire($form->getValuesRequire());

    if (!empty($values['photo'])){
      $badge->setPhoto($form->photo);
    }
    if (!empty($values['icon'])){
      $badge->setIcon($form->icon);
    }

    // set after submit
    if ($badge->photo_id){
      $form->getElement('photo')->getDecorator('hebadgePhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgePhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.icon'));
    } else {
      $form->getElement('photo')->getDecorator('hebadgePhoto')->setOptions(array('type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgePhoto')->setOptions(array('type' => 'thumb.icon'));
    }

    $form->addNotice('Your changes have been saved.');

  }

  public function removeAction()
  {
    $badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('badge_id'));

    if ($badge){

      if ($this->getRequest()->isPost()){

        $badge->delete();

        $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
        ));

      }

    }

    $this->renderScript('admin-badges/delete.tpl');

  }

  public function removePhotoAction()
  {
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'badges', 'action' => 'index'), 'admin_default', true);
    }


    $badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('badge_id'));

    if (!$badge){
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'badges', 'action' => 'index'), 'admin_default', true);
    }

    if ($this->_getParam('type') == 'icon'){
      $badge->removeIcon();
    } else {
      $badge->removePhoto();
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'badges', 'action' => 'edit', 'badge_id' => $badge->badge_id), 'admin_default', true);


  }

  public function membersAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');


    $this->view->formFilter = $formFilter = new Hebadge_Form_Admin_Badge_FilterMembers();

    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())){
      $values = $formFilter->getValues();
    }
    $this->view->formValues = $values;

    $table = Engine_Api::_()->getDbTable('users', 'user');
    $select = $table->select();

    if ($this->_getParam('username') != ""){
      $select->where('username LIKE ?', '%' . $this->_getParam('username') . '%');
    }
    if ($this->_getParam('displayname') != ""){
      $select->where('displayname LIKE ?', '%' . $this->_getParam('displayname') . '%');
    }
    if ($this->_getParam('email') != ""){
      $select->where('email LIKE ?', '%' . $this->_getParam('email') . '%');
    }
    $level_id = $this->_getParam('level_id');
    if (!empty($level_id)){
      $select->where('level_id = ?', intval($this->_getParam('level_id')));
    }

    $select->order('user_id DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(30);
  }


  public function editMemberAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->subject = $subject = Engine_Api::_()->getItem('user', $this->_getParam('id'));
    if (!$subject){
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
      ));
      return;
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();

    if ($request->getParam('badge')){

      if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
        return ;
      }


      $badge = Engine_Api::_()->getItem('hebadge_badge', $request->getParam('badge'));

      if ($badge){

        if ($request->getParam('enabled')){

          $badge->addMember($subject);
          $badge->setApprovedMember($subject);


          if (!Engine_Api::_()->getDbTable('notifications', 'activity')->getNotificationByObjectAndType($subject, $badge, 'hebadge_new')){
            Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($subject, $subject, $badge, 'hebadge_new');
          }

        } else {
          $badge->removeMember($subject);
        }

      }

      return;
    }


    $table = Engine_Api::_()->getDbTable('badges', 'hebadge');
    $this->view->paginator = $paginator = $table->getPaginator();
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $ids = array();
    foreach ($paginator->getCurrentItems() as $item){
      $ids[] = $item->getIdentity();
    }

    $this->view->members = $table->getOwnerMembersByBadgeIds($ids, $subject);


    $this->view->simple_name = 'hebadge_admin_member_badges';
    $this->view->params = array(
      'format' => 'smoothbox'
    );

  }

  public function enabledAction()
  {
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      return ;
    }


    $request = Zend_Controller_Front::getInstance()->getRequest();

    $badge = Engine_Api::_()->getItem('hebadge_badge', $request->getParam('badge_id'));
    if (!$badge){
      return ;
    }
    $badge->setFromArray(array('enabled' => $request->getParam('enabled')))->save();
  }


}