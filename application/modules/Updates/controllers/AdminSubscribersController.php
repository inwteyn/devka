<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSubscribersController.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_AdminSubscribersController extends Core_Controller_Action_Admin
{
	
	public function indexAction()
  {
  	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_subscriber');

    $this->view->formFilter = $formFilter = new Updates_Form_Admin_Subscribers_Filter();
    $this->view->formNewSubscriber = $formNewSubscriber = new Updates_Form_Admin_Subscribers_Newsubscriber();
    
    $page = $this->_getParam('page',1);

    $table = $this->_helper->api()->getDbtable('subscribers', 'updates');
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
      'order' => 'subscriber_id',
      'order_direction' => 'DESC',
    ), $values);
    
    $this->view->assign($values);

    // Set up select info
    $select->order(( !empty($values['order']) 
    		? $values['order'] 
    		: 'subscriber_id' ) . ' ' . ( !empty($values['order_direction']) 
    																? $values['order_direction'] 
    																: 'DESC' ));

    if( !empty($values['name']) )
    {
      $select->where('name LIKE ?', '%' . $values['name'] . '%');
    }

    if( !empty($values['email_address']) )
    {
      $select->where('email_address LIKE ?', '%' . $values['email_address'] . '%');
    }

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator = $paginator->setCurrentPageNumber( $page );
	
    $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
  }
  
  public function addAction()
  {
  	$this->view->form = $form = new Updates_Form_Admin_Subscribers_Add();

  	if( $this->getRequest()->isPost() )
    {  
    	$subscriberTb = $this->_helper->api()->getDbtable('subscribers', 'updates');
    	$values = $this->_getAllParams();
    	$emailForm = new Updates_Form_Widgets_Subscribe();
  		$email_box = $emailForm->getElement('updates_email_box');
  		
    	$i = 1;
    	$added = 0;
    	
    	while (isset($values['name'.$i]) && isset($values['email_address'.$i]))
    	{
    		if ($email_box->isValid($values['email_address'.$i]))
    		{
    			$subscriber = $subscriberTb->createRow();
    			$subscriber->email_address = $values['email_address'.$i];
    			$subscriber->name = $values['name'.$i];
					$subscriber->user_id = $this->_helper->api()->user()->getViewer()->getIdentity();
          if ($subscriber->save())
          {
    			  $added++;
          }
    		}
    		$i++;
    	}
    	
    	if ($added > 0)
    	{
    		$msg = 'UPDATES_Subscriber(s) Successfully Added.';
    	}
    	else 
    	{
    		$msg = 'UPDATES_An error has occurred!!!';
    	}
    	
	    $this->_forward('success', 'utility', 'core', array(
      	'smoothboxClose' => TRUE,
      	'parentRefresh' => TRUE,
      	'format'=> 'smoothbox',
      	'messages' => array($this->view->translate($msg)),
    	));
    }
    
  }
  
  public function importAction()
  {
  	$this->view->form = $form = new Updates_Form_Admin_Subscribers_Import();

  	if( $this->getRequest()->isPost() )
    {
    	$contacts = $form->uploadcsv();
    	$subscriberTb = $this->_helper->api()->getDbtable('subscribers', 'updates');
    	$emailForm = new Updates_Form_Widgets_Subscribe();

    	$added = 0;
    	
    	if (count($contacts) > 0)
    	{
	    	foreach ($contacts as $email=>$name)
	    	{
	   			$subscriber =  $subscriberTb->createRow();
					$subscriber->email_address = $email;
          $subscriber->name = $name;
				  $subscriber->user_id = $this->_helper->api()->user()->getViewer()->getIdentity();
          if ($subscriber->save())
          {
	   			  $added++;
          }
	    	}
    	}

			$parentRefresh = false;
    	if ($added > 0)
    	{
    		$msg = 'UPDATES_Subscriber(s) Successfully Added.';
				$parentRefresh = TRUE;
    	}
    	elseif (count($contacts)>0)
			{
				$msg = 'UPDATES_Subscribers are already members.';
			}
			else
    	{
    		$msg = 'UPDATES_An error has occurred!!!';
    	}
    	
    	$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => TRUE,
				'parentRefresh' => $parentRefresh,
				'format'=> 'smoothbox',
				'messages' => array($this->view->translate($msg))
      ));
    }
  }

  public function editAction()
  {
    $id = $this->_getParam('id', null);
    $this->view->subscriber = $subscriber = $this->_helper->api()->getDbtable('subscribers', 'updates')->getSubscriber($id);
    $this->view->form = $form = new Updates_Form_Admin_Subscribers_Edit();
		$form->populate($subscriber->toArray());

    // Posting form
    if( $this->getRequest()->isPost() )
    {
      if( $form->isValid($this->getRequest()->getPost()) )
      {
        $subscriber->setFromArray($form->getValues());
        
        $db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
      	$db->beginTransaction();
        try {
        	$subscriber->save();
        	$db->commit();
        	$edited_msg = 'UPDATES_Subscriber Edited.';
        }
        catch (Exception $e) 
        {
        	$db->rollback();
        	$edited_msg = 'UPDATES_An error has occurred!!!';
        }
        
        $this->_forward('success', 'utility', 'core', array(
		      'smoothboxClose' => true,
		      'parentRefresh' => true,
		      'format'=> 'smoothbox',
		      'messages' => array($this->view->translate($edited_msg))
		     ));
      }
      
    }

    // Initialize data
    else
    {
      foreach( $form->getElements() as $name => $element )
      {
        if( _ENGINE_ADMIN_NEUTER && $name == 'email_address' ) continue;
        if( isset($user->$name) )
        {
          $element->setValue($user->$name);
        }
      }
    }
  }

  public function deleteAction()
  {
    $id = $this->_getParam('id', null);
    $this->view->subscriber = $subscriber = $this->_helper->api()->getDbtable('subscribers', 'updates')->getSubscriber($id);
    $this->view->form = $form = new Updates_Form_Admin_Subscribers_Delete();

    if ($this->getRequest()->isPost()) 
    {
      $db = $this->_helper->api()->getDbtable('subscribers', 'updates')->getAdapter();
      $db->beginTransaction();

      try
      {
      	$subscriberTb = $this->_helper->api()->getDbtable('subscribers', 'updates');
      	$select  = $subscriberTb->select()->where('subscriber_id = ?', $id);
      	$subscriber = $subscriberTb->fetchRow($select);
        $subscriber->delete();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      
      $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array($this->view->translate('UPDATES_Subscriber deleted.'))
      ));
    }
  }

  public function deleteSelectedAction()
  {
    $this->view->deleteSelectedForm = $deleteSelectedForm = new Updates_Form_Admin_Subscribers_DeleteSelected();

    if ($this->getRequest()->isPost())
    {
      $subscribers = $this->_getParam('subscribers');
      $subscribers_str = '0';
      foreach($subscribers as $item) {
        $subscribers_str .= ',' . $item;
      }

      $subscribersTbl = Engine_Api::_()->getDbTable('subscribers', 'updates');
      $subscribersTbl->delete(array("subscriber_id IN ({$subscribers_str})"));

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => TRUE,
          'parentRefresh' => TRUE,
          'format'=> 'smoothbox',
          'messages' => array($this->view->translate(array('UPDATES_Selected subscribers have been deleted successfully')))
      ));
    }
  }
}