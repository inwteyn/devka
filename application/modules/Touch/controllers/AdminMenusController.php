<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminMenusController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_AdminMenusController extends Core_Controller_Action_Admin
{
  protected $_menus;

  protected $_enabledModuleNames;

  public function init()
  {
    // Get list of menus
    $menusTable = Engine_Api::_()->getDbtable('menus', 'touch');
    $menusSelect = $menusTable->select()
      ->where('type IN(?)', array('standard', 'hidden'));
    $this->view->menus = $this->_menus = $menusTable->fetchAll($menusSelect);

    $this->_enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
  }
  
  public function indexAction()
  {
    $this->view->name = $name = $this->_getParam('name', 'core_main');

    // Get list of menus
    $menus = $this->_menus;

    // Check if selected menu is in list
    $selectedMenu = $menus->getRowMatching('name', $name);
    if( null === $selectedMenu ) {
      throw new Core_Model_Exception('Invalid menu name');
    }
    $this->view->selectedMenu = $selectedMenu;

    // Make select options
    $menuList = array();
    foreach( $menus as $menu ) {
      $menuList[$menu->name] = $this->view->translate($menu->title);
    }
    $this->view->menuList = $menuList;

    // Get menu items
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'touch');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('menu = ?', $name)
      ->order('order');
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItems = $menuItems = $menuItemsTable->fetchAll($menuItemsSelect);
  }

  public function createAction()
  {
    // Get uri list of touch pages
    $pagesElement = new Engine_Form_Element_Select('uri_pages');
    $pagesElement->setLabel('Pages');
    $pagesElement->setMultiOptions(array('' => 'Nothing selected'));
    $pagesElement->setOrder(2);

    $pagesTable = Engine_Api::_()->getDbTable('pages', 'touch');
    $db = $pagesTable->getAdapter();
    $db->beginTransaction();

    $select = $pagesTable->select()->where('custom = ?', 1);
    $pagesSelected = $db->query($select)->fetchAll();

    foreach($pagesSelected as $pages)
    {
      $pagesElement->addMultiOption('pages/'.$pages['url'], $pages['url']);
    }

    $this->view->name = $name = $this->_getParam('name');

    // Get list of menus  
    $menus = $this->_menus;

    // Check if selected menu is in list
    $selectedMenu = $menus->getRowMatching('name', $name);
    if( null === $selectedMenu ) {
      throw new Core_Model_Exception('Invalid menu name');
    }
    $this->view->selectedMenu = $selectedMenu;

    // Get form
    $form = new Touch_Form_ItemCreate();
    $form->addElement($pagesElement);
    $this->view->form = $form;




    // Check stuff
    if( !$this->getRequest()->isPost() )
    {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Save
    $values = $form->getValues();

    $label = $values['label'];
    unset($values['label']);
    unset($values['uri_pages']);
    unset($values['uri_type']);

    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'touch');

    $db = $menuItemsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      $menuItem = $menuItemsTable->createRow();

    $menuItem->label = $label;
    $menuItem->params = $values;
    $menuItem->menu = $name;
    $menuItem->module = 'core'; // Need to do this to prevent it from being hidden
    $menuItem->plugin = '';
    $menuItem->submenu = '';
    $menuItem->custom = 1;
    $menuItem->save();

    $menuItem->name = 'custom_' . sprintf('%d', $menuItem->id);
    $menuItem->save();
      $db->commit();
    }catch(Exception $e)
                     {throw $e; $db->rollback();}



    $this->view->status = true;
    $this->view->form = null;
  }

  public function editAction()
  {
    // Get uri list of touch pages
    $pagesElement = new Engine_Form_Element_Select('uri_pages');
    $pagesElement->setLabel('Pages');
    $pagesElement->setMultiOptions(array('' => 'Nothing selected'));
    $pagesElement->setOrder(2);

    $pagesTable = Engine_Api::_()->getDbTable('pages', 'touch');
    $db = $pagesTable->getAdapter();
    $db->beginTransaction();

    $select = $pagesTable->select()->where('custom = ?', 1);
    $pagesSelected = $db->query($select)->fetchAll();

    foreach($pagesSelected as $pages)
    {
      $pagesElement->addMultiOption('pages/'.$pages['url'], $pages['url']);
    }



    $this->view->name = $name = $this->_getParam('name');

    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'touch');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('name = ?', $name);
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

    if( !$menuItem ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $form = new Touch_Form_ItemCreate();
    $form->setTitle('Edit Menu Item');
    $form->submit->setLabel('Edit Menu Item');
    $form->addElement($pagesElement);
    $this->view->form = $form;
    // Make safe
    $menuItemData = $menuItem->toArray();
    if( isset($menuItemData['params']) && is_array($menuItemData['params']))
      $menuItemData = array_merge($menuItemData, $menuItemData['params']);
    if( !$menuItem->custom )
    {
      $form->removeElement('uri');
      $form->removeElement('uri_pages');
      $form->removeElement('uri_type');
    }

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      $form->populate($menuItemData);
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Save
    $values = $form->getValues();

    unset($values['uri_type']);
    unset($values['uri_pages']);

    
    $menuItem->label = $values['label'];
    unset($values['label']);

    $db = $menuItemsTable->getAdapter();
    $db->beginTransaction();
    try
    {
      if( $menuItem->custom ) {
        $menuItem->params = $values;
      } else if( !empty($values['target']) ) {
        $menuItem->params = array_merge($menuItem->params, array('target' => $values['target']));
      }
      if( isset($values['enabled']) ) {
        $menuItem->enabled = (bool) $values['enabled'];
      }
      $menuItem->save();
      $db->commit();
    }
    catch(Exception $e)
                    {
                      $db->rollback();
                      throw $e;
                    }
    $this->view->status = true;
    $this->view->form = null;
  }

  public function deleteAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'touch');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('name = ?', $name)
      ->order('order ASC');
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

    if( !$menuItem || !$menuItem->custom ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_ItemDelete();
    
    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $menuItem->delete();

    $this->view->form = null;
    $this->view->status = true;
  }

  public function orderAction()
  {
    if (_ENGINE_ADMIN_NEUTER) {
      return $this->_helper->viewRenderer->setNoRender(true);
    }
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    $table = $this->_helper->api()->getDbtable('menuItems', 'touch');
    $menuitems = $table->fetchAll($table->select()->where('menu = ?', $this->getRequest()->getParam('menu')));
    foreach ($menuitems as $menuitem)
    {
      $menuitem->order = $this->getRequest()->getParam('admin_menus_item_'.$menuitem->name);
      $menuitem->save();
    }
    return;
  }

}
