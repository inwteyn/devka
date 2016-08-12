<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminPagebadgesController.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_AdminPageBadgesController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');


    $table = Engine_Api::_()->getDbTable('pagebadges', 'hebadge');

    $this->view->paginator = $paginator = $table->getPaginator(null, true);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

  }

  public function createAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');


    $this->view->form = $form = new Hebadge_Form_Admin_Pagebadge_Create();

    // populate icon
    $form->getElement('photo')->getDecorator('hebadgePagePhoto')->setOptions(array('type' => 'thumb.profile'));
    $form->getElement('icon')->getDecorator('hebadgePagePhoto')->setOptions(array('type' => 'thumb.icon'));

    if (!$this->getRequest()->isPost()){
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())){
      return;
    }

    $values = $form->getValues();

    $table = Engine_Api::_()->getDbTable('pagebadges', 'hebadge');

    $badge = $table->createRow();
    $badge->setFromArray($values);
    $badge->save();

    if (!empty($values['photo'])){
      $badge->setPhoto($form->photo);
    }
    if (!empty($values['icon'])){
      $badge->setIcon($form->icon);
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'edit', 'pagebadge_id' => $badge->pagebadge_id), 'admin_default', true);

  }

  public function editAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');

    $this->view->form = $form = new Hebadge_Form_Admin_Pagebadge_Edit();

    $badge = Engine_Api::_()->getItem('hebadge_pagebadge', $this->_getParam('pagebadge_id'));

    if (!$badge){
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'index'), 'admin_default', true);
    }

    $form->populate($badge->toArray());

    // populate icon
    if ($badge->photo_id){
      $form->getElement('photo')->getDecorator('hebadgePagePhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgePagePhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.icon'));
    } else {
      $form->getElement('photo')->getDecorator('hebadgePagePhoto')->setOptions(array('type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgePagePhoto')->setOptions(array('type' => 'thumb.icon'));
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
      $form->getElement('photo')->getDecorator('hebadgePagePhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgePagePhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.icon'));
    } else {
      $form->getElement('photo')->getDecorator('hebadgePagePhoto')->setOptions(array('type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgePagePhoto')->setOptions(array('type' => 'thumb.icon'));
    }

    $form->addNotice('Your changes have been saved.');

  }

  public function removeAction()
  {
    $badge = Engine_Api::_()->getItem('hebadge_pagebadge', $this->_getParam('pagebadge_id'));

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

    $this->renderScript('admin-pagebadges/delete.tpl');

  }

  public function removePhotoAction()
  {
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'index'), 'admin_default', true);
    }

    $badge = Engine_Api::_()->getItem('hebadge_pagebadge', $this->_getParam('pagebadge_id'));

    if (!$badge){
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'index'), 'admin_default', true);
    }

    if ($this->_getParam('type') == 'icon'){
      $badge->removeIcon();
    } else {
      $badge->removePhoto();
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'edit', 'pagebadge_id' => $badge->pagebadge_id), 'admin_default', true);

  }


  public function membersAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');



    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $select = $table->select();

    $prefix = $table->getTablePrefix();

    $select
        ->setIntegrityCheck(false)
        ->from($prefix . 'page_pages')
        ->joinLeft($prefix . 'page_fields_values', $prefix . "page_fields_values.item_id = " . $prefix . "page_pages.page_id")
        ->joinLeft($prefix . 'page_fields_options', $prefix . "page_fields_options.option_id = " . $prefix . "page_fields_values.value AND " . $prefix . "page_fields_options.field_id = 1", array("category" => $prefix . "page_fields_options.label"))
        ->joinLeft( $prefix . 'users', $prefix . 'users.user_id = ' . $prefix . 'page_pages.user_id', array() )
    ;

    $this->view->filterForm = $filterForm = new Hebadge_Form_Admin_Pagebadge_FilterMembers();
    $page = $this->_getParam('page', 1);

    $values = array();
    if ($filterForm->isValid($this->_getAllParams())){
      $values = $filterForm->getValues();
    }

    foreach ($values as $key => $value){
      if (null === $value){
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'page_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'page_id') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC'));

    if (!empty($values['title'])){
      $select->where($prefix . 'page_pages.title LIKE ?', '%' . $values['title'] . '%');
    }

    if (!empty($values['category']) && $values['category'] != -1){
      $select
          ->where($prefix . 'page_fields_values.field_id = 1 AND ' . $prefix . 'page_fields_values.value = ?', $values['category']);
    } elseif (isset($values['category']) && $values['category'] == -1) {
      $select
          ->where($prefix . 'page_fields_options.label IS NULL');
    }

    if (isset($values['approved']) && $values['approved'] != -1){
      $select->where($prefix . 'page_pages.approved = ?', $values['approved']);
    }

    if (isset($values['featured']) && $values['featured'] != -1){
      $select->where($prefix . 'page_pages.featured = ?', $values['featured']);
    }

    if (Engine_Api::_()->getApi('settings', 'core')->__get('page.package.enabled')){
      if (isset($values['package']) && $values['package'] != -1){
        $select->where($prefix . 'page_pages.package_id = ?', $values['package']);
      }
    }

    $select->where($prefix . "page_pages.name <> 'footer' AND " . $prefix . "page_pages.name <> 'header' AND " . $prefix . "page_pages.name <> 'default'");
    $select->group($prefix . "page_pages.page_id");

    $pageowner = $this->_getParam('pageowner');
    if (!empty($pageowner)){
      $select->where( "" . $prefix. "users.user_id LIKE ? OR " .$prefix. "users.username LIKE ? OR " .$prefix. "users.displayname LIKE ? OR " .$prefix. "users.email LIKE ?", '%' . $pageowner . '%');
    }


    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(30);

    $this->view->formValues = array_filter($values);

  }


  public function editMemberAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->subject = $subject = Engine_Api::_()->getItem('page', $this->_getParam('id'));
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

      $badge = Engine_Api::_()->getItem('hebadge_pagebadge', $this->_getParam('badge'));

      if ($badge){
        if ($request->getParam('enabled')){

          $badge->addMember($subject);
          $badge->setApprovedMember($subject);


          if (!Engine_Api::_()->getDbTable('notifications', 'activity')->getNotificationByObjectAndType($subject, $badge, 'hebadgepage_new')){
            Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($subject->getOwner(), $subject->getOwner(), $badge, 'hebadgepage_new');

          }

        } else {
          $badge->removeMember($subject);
        }
      }

      return;
    }


    $table = Engine_Api::_()->getDbTable('pagebadges', 'hebadge');
    $this->view->paginator = $paginator = $table->getPaginator();
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $ids = array();
    foreach ($paginator->getCurrentItems() as $item){
      $ids[] = $item->getIdentity();
    }

    $this->view->members = $table->getOwnerMembersByBadgeIds($ids, $subject);


    $this->view->simple_name = 'hebadge_admin_pagemember_badges';
    $this->view->params = array(
      'format' => 'smoothbox'
    );

  }


  public function requestsAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');


    $memberTable = Engine_Api::_()->getDbTable('pagemembers', 'hebadge');
    $select = $memberTable->select()
        ->where('approved = 0')
        ->order('creation_date DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page'));


    $page_ids = array();
    $badge_ids = array();
    foreach ($paginator->getCurrentItems() as $item){
      $page_ids[] = $item->page_id;
      $badge_ids[] = $item->pagebadge_id;
    }

    $badges = array();
    foreach (Engine_Api::_()->hebadge()->getTableItems(Engine_Api::_()->getDbTable('pagebadges', 'hebadge'), $badge_ids) as $item){
      $badges[$item->getIdentity()] = $item;
    }
    $pages = array();
    foreach (Engine_Api::_()->hebadge()->getTableItems(Engine_Api::_()->getDbTable('pages', 'page'), $page_ids) as $item){
      $pages[$item->getIdentity()] = $item;
    }

    $this->view->pages = $pages;
    $this->view->badges = $badges;

  }


  public function requestApprovedAction()
  {
    $badge = Engine_Api::_()->getItem('hebadge_pagebadge', $this->_getParam('badge_id'));
    $subject = Engine_Api::_()->getItem('page', $this->_getParam('page_id'));

    if (!$badge || !$subject){
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'requests'), 'admin_default', true);
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();

    if ($request->getParam('approved')){

      if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
        return ;
      }

      $badge->addMember($subject);
      $badge->setApprovedMember($subject);

      if (!Engine_Api::_()->getDbTable('notifications', 'activity')->getNotificationByObjectAndType($subject->getOwner(), $badge, 'hebadgepage_new')){
        Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($subject->getOwner(), $subject->getOwner(), $badge, 'hebadgepage_new');
      }

    } else {
      $badge->removeMember($subject);
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'requests'), 'admin_default', true);


  }


  public function enabledAction()
  {
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      return ;
    }


    $request = Zend_Controller_Front::getInstance()->getRequest();

    $badge = Engine_Api::_()->getItem('hebadge_pagebadge', $request->getParam('badge_id'));
    if (!$badge){
      return ;
    }
    $badge->setFromArray(array('enabled' => $request->getParam('enabled')))->save();
  }

}