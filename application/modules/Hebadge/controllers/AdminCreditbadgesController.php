<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminCreditbadgesController.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_AdminCreditbadgesController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');

    $table = Engine_Api::_()->getDbTable('creditbadges', 'hebadge');

    $this->view->paginator = $paginator = $table->getPaginator(null, true);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

  }

  public function createAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');

    $this->view->form = $form = new Hebadge_Form_Admin_Creditbadge_Create();

    // populate icon
    $form->getElement('photo')->getDecorator('hebadgeCreditPhoto')->setOptions(array('type' => 'thumb.profile'));
    $form->getElement('icon')->getDecorator('hebadgeCreditPhoto')->setOptions(array('type' => 'thumb.icon'));

    if (!$this->getRequest()->isPost()){
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())){
      return;
    }

    $values = $form->getValues();

    $table = Engine_Api::_()->getDbTable('creditbadges', 'hebadge');

    $badge = $table->createRow();
    $badge->setFromArray($values);
    $badge->save();

    if (!empty($values['photo'])){
      $badge->setPhoto($form->photo);
    }
    if (!empty($values['icon'])){
      $badge->setIcon($form->icon);
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'creditbadges', 'action' => 'edit', 'creditbadge_id' => $badge->creditbadge_id), 'admin_default', true);

  }

  public function editAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');

    $this->view->form = $form = new Hebadge_Form_Admin_Creditbadge_Edit();

    $badge = Engine_Api::_()->getItem('hebadge_creditbadge', $this->_getParam('creditbadge_id'));

    if (!$badge){
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'creditbadges', 'action' => 'index'), 'admin_default', true);
    }

    $form->populate($badge->toArray());

    // populate icon
    if ($badge->photo_id){
      $form->getElement('photo')->getDecorator('hebadgeCreditPhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgeCreditPhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.icon'));
    } else {
      $form->getElement('photo')->getDecorator('hebadgeCreditPhoto')->setOptions(array('type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgeCreditPhoto')->setOptions(array('type' => 'thumb.icon'));
    }

    if (!$this->getRequest()->isPost()){
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())){
      return;
    }

    $values = $form->getValues();

    $badge->setFromArray($values);
    $badge->save();

    if (!empty($values['photo'])){
      $badge->setPhoto($form->photo);
    }
    if (!empty($values['icon'])){
      $badge->setIcon($form->icon);
    }

    // set after submit
    if ($badge->photo_id){
      $form->getElement('photo')->getDecorator('hebadgeCreditPhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgeCreditPhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.icon'));
    } else {
      $form->getElement('photo')->getDecorator('hebadgeCreditPhoto')->setOptions(array('type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgeCreditPhoto')->setOptions(array('type' => 'thumb.icon'));
    }

    $form->addNotice('Your changes have been saved.');

  }

  public function removeAction()
  {
    $badge = Engine_Api::_()->getItem('hebadge_creditbadge', $this->_getParam('creditbadge_id'));

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

    $this->renderScript('admin-creditbadges/delete.tpl');

  }

  public function removePhotoAction()
  {
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'creditbadges', 'action' => 'index'), 'admin_default', true);
    }

    $badge = Engine_Api::_()->getItem('hebadge_creditbadge', $this->_getParam('creditbadge_id'));

    if (!$badge){
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'creditbadges', 'action' => 'index'), 'admin_default', true);
    }

    if ($this->_getParam('type') == 'icon'){
      $badge->removeIcon();
    } else {
      $badge->removePhoto();
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'creditbadges', 'action' => 'edit', 'creditbadge_id' => $badge->creditbadge_id), 'admin_default', true);

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

  public function enabledAction()
  {
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      return ;
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $badge = Engine_Api::_()->getItem('hebadge_creditbadge', $request->getParam('badge_id'));
    if (!$badge){
      return ;
    }
    $badge->setFromArray(array('enabled' => $request->getParam('enabled')))->save();
  }




}