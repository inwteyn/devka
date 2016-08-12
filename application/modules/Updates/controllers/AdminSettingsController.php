<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function indexAction()
  {
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_settings');
      
		$this->view->form = $form = new Updates_Form_Admin_Setting();
    $form->setAttrib('class', '');

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {

      // Check license
      $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
      $product_result = $hecoreApi->checkProduct('updates');

      if (isset($product_result['result']) && !$product_result['result']) {
        $form->addError($product_result['message']);
        $this->view->headScript()->appendScript($product_result['script']);

        return;
      }
      $values = $form->getValues();

      if ($form->saveChanges($values))
      {
      	$form->addNotice('UPDATES_Changes have been successfully saved.');
      }
      else
      {
      	$form->addError('UPDATES_An error has occurred while saving changes!!!');
      }
    }
    
    //POPULATE PRESETS
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $update_mode = $settings->__get('updates.update.mode');
    $update_time = (int) $settings->__get('updates.update.time');
     
    $period = $settings->__get('updates.update.period');
    $hour = date('h', $update_time);
    $minute = date('i', $update_time);
    $am_pm = date('A', $update_time);
    $per_minute_items = $settings->__get('updates.perminut.itemnumber');
		    
    $form->populate(array('mode'=>$update_mode, 'period' => $period, 'hour' => $hour, 'minute' => $minute, 'am_pm' => $am_pm, 'per_minute_items'=>$per_minute_items));
  }
  
  
	public function levelAction()
  {
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_settings');

    $level_id = $this->_getParam('id', 1);

    $this->view->form = $form = new Updates_Form_Admin_Level();

    $form->setAttrib('class', '');
    $form->level_id->setValue($level_id);
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    if (!$this->getRequest()->isPost()) 
    {
      if (null !== $level_id)
      {
        $form->populate($permissionsTable->getAllowed('updates', $level_id, array_keys($form->getValues())));
        
        return;
      }

      return;
    }

    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) 
    {
      return;
    }

    // Process

    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try 
    {
      $permissionsTable->setAllowed('updates', $level_id, $values);
      // Commit
      $db->commit();
    }
    catch (Exception $e) 
    {
      $db->rollBack();
      throw $e;
    }
  }}