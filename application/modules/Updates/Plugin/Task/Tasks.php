<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Send.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Plugin_Task_Tasks extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    /**
     * @var $updatesTbl Updates_Model_DbTable_Updates
     * @var $campaignsTbl Updates_Model_DbTable_Campaigns
     * @var $settings Core_Api_Settings
     * @var $rowUpdate Updates_Model_Update
     * @var $rowCampaign Updates_Model_Campaign
     * @var $tasksTbl Updates_Model_DbTable_Tasks
     * @var $subscribersTbl Updates_Model_DbTable_Subscribers
     */
    $updatesTbl = Engine_Api::_()->getDbtable('updates', 'updates');
    $campaignsTbl = Engine_Api::_()->getDbtable('campaigns', 'updates');
		$settings = Engine_Api::_()->getApi('settings', 'core');
    $tasksTbl = Engine_Api::_()->getDbTable('tasks', 'updates');
    $now = Engine_Api::_()->updates()->getTimestamp();
    $rowUpdate = '';
    $rowCampaign = '';

    $subscribersTbl = Engine_Api::_()->getDbtable('subscribers', 'updates');
    $totUsersCount = ($settings->__get('updates.users.disabled')) ? 0 : $subscribersTbl->getTotalSubscribedUsers();
    $totSubsCount = ($settings->__get('updates.subscribers.disabled')) ? 0 : $subscribersTbl->getTotalSubscribedEmails();

    if (($totUsersCount + $totSubsCount) == 0) {
      return;
    }

    if ($settings->__get('updates.update.mode') == 'automatically' && $settings->__get('updates.update.time') <= $now)
    {
			if ( null === ($rowUpdate = $updatesTbl->prepareUpdate(array(), true))) {
        if( APPLICATION_ENV == 'development' ) {
          $this->getLog()->log('NULL value was returned while preparing new newsletter.', Zend_Log::WARN);
        }
			}
      else {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $tasksTb = Engine_Api::_()->getDbTable('tasks', 'updates');

        $activeTask = $tasksTbl->getActiveTask();
        if (!isset($activeTask)) {
          // adding task
          $tasksRow = $tasksTb->createRow();
          $tasksRow->type = 'updates';
          $tasksRow->creation_date = date('Y-m-d H:i:s', Engine_Api::_()->updates()->getTimestamp());
          $users_count = 0;
          $subscribers_count = 0;
          if (!$settings->__get('updates.users.disabled')) {
            $users_count = $subscribersTbl->getTotalSubscribedUsers();
          }
          if (!$settings->__get('updates.subscribers.disabled')) {
            $subscribers_count = $subscribersTbl->getTotalSubscribedEmails();
          }
          $tasksRow->total_recipients = (int)($users_count + $subscribers_count);
          $tasksRow->updcamp_id = $rowUpdate->update_id;

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

          $rowUpdate->sendUpdates( 10 );
        }
      }
  	}

    $activeTask = $tasksTbl->getActiveTask();

    if (isset($activeTask))
    {
      // SENDING UPDATES
      if ($activeTask->type == 'updates')
      {
        if( !($rowUpdate instanceof Updates_Model_Update) ) {
          $rowUpdate = $updatesTbl->fetchRow($updatesTbl->select()
            ->where('sending_finished = ?', 0)
            ->where('update_id=?', $activeTask->updcamp_id));
        }

        if( $rowUpdate instanceof Updates_Model_Update )
        {
          // Process
          try {
            set_time_limit (0);
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '150M');

            $rowUpdate->sendUpdates();

          } catch( Exception $e ) {
            if( APPLICATION_ENV == 'development' ) {
                $this->getLog()->log(sprintf('An error has occurred while sending newsletter [%d]:', $rowUpdate->getIdentity()) . $e->__toString(), Zend_Log::CRIT);
            }
          }

          $this->_setWasIdle(false);

        } else {
          $this->_setWasIdle(true);
        }
      }       // SENDING CAMPAIGNS
      elseif ($activeTask->type == 'campaign')
      {
        if( !($rowCampaign instanceof Updates_Model_Campaign) ) {
          $rowCampaign = $campaignsTbl->fetchRow($campaignsTbl->select()
            ->where('finished = ?', 0)
            ->where('campaign_id=?', $activeTask->updcamp_id));
        }

        if( $rowCampaign instanceof Updates_Model_Campaign )
        {
          // Process
          try {
            set_time_limit (0);
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '150M');

            $rowCampaign->sendCampaign();

          } catch( Exception $e ) {
            if( APPLICATION_ENV == 'development' ) {
                $this->getLog()->log(sprintf('An error has occurred while sending newsletter [%d]:', $rowCampaign->getIdentity()) . $e->__toString(), Zend_Log::CRIT);
            }
          }

          $this->_setWasIdle(false);

        } else {
          $this->_setWasIdle(true);
        }
      }
    }
    return;
  }

}