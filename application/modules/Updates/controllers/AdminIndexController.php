<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminGeneralController.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_AdminIndexController extends Core_Controller_Action_Admin
{
	public function indexAction()
  {
    /**
     *  @var  $subscriberTb Updates_Model_DbTable_Subscribers
     */

    // if demoadmin
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      $this->view->engine_admin_neuter = true;
    }

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_general');

    $subscriberTb = Engine_Api::_()->getDbtable('subscribers', 'updates');
    $campaignsTb = Engine_Api::_()->getDbtable('campaigns', 'updates');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->mailService = $mailService = $settings->__get('updates.mailservice');

    if ($mailService == 'mailchimp') {
      include_once 'application/modules/Updates/Api/MCAPI.class.php';
      $list_id = $settings->__get('updates.mailchimp.listid');
      $apiKey = $settings->__get('updates.mailchimp.apikey');
      $api = new MCAPI($apiKey);
      $mailChimpResult = $api->listMembers($list_id, 'subscribed');
      $mailChimpMembers = $mailChimpResult['data'];
      $this->view->usersCount = count($mailChimpMembers);
    }
    else {
      $this->view->usersCount = $subscriberTb->getTotalSubscribedUsers();
      $this->view->subscribersCount = $subscriberTb->getTotalSubscribedEmails();
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->mode = $mode = $settings->__get('updates.update.mode');
    $this->view->last_sent_time = $last_sent_time = $settings->__get('updates.update.last.time');
      $this->view->next_send_time = $next_send_time = $settings->__get('updates.update.time');
    $this->view->users_disabled = $users_disabled = $settings->__get('updates.users.disabled');
    $this->view->subscribers_disabled = $subscribers_disabled = $settings->__get('updates.subscribers.disabled');

    if (null !== ($totalActiveCampaigns = $campaignsTb->getTotalActiveCampaigns()))
    {
      $this->view->totalActiveCampaigns = $totalActiveCampaigns->toArray();
    }

      
    if (null !== ($totalFutureScheduledCampaigns = $campaignsTb->getTotalFutureScheduleCampaigns()))
    {
      $this->view->totalFutureScheduledCampaigns = $totalFutureScheduledCampaigns->toArray();
    }
    
    $this->view->nextSendScheduleCampaign = $nextSendScheduleCampaign = $campaignsTb->getNextSendScheduleCampaign();
    $this->view->lastSentScheduleCampaign = $lastSentScheduleCampaign = $campaignsTb->getLastSentScheduleCampaign();
  }
  
  public function sendAction()
  {
  	$this->view->form = $form = new Updates_Form_Admin_Send();
  	if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      /**
       * @var $updatesTbl Updates_Model_DbTable_Updates
       * @var $row Updates_Model_Update
       */
			$updatesTbl = Engine_Api::_()->getDbtable('updates', 'updates');

			if ( null === ($row = $updatesTbl->prepareUpdate() )) {

				$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => false,
					'format'=> 'smoothbox',
					'messages' => array($this->view->translate('No updates has been found. Please, try again later.')),
				));
        return;
			}

      $subscribersTbl = Engine_Api::_()->getDbtable('subscribers', 'updates');
      $tasksTb = Engine_Api::_()->getDbTable('tasks', 'updates');

      // adding task
      $tasksRow = $tasksTb->createRow();
      $tasksRow->type = 'updates';
      $tasksRow->creation_date = new Zend_Db_Expr('NOW()');
      $users_count = 0;
      $subscribers_count = 0;

      $settings = Engine_Api::_()->getApi('settings', 'core');
      $mailService = $settings->__get('updates.mailservice');

      if ($mailService == 'mailchimp') {
        include_once 'application/modules/Updates/Api/MCAPI.class.php';
        $apiKey = $settings->__get('updates.mailchimp.apikey');
        $api = new MCAPI($apiKey);
        $list_id = $settings->__get('updates.mailchimp.listid');
        $mailChimpResult = $api->listMembers($list_id, 'subscribed');
        $mailChimpMembers = $mailChimpResult['data'];
        $tasksRow->total_recipients = count($mailChimpMembers);
      }
      else {
        if (!$settings->__get('updates.users.disabled')) {
          $users_count = $subscribersTbl->getTotalSubscribedUsers();
        }
        if (!$settings->__get('updates.subscribers.disabled')) {
          $subscribers_count = $subscribersTbl->getTotalSubscribedEmails();
        }
        $tasksRow->total_recipients = (int)($users_count + $subscribers_count);
      }

      $tasksRow->updcamp_id = $row->update_id;

      // Verify mail template type
      $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
      $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', 'updates'));
      if (null === $mailTemplate) {
        $subject = '';
      }
      else {
        $translate = Zend_Registry::get('Zend_Translate');
        $mailForSendGrid = new Updates_Model_Mail();
        $recipientLanguage = $translate->getLocale();

        // Get subject
        $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
        $subject = (string)$mailForSendGrid->translate($subjectKey, $recipientLanguage);
      }
      $tasksRow->subject = $subject;

      $tasksRow->save();

      set_time_limit (0);
      ini_set('max_execution_time', 0);
      ini_set('memory_limit', '150M');

      // Send 10 emails for the first time
      $data = $row->sendUpdates( 10 );

    	$this->_forward('success', 'utility', 'core', array(
      	'smoothboxClose' => TRUE,
      	'parentRefresh' => TRUE,
      	'format'=> 'smoothbox',
      	'messages' => array($this->view->translate(array('UPDATES_Update has been sent successfully to %s reciever',
    																										 'Update has been sent  successfully to %s recievers', $row->sent), $row->sent)),
    	));
    }
  }

	public function recipientsAction()
  {
		$recipients = $this->_getParam('recipients');
		$operation = $this->_getParam('operation');

		$settings = Engine_Api::_()->getApi('settings', 'core');
		$disabled = ($operation == 'disable')? 1:0;
		$result = NULL;

		if ($recipients == 'users') {
			$settings->__set('updates.users.disabled', $disabled);
			$result = $settings->__get('updates.users.disabled');
		} elseif ($recipients == 'subscribers') {
			$settings->__set('updates.subscribers.disabled', $disabled);
			$result = $settings->__get('updates.subscribers.disabled');
		}

		$this->view->result = $result;
	}
}
