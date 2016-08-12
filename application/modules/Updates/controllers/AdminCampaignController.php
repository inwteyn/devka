<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminLayoutController.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Updates_AdminCampaignController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_campaign');

    $campaignTb = Engine_Api::_()->getDbtable('campaigns', 'updates');

    //ACTIVE CAMPAIGNS
    $active_paginator = Zend_Paginator::factory($campaignTb->getActiveCampaigns());

    $active_paginator->setItemCountPerPage(10);
    $active_paginator->setCurrentPageNumber( 1 );
    $this->view->active_paginator = $active_paginator;
    $this->view->active_paginator_pages = $active_paginator->getPages();

    //SCHEDULE CAMPAIGNS
    $schedule_paginator = Zend_Paginator::factory($campaignTb->getScheduleCampaigns());
    $schedule_paginator->setItemCountPerPage(10);
    $date = date('Y-m-d H:i:s', strtotime(Engine_Api::_()->updates()->getDatetime()));

    $select = $campaignTb->select()
      ->setIntegrityCheck(false)
      ->from($campaignTb->info('name'), array('COUNT(campaign_id) AS _count'))
      ->where('type=?', 'schedule')
      ->where('finished=?', 0)
      ->where('planned_date >?', $date)
      ->order('planned_date DESC')
      ->limit(1);
    $item = $campaignTb->fetchRow($select);
    $page = (int)($item->_count/10);
    if ($page*10 < $item->_count)
    {
      $page++;
    }

    $schedule_paginator->setCurrentPageNumber( $page );
    $this->view->schedule_paginator = $schedule_paginator;
    $this->view->schedule_paginator_pages = $schedule_paginator->getPages();

    //SENT CAMPAIGNS
    $sent_paginator = Zend_Paginator::factory($campaignTb->getSentCampaigns());
    $sent_paginator->setItemCountPerPage(10);
    $sent_paginator->setCurrentPageNumber( 1 );
    $this->view->sent_paginator = $sent_paginator;
    $this->view->sent_paginator_pages = $sent_paginator->getPages();

    //NEW INSTANT CAMPAIGN SENT
    $campaign_id = $this->_getParam('campaign_id', 0);
    $campaignTb = Engine_Api::_()->getDbtable('campaigns', 'updates');
    $this->view->instant_campaign = $campaignTb->getCampaign($campaign_id);
  }

  public function campaignsAction()
  {
    $type = $this->_getParam('type', 'active_paginator');
    $campaignTb = Engine_Api::_()->getDbtable('campaigns', 'updates');

    if ($type == 'active_paginator'){
      $paginator = Zend_Paginator::factory($campaignTb->getActiveCampaigns());
    } elseif ($type == 'schedule_paginator') {
      $paginator = Zend_Paginator::factory($campaignTb->getScheduleCampaigns());
    } elseif ($type == 'sent_paginator') {
      $paginator = Zend_Paginator::factory($campaignTb->getSentCampaigns());
    }

    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber( $this->_getParam( 'page', 1));

    $this->view->html = $this->view->ajaxPaginator($paginator, $type);
  }

  public function stopAction()
  {
    $campaign_id = $this->_getParam('campaign_id', 0);
    $campaignTb = Engine_Api::_()->getDbtable('campaigns', 'updates');
    $templateTb = Engine_Api::_()->getDbtable('templates', 'updates');

    $campaign = $campaignTb->getCampaign($campaign_id);
    $this->view->template = $template = $templateTb->getTemplate($campaign->template_id);
    $this->view->form = $form = new Updates_Form_Admin_Campaign_Stop();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $campaign->finished = 1;
      if ($campaign->save())
      {
        $tasksTbl = Engine_Api::_()->getDbTable('tasks', 'updates');
        $where = array(
          $tasksTbl->getAdapter()->quoteInto('updcamp_id = ?', $campaign->campaign_id),
          $tasksTbl->getAdapter()->quoteInto('type = ?', 'campaign')
        );
        $tasksTbl->update(array('finished' => 1), $where);

        $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'format'=> 'smoothbox',
          'messages' => array($this->view->translate("UPDATES_The campaign '%s' successfully has been stopped.", $template->subject)),
        ));
      }
    }
    $form->populate(array('campaign_id'=>$campaign_id));
  }

  public function editAction()
  {
    // if demoadmin
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      $this->view->engine_admin_neuter = true;
    }

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_campaign');

    $campaign_id = $this->_getParam('campaign_id', 0);

    /**
     * @var $campaignTb Updates_Model_DbTable_Campaigns
     * @var $templateTb Updates_Model_DbTable_Templates
     * @var $campaign Updates_Model_Campaign
     * @var $template Updates_Model_Template
     */
    $campaignTb = Engine_Api::_()->getDbTable('campaigns', 'updates');
    $templateTb = Engine_Api::_()->getDbTable('templates', 'updates');
    $tasksTbl = Engine_Api::_()->getDbTable('tasks', 'updates');

    $this->view->form = $form = new Updates_Form_Admin_Campaign_Edit();
    $form->getDecorator('description')->setOption('escape', false);

    $totalRecipients = 0;
    $values = $this->_getAllParams();

    if (null != ($campaign = Engine_Api::_()->getItem('campaign', $campaign_id)))
    {
      $template = Engine_Api::_()->getItem('updates_template', $campaign->template_id);
      $totalRecipients = (int) $campaign->getTotalRecipients();

      $planned_date['date'] = date('m/d/Y', strtotime($campaign->planned_date));
      $planned_date['hour'] = date('h', strtotime($campaign->planned_date));
      if ($planned_date['hour']<10){$planned_date['hour'] = str_replace('0','', $planned_date['hour']);}
      $planned_date['minute'] = date('i', strtotime($campaign->planned_date));
      if ($planned_date['hour']<10){$planned_date['hour'] = str_replace('0','', $planned_date['hour']);}
      $planned_date['ampm'] = date('A', strtotime($campaign->planned_date));

      $values = array_merge(array_merge($campaign->recievers, array(
        'subject'=>$template->subject,
        'message'=>$template->message,
        'campaign_type'=>$campaign->type,
        'planned_date' => $planned_date,
        'campaign_id'=>$campaign->campaign_id,
      )), $values);

      if ($campaign->type == 'schedule')
      {
        $submit = $form->getElement('submit');
        $submit->setLabel($this->view->translate('UPDATES_Save Changes'));
      }
    }

    $values['recipients'] = '<span id="total_recipients" style="font-weight: bold;color:red">' . $totalRecipients . '</span>&nbsp;' .
      '<img border="0px" style="display: none;" title="Loading..." src="application/modules/Updates/externals/images/loading.gif" id="loading_refresh_recipients">';

    $form->populate($values);

    $this->view->templates = $templates = $templateTb->getTemplates();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $data = array();
    if ( !is_null($campaign) ) {
      $data = $campaign->toArray();
      unset($data['campaign_id']);
    }

    $campaign = $campaignTb->createRow();
    $campaign->setFromArray($data);

    if ( $values['campaign_type'] == 'instant' )
    {
      $pd = $this->_getParam('planned_date');
      $pd['date'] = date('n/j/Y',time());
      $pd['hour'] = date('g', time());
      $pd['minute'] = 59;
      $pd['ampm'] = date('a', time());
      $values['planned_date'] = $pd;
    }

    $campaign->campaign_id = $campaign->campaign_id++;
    $campaign->type = $values['campaign_type'];
    $campaign->setPlannedDate($values);
    $values['planned_date'] = $campaign->planned_date;

    if ( !$form->isValid($values) ) {
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['planned_date']);
     date_default_timezone_set($oldTz);
    $values['planned_date']= date('Y-m-d H:i:s', $start);

    $template = $templateTb->createRow();
    $template->subject = $values['subject'];
    $template->message = $values['message'];
    $template->save();

    $campaign->finished = 0;
    $campaign->sent = 0;
    $campaign->template_id = $template->template_id;
    $campaign->type = $values['campaign_type'];
    $campaign->setRecipients($values, $form->getElements(), $this->getRequest()->getPost());

    set_time_limit (0);
    ini_set('max_execution_time', 0);
    ini_set('memory_limit', '150M');

    if ( $campaign->save() )
    {
      $tasksRow = $tasksTbl->createRow();
      $tasksRow->type = 'campaign';
      $tasksRow->creation_date = new Zend_Db_Expr('NOW()');
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
        //$tasksRow->total_recipients = (int) $campaign->getTotalRecipients();
        $tasksRow->total_recipients = (int) $values['recipients_qty'];
      }
      $tasksRow->updcamp_id = $campaign->campaign_id;
      if ($campaign->type == 'schedule') {
        $tasksRow->scheduled = 1;
      }
      $tasksRow->subject = $template->subject;
      $tasksRow->save();

      $data = $campaign->sendCampaign( 10 );
    }

    $this->_redirectCustom(array('route'=>'campaign_instant_sent', 'campaign_id'=>$campaign->campaign_id));
  }

  public function deleteAction()
  {
    /**
     * @var $table Updates_Model_DbTable_Campaigns
     * @var $campaign Updates_Model_Campaign
     */

    $campaign_id = $this->_getParam('campaign_id', 0);
    $task = $this->_getParam('task');

    if ($task === 'delete')
    {
      // delete updates_task
      $tasksTbl = Engine_Api::_()->getDbTable('tasks', 'updates');
      $tasksTbl->delete(array('updcamp_id = ?' => $campaign_id));
      // delete campaign
      $table = Engine_Api::_()->getDbtable('campaigns', 'updates');
      $select = $table->select()->where('campaign_id = ?', $campaign_id)->limit(1);
      $campaign = $table->fetchRow($select);
      if ($campaign->delete())
      {
        $this->view->success = 1;
      }
    }
  }

  public function templateAction()
  {
    /**
     * @var $viewer User_Model_User
     * @var $mail Core_Api_Mail
     * @var $templateTb Updates_Model_DbTable_Templates
     */
    $task = $this->_getParam('task');
    $template_id = $this->_getParam('template_id', 0);

    if ($task == 'delete')
    {
      $template = Engine_Api::_()->getDbtable('templates', 'updates')->getTemplate($template_id);
      if ($template->delete()){
        $this->view->success = 1;
      }
    } elseif ($task == 'preview') {
			$viewer = Engine_Api::_()->user()->getViewer();
			$templateTb = Engine_Api::_()->getDbtable('templates', 'updates');
			$this->view->template = $template = $templateTb->getTemplate($template_id);
			$standardVariables = $templateTb->getStandardVariables($viewer);
			$widgetsVariables = $templateTb->getWidgetsVariables($template->message);
			$suggestWidgetsVariables = $templateTb->getSuggestWidgetsVariables($template->message, $viewer);
			$variables['keys'] = array_merge($standardVariables['keys'], $widgetsVariables['keys'], $suggestWidgetsVariables['keys']);
			$variables['replaces'] = array_merge($standardVariables['replaces'], $widgetsVariables['replaces'], $suggestWidgetsVariables['replaces']);

			$preview['subject'] = str_replace($standardVariables['keys'], $standardVariables['replaces'], $template->subject);
			$preview['message'] = str_replace($variables['keys'], $variables['replaces'], $template->message);

			$this->view->preview = $preview;
			$this->view->form = $form = new Updates_Form_Admin_Campaign_Preview();
		}
  }

  public function testemailAction()
  {
    $this->view->form = $form = new Updates_Form_Admin_Layout_Testemail();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
		{
      /**
       * @var $viewer User_Model_User
       * @var $mail Core_Api_Mail
       * @var $templateTb Updates_Model_DbTable_Templates
       */
      $view = Zend_Registry::get('Zend_View');
      $viewer = Engine_Api::_()->user()->getViewer();
	  	$test_email = $this->_getParam('test_email');
      $subject = $this->_getParam('subject');
      $message = $this->_getParam('message');
      $mail = Engine_Api::_()->getApi('mail', 'core');
      $templateTb = Engine_Api::_()->getDbtable('templates', 'updates');
      $widgetVariables = $templateTb->getWidgetsVariables($message);

      $standartVariables = $templateTb->getStandardVariables($viewer);
      $standartVariables['replaces'][5] = $test_email;

      $widgetsVariablesReplaced = array();
      $i = -1;
      foreach($widgetVariables['replaces'] as $widget)
      {
        $i++;
        $widget = str_replace (
        array('href="', "href='", 'src="', "src='"),
        array(
             'target="_blank" href="http://'.$_SERVER['HTTP_HOST'],
             "target='_blank' href='http://".$_SERVER['HTTP_HOST'],
             'src="http://' . $_SERVER['HTTP_HOST'] ,
             "src='http://" . $_SERVER['HTTP_HOST'] ,
        ), $widget);
        $widgetsVariablesReplaced['replaces'][$i] = $widget;
      }

      $message = str_replace($widgetVariables['keys'], $widgetsVariablesReplaced['replaces'], $message);

      $keys = array_merge($standartVariables['keys'], $widgetVariables['keys']);
      $replaces = array_merge($standartVariables['replaces'], $widgetVariables['replaces']);

      $params['subject'] = str_replace($standartVariables['keys'], $standartVariables['replaces'], $subject);
      $params['message'] = str_replace($keys, $replaces, $message);

      $remove = array("\n", "\r\n", "\r");
      $params['message'] = str_replace($remove, ' ', $params['message']);

      $suggestWidgetsVariables = $templateTb->getSuggestWidgetsVariables($params['message'], $viewer);

      if ($suggestWidgetsVariables) {
        $params['message'] = str_replace($suggestWidgetsVariables['keys'], $suggestWidgetsVariables['replaces'], $params['message']);
        $remove = array("\n", "\r\n", "\r");
        $params['message'] = str_replace($remove, ' ', $params['message']);
      }

      $settings = Engine_Api::_()->getApi('settings', 'core');
      $mailService = $settings->__get('updates.mailservice');

      $messageBody = $params['message'];

      if ($mailService == 'socialengine')
      {
        if ($mail->sendSystemRaw($test_email, 'campaign', $params) instanceof Core_Api_Mail)
        {
          $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => TRUE,
            'parentRefresh' => FALSE,
            'format'=> 'smoothbox',
            'messages' => array($this->view->translate('UPDATES_Test mail successfully has been sent to ').$test_email),
          ));
        }
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
        $sendgridMail = new SendGrid\Mail();

        $sendgridMail->
          addTo($test_email)->
          setFrom($fromAddress)->
          setFromName($fromName)->
          setSubject('Test Campaign')->
          setText(strip_tags($messageBody))->
          setHtml($messageBody);

        $result = $sendgrid->
          web->
          send($sendgridMail);

        // send message
        if ($result) {
          $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => TRUE,
            'parentRefresh' => FALSE,
            'format'=> 'smoothbox',
            'messages' => array($this->view->translate('UPDATES_Test mail successfully has been sent to ').$test_email),
          ));
        }
        else {
          echo "Something went wrong - " . $result;
          exit;
        }
        unset($sendgridMail);
      }
      elseif ($mailService == 'mailchimp')
      {
        if (!class_exists('MCAPI')) {
          include_once 'application/modules/Updates/Api/MCAPI.class.php';
        }
        $apikey = $settings->__get('updates.mailchimp.apikey');
        $api = new MCAPI($apikey);
        $type = 'regular';

        $list_id = $settings->__get('updates.mailchimp.listid');
        $opts['list_id'] = $list_id;
        $opts['subject'] = $settings->__get('updates.mailchimp.subject');
        $opts['from_email'] = $settings->__get('updates.mailchimp.fromemail');
        $opts['from_name'] = $settings->__get('updates.mailchimp.fromname');
        $opts['title'] = $settings->__get('updates.mailchimp.title');
        $opts['tracking'] = array('opens' => true, 'html_clicks' => true, 'text_clicks' => false);
        $opts['authenticate'] = true;

        $messageBody = str_replace('Unsubscribe</a>&nbsp;|', '</a>', $messageBody);

        $content = array('html'=>$messageBody,
              'text' => 'text text text *|UNSUB|*'
        );

        $campaign_id = $api->campaignCreate($type, $opts, $content);

        if ($api->errorCode) {
          echo "Unable to Create New Campaign!\n";
          echo "\tCode=".$api->errorCode."\n";
          echo "\tMsg=".$api->errorMessage."\n";
          exit;
        }

        $emails = array($test_email);
        $api->campaignSendTest($campaign_id, $emails);

        if ($api->errorCode) {
          echo "Unable to Send Campaign!\n";
          echo "\tCode=".$api->errorCode."\n";
          echo "\tMsg=".$api->errorMessage."\n";
          exit;
        }
        else {
          $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => TRUE,
            'parentRefresh' => FALSE,
            'format'=> 'smoothbox',
            'messages' => array($this->view->translate('UPDATES_Test mail successfully has been sent to ').$test_email),
          ));
        }
        unset($campaign_id);
      }
	  }
  }

  public function refreshRecipientsAction()
  {
    /**
     * @var $campaignTb Updates_Model_DbTable_Campaigns
     * @var $campaign Updates_Model_Campaign
     */
    $recipients = $this->_getParam('recipients');

    $campaignTb = Engine_Api::_()->getDbtable('campaigns', 'updates');
    $campaign = $campaignTb->createRow();
    $campaign->campaign_id = $campaignTb->getInsertId();
    $campaign->recievers = $recipients;

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->mailService = $mailService = $settings->__get('updates.mailservice');

    if ($mailService == 'mailchimp') {
      include_once 'application/modules/Updates/Api/MCAPI.class.php';
      $list_id = $settings->__get('updates.mailchimp.listid');
      $apiKey = $settings->__get('updates.mailchimp.apikey');
      $api = new MCAPI($apiKey);
      $mailChimpResult = $api->listMembers($list_id, 'subscribed');
      $mailChimpMembers = $mailChimpResult['data'];
      $this->view->total_recipients  = count($mailChimpMembers);
    }
    else {
      $this->view->total_recipients = $totalRecipients = $campaign->getTotalRecipients();
    }
    $this->view->success = 1;
  }

  protected function _executeSearch()
  {
    // Check form
    $form2 = new User_Form_Search(array(
      'type' => 'user'
    ));

    if( !$form2->isValid($this->_getAllParams()) ) {
      $this->view->error = true;
      return false;
    }

    $this->view->form2 = $form2;

    // Get search params
    $page = (int)  $this->_getParam('page', 1);
    $ajax = (bool) $this->_getParam('ajax', false);
    $options = $form2->getValues();

    // Process options
    $tmp = array();
    $originalOptions = $options;
    foreach( $options as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $options = $tmp;

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');

    $profile_type = @$options['profile_type'];
    $displayname = @$options['displayname'];
    if (!empty($options['extra'])) {
      extract($options['extra']); // is_online, has_photo, submit
    }

    // Construct query
    $select = $table->select()
      ->from($userTableName)
      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
      ->where("{$userTableName}.search = ?", 1)
      ->where("{$userTableName}.enabled = ?", 1)
      ->order("{$userTableName}.displayname ASC");

    // Build the photo and is online part of query
    if( isset($has_photo) && !empty($has_photo) ) {
      $select->where($userTableName.'.photo_id != ?', "0");
    }

    if( isset($is_online) && !empty($is_online) ) {
      $select
        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
        ->group("engine4_user_online.user_id")
        ->where($userTableName.'.user_id != ?', "0");
    }

    // Add displayname
    if( !empty($displayname) ) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach( $searchParts as $k => $v ) {
      $select->where("`{$searchTableName}`.{$k}", $v);
    }
  }
}