 <?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Updates.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Model_Update extends Core_Model_Item_Abstract
{
  protected $_type = 'update';

  /**
   * @param int $limit
   * @return array
   */
  public function sendUpdates($limit = 0)
  {
    if ($this->sending_finished != 0) {
      return;
    }

    /**
     * @var $settings Core_Api_Settings
     * @var $table Updates_Model_DbTable_Subscribers
     * @var $tasksTbl Updates_Model_DbTable_Tasks
     */

		$settings = Engine_Api::_()->getApi('settings', 'core');
    $subscribersTbl = Engine_Api::_()->getDbtable('subscribers', 'updates');

    $tasksTbl = Engine_Api::_()->getDbTable('tasks','updates');
    $tasksRow = $tasksTbl->getCurrentTask($this->update_id,'updates');

    $sent = $tasksRow->sent;
    $totalRecipients = $tasksRow->total_recipients;

    $mailChimpMembers = 0;
    $mailService = $settings->__get('updates.mailservice');
    $api = '';
    $list_id = '';

    if ($mailService == 'mailchimp') {
      include_once 'application/modules/Updates/Api/MCAPI.class.php';
      $list_id = $settings->__get('updates.mailchimp.listid');
      $apiKey = $settings->__get('updates.mailchimp.apikey');
      $api = new MCAPI($apiKey);
      $mailChimpResult = $api->listMembers($list_id, 'subscribed', null, 0, 15000);
      $mailChimpMembers = $mailChimpResult['data'];

      if ($sent >= count($mailChimpMembers)) {
        $this->sending_finished = 1;
        $tasksRow->finished = 1;
        $tasksRow->save();
        $this->save();
        return;
      }
    }
    else {
      if ($sent >= $totalRecipients) {
        $this->sending_finished = 1;
        $tasksRow->finished = 1;
        $tasksRow->save();
        $this->save();
        return;
      } else {
        $receivedUsersCount = 0;
        $receivedSubscribersCount = 0;
        if (!$settings->__get('updates.users.disabled')) {
          $receivedUsers = $subscribersTbl->getReceivedUsers($this->update_id, 'updates');
          $receivedUsersCount = $receivedUsers->count();
        }
        if (!$settings->__get('updates.subscribers.disabled')) {
          $receivedSubscribers = $subscribersTbl->getReceivedEmails($this->update_id, 'updates');
          $receivedSubscribersCount = $receivedSubscribers->count();
        }
        $totalReceivedRecipients = (int)($receivedUsersCount + $receivedSubscribersCount);
        if ($totalReceivedRecipients >= $totalRecipients) {
          $this->sending_finished = 1;
          $this->sent = $totalReceivedRecipients;
          $tasksRow->sent = $totalReceivedRecipients;
          $tasksRow->finished = 1;
          $tasksRow->save();
          $this->save();
          return;
        }
      }
    }

    /**
     * @var $view Zend_View
     * @var $mail Core_Api_Mail
     * @var $log Zend_Log
     */
    $limit = (int)($limit) ? $limit : $settings->__get('updates.perminut.itemnumber');
    $view = Zend_Registry::get('Zend_View');
    $mail = Engine_Api::_()->getApi('mail', 'core');
    $log = Zend_Registry::get('Zend_Log');
    $message = htmlspecialchars_decode($this->message);

    $notWidget = Engine_Api::_()->getDbtable('widgets', 'updates')->getWidget(array('name' => 'notifications'))->toArray();
    $notWidget['title'] = $view->translate('UPDATES_' . $notWidget['params']['title']);

    $mailForSendGrid = new Updates_Model_Mail();
    $translate = '';
    $from = '';
    $mailTemplate = '';
    $swift = '';
    $users = 0;
    $type = '';
    $opts = array();

    if ($mailService == 'mailchimp')
    {
      $type = 'regular';
      $opts['list_id'] = $list_id;
      $opts['subject'] = $settings->__get('updates.mailchimp.subject');
      $opts['from_email'] = $settings->__get('updates.mailchimp.fromemail');
      $opts['from_name'] = $settings->__get('updates.mailchimp.fromname');
      $opts['title'] = $settings->__get('updates.mailchimp.title');
      $opts['tracking'] = array('opens' => true, 'html_clicks' => true, 'text_clicks' => false);
      $opts['authenticate'] = true;
    }
    elseif ($mailService == 'sendgrid')
    {
      include_once 'application/modules/Updates/Api/SendGrid_loader.php';

      // Login credentials
      $username = $settings->__get('updates.sendgrid.username');
      $password = $settings->__get('updates.sendgrid.password');

      // Get admin info
      $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
      $fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');

      $sendgrid = new SendGrid($username, $password);

      // Verify mail template type
      $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
      $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', 'updates'));
      if (null === $mailTemplate) {
        return;
      }

      $translate = Zend_Registry::get('Zend_Translate');
    }

    $users_count = 0;
    if (!$settings->__get('updates.users.disabled') && $mailService != 'mailchimp' && $sent < $totalRecipients)
    {
      $users = $subscribersTbl->getSubscribedUsers($this->update_id, $limit);
      $users_count = $users->count();
      if ($users->count() > 0) {
        /**
         * @var $notTb Activity_Model_DbTable_Notifications
         * @var $user User_Model_User
         * @var $subscriber Updates_Model_Subscriber
         */
        $notTb = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notEnabled = preg_match('/\[notifications\]/', $message);
        $tableMessage = Engine_Api::_()->getDbTable('messages','updates');
        $select  = $tableMessage->select()->where('update_id = ?',$this->getIdentity());
        $messages = $tableMessage->fetchAll($select);
        $translate    = Zend_Registry::get('Zend_Translate');
        $languageList = $translate->getList();
        $origin = $translate->getLocale();
        // SEND UPDATES TO REGISTERED USERS
        foreach ($users as $user)
        {

          foreach($messages as $m){
            if($m['lang'] == $user->locale){
              $message = htmlspecialchars_decode($m['message']);
              $notEnabled = preg_match('/\[notifications\]/', $message);

            }
          }
        if(in_array($user->locale,$languageList)){
          try {
            $language = Zend_Locale::findLocale($user->locale);
          } catch( Exception $e ) {
            $language = $user->locale;
          }
          $translate->setLocale($language);
        }
          try {
            $notificationsHTML = '';
            if ($notEnabled && $user->getIdentity() && (int)$notTb->hasNotifications($user))
            {
              $notifications = $notTb->getNotificationsPaginator($user);
              $notifications->setCurrentPageNumber(1);

              $notificationsHTML = $view->widgetHTML($notWidget, $notifications);
              $notificationsHTML = str_replace(array('href="', "href='", 'src="/', "src='/", '<br>', '<br/>'),
                array('target="_blank" href="http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl(),
                      "target='_blank' href='http://" . $_SERVER['HTTP_HOST'] . $view->baseUrl(),
                      'src="http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/',
                      "src='http://" . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/',
                      " ", " "),
                $notificationsHTML);
            }

            if ($mailService == 'socialengine')
            {
              $params['updates'] = str_replace(array('[displayname]', '[email]', '[notifications]'), array($user->displayname, $user->email, $notificationsHTML), $message);
              $mail->sendSystemRaw($user->email, 'updates', $params);
              $sent++;
              $user->updates_update_id = $this->update_id;
              $user->disableHooks(true);
              $user->save();
              $user->disableHooks(false);
            }
            elseif ($mailService == 'sendgrid')
            {
              $messageBody = str_replace(array('[displayname]', '[email]', '[notifications]'), array($user->displayname, $user->email, $notificationsHTML), $message);

              if (!empty($user->language)) {
                $recipientLanguage = $user->language;
              } else {
                $recipientLanguage = $translate->getLocale();
              }

              // Get subject
              $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
              $subject = (string)$mailForSendGrid->translate($subjectKey, $recipientLanguage);

              // Create a message
              $sendgridMail = new SendGrid\Mail();

              $sendgridMail->
                addTo($user->email, $user->displayname)->
                setFrom($fromAddress)->
                setFromName($fromName)->
                setSubject($subject)->
                setText(strip_tags($messageBody))->
                setHtml($messageBody);

              $result = $sendgrid->
                web->
                send($sendgridMail);

              // send message
              if ($result) {
                //echo 'Message sent out to '.$recipients.' users';
                $sent++;
                $user->updates_update_id = $this->update_id;
                $user->save();
              } else {
                echo "Something went wrong - " . $result;
                exit;
              }
              unset($sendgridMail);
            }
            if ($sent >= $totalRecipients) {
              break;
            }
          } catch (Exception $e) {
            //print_log($e->__toString());
          }
        }
      }
    }

    if ($mailService == 'mailchimp')
    {
      $messageBody = str_replace(array('[displayname]', '[email]', '[notifications]'), array("*|FNAME|* *|LNAME|*", "*|EMAIL|*", "*|NOTIFMERGE|*"), $message);
      $content = array('html' => $messageBody, 'text' => 'text text text *|UNSUB|*');

      $campaign_id = $api->campaignCreate($type, $opts, $content);

      if ($api->errorCode) {
        echo "Unable to Create New Updates!\n";
        echo "\tCode=" . $api->errorCode . "\n";
        echo "\tMsg=" . $api->errorMessage . "\n";
        exit;
      }

      $api->campaignSendNow($campaign_id);

      if ($api->errorCode) {
        echo "Unable to Send Updates!\n";
        echo "\tCode=" . $api->errorCode . "\n";
        echo "\tMsg=" . $api->errorMessage . "\n";
        exit;
      }

      $sent = count($mailChimpMembers);

      if ($api->errorCode) {
        echo "Unable to load listMembers()!";
        echo "\n\tCode=" . $api->errorCode;
        echo "\n\tMsg=" . $api->errorMessage . "\n";
        exit;
      }
      unset($campaign_id);
    }

    //$users_count = ($users) ? $users->count() : 0;
    $subscribers_count = 0;
    if ($users_count < $limit && !$settings->__get('updates.subscribers.disabled') && $mailService != 'mailchimp' && $sent < $totalRecipients)
    {
      $limit = (int)($limit - $users_count);
      $subscribers = $subscribersTbl->getSubscribedEmails($this->update_id, $limit);
      $subscribers_count = $subscribers->count();

      // SEND UPDATES TO SUBSCRIBERS
      if ($subscribers->count() > 0)
      {
        foreach ($subscribers as $subscriber)
        {
          try {
            if ($mailService == 'socialengine')
            {
              $params['updates'] = str_replace(array('[displayname]', '[email]', '[notifications]'), array($subscriber->name, $subscriber->email_address, ''), $message);
              $mail->sendSystemRaw($subscriber->email_address, 'updates', $params);
              $sent++;
              $subscriber->update_id = $this->update_id;
              $subscriber->save();
            }
            elseif ($mailService == 'sendgrid')
            {
              $messageBody = str_replace(array('[displayname]', '[email]', '[notifications]'), array($subscriber->name, $subscriber->email_address, ''), $message);

              if (!empty($subscriber->language)) {
                $recipientLanguage = $subscriber->language;
              } else {
                $recipientLanguage = $translate->getLocale();
              }

              // Get subject
              $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
              $subject = (string)$mailForSendGrid->translate($subjectKey, $recipientLanguage);

              // Create a message
              include SENDGRID_ROOT_DIR . 'Mail.php';
              $sendgridMail = new SendGrid\Mail();

              $sendgridMail->
                addTo($subscriber->email_address, $subscriber->name)->
                setFrom($fromAddress)->
                setFromName($fromName)->
                setSubject($subject)->
                setText(strip_tags($messageBody))->
                setHtml($messageBody);

              $result = $sendgrid->
                web->
                send($sendgridMail);

              // send message
              if ($result) {
                //echo 'Message sent out to '.$recipients.' users';
                $sent++;
                $subscriber->update_id = $this->update_id;
                $subscriber->save();
              } else {
                echo "Something went wrong - " . $result;
                exit;
              }
              unset($sendgridMail);
            }
            if ($sent >= $totalRecipients) {
              break;
            }
          } catch (Exception $e) {
            // print_log($e->__toString());
          }
        }
      }
    }

    $tasksRow->sent = $sent;
    $this->sent = $sent;

    if ($mailService == 'mailchimp') {
      if ($sent >= count($mailChimpMembers)) {
        $this->sending_finished = 1;
        $tasksRow->finished = 1;
      }
    }
    else {
      if ($sent >= $totalRecipients) {
        $this->sending_finished = 1;
        $tasksRow->finished = 1;
      }
      else {
        if ($users_count == 0 && $subscribers_count == 0) {
          $this->sending_finished = 1;
          $tasksRow->finished = 1;
        }
      }
    }

    $period = $settings->__get('updates.update.period');
    $time = $settings->__get('updates.update.time');
    $next_update_time = $time;
    $now = Engine_Api::_()->updates()->getTimestamp();

    if ($time <= $now) {
      if ($period == 'everyday') {
        $next_update_time = $time + 24 * 60 * 60;
        $next_update_time = ($next_update_time > $now) ? $next_update_time : $now + 24 * 60 * 60;
      } else {
        $next_update_time = $time + 7 * 24 * 60 * 60;
        $next_update_time = ($next_update_time > $now) ? $next_update_time : $now + 7 * 24 * 60 * 60;
      }
      $settings->__set('updates.update.time', $next_update_time);
    }

    $settings->__set('updates.update.last.time', $now);
    $this->save();
    $tasksRow->save();

    return;
  }
}