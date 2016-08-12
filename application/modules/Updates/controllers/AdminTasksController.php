<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminTasksController.php 2012-03-09 11:18 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_AdminTasksController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
  	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_tasks');


    //ACTIVE TASKS
    $tasksTbl = Engine_Api::_()->getDbtable('tasks', 'updates');
		$select = $tasksTbl->select()
      ->setIntegrityCheck(false)
      ->from(array($tasksTbl->info('name')))
      ->order('task_id DESC');

		$paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber( 1 );
    $this->view->paginator = $paginator;
    $this->view->paginator_pages = $paginator->getPages();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->isEnabledAutoRefresh = $settings->__get('updates.autorefresh.enabled');
  }

  public function paginationAction()
  {
    $tasksTbl = Engine_Api::_()->getDbtable('tasks', 'updates');
		$select = $tasksTbl->select()
      ->setIntegrityCheck(false)
      ->from(array($tasksTbl->info('name')))
      ->order('task_id DESC');

		$paginator = Zend_Paginator::factory($select);

    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber( $this->_getParam( 'page', 1));

    $this->view->html = $this->view->ajaxPaginator($paginator, 'tasks_paginator');
  }

  public function cancelAction()
  {
    $task_id = $this->_getParam('task_id');
    $tasksTbl = Engine_Api::_()->getDbtable('tasks', 'updates');

    $paginator = Zend_Paginator::factory($tasksTbl->cancelTask($task_id));
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber( $this->_getParam( 'tasks_page', 1));

    $this->view->html = $this->view->ajaxPaginator($paginator, 'tasks_paginator');
  }

  public function restartAction()
  {
    $task_id = $this->_getParam('task_id');
    $tasksTbl = Engine_Api::_()->getDbtable('tasks', 'updates');

    $paginator = Zend_Paginator::factory($tasksTbl->restartTask($task_id));
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber( $this->_getParam( 'tasks_page', 1));

    $this->view->html = $this->view->ajaxPaginator($paginator, 'tasks_paginator');
  }

  public function deleteAction()
  {
    $this->view->deleteTaskForm = $deleteTaskForm = new Updates_Form_Admin_Tasks_Delete('single');

    if( $this->getRequest()->isPost())
    {
      $task_id = $this->_getParam('task_id');
      $tasksTbl = Engine_Api::_()->getDbtable('tasks', 'updates');
      $tasksTbl->deleteTask($task_id);

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => TRUE,
          'parentRefresh' => TRUE,
          'format'=> 'smoothbox',
          'messages' => array($this->view->translate(array('UPDATES_Task has been deleted successfully')))
      ));
    }
  }

  public function deleteSelectedAction()
  {
    $this->view->deleteTaskForm = $deleteTaskForm = new Updates_Form_Admin_Tasks_Delete('multiple');

    if( $this->getRequest()->isPost())
    {
      $tasks = $this->_getParam('tasks');

      $tasks_str = '0';
      foreach($tasks as $item) {
        $tasks_str .= ',' . $item;
      }

      $tasksTbl = Engine_Api::_()->getDbtable('tasks', 'updates');
      $tasksTbl->delete(array("task_id IN ({$tasks_str})"));

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => TRUE,
          'parentRefresh' => TRUE,
          'format'=> 'smoothbox',
          'messages' => array($this->view->translate(array('UPDATES_Selected tasks have been deleted successfully')))
      ));
    }
  }

  public function enableAutoRefreshAction()
  {
    $isEnable = $this->_getParam('isEnable',1);
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if($isEnable) {
      $settings->__set('updates.autorefresh.enabled', 1);
    }
    else {
      $settings->__set('updates.autorefresh.enabled', 0);
    }
  }

}