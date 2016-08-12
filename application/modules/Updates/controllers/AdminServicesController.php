<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminServicesController.php 2012-02-14 15:18 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
include_once 'application/modules/Updates/Api/MCAPI.class.php';

class Updates_AdminServicesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // if demoadmin
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      $this->view->engine_admin_neuter = true;
    }

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_services');

    $this->view->selectForm = $selectForm =  new Updates_Form_Admin_Services_Select();
    $selectForm->getDecorator('description')->setOption('escape', false);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $mailService = $settings->__get('updates.mailservice');
    $this->view->mailService = $mailService;

    $mailChimpData['list_name'] = $settings->__get('updates.mailchimp.listname');
    $mailChimpData['api_key'] = $settings->__get('updates.mailchimp.apikey');
    $mailChimpData['list_id'] = $list_id = $settings->__get('updates.mailchimp.listid');
    $mailChimpData['title'] = $settings->__get('updates.mailchimp.title');
    $mailChimpData['subject'] = $settings->__get('updates.mailchimp.subject');
    $mailChimpData['from_email'] = $settings->__get('updates.mailchimp.fromemail');
    $mailChimpData['from_name'] = $settings->__get('updates.mailchimp.fromname');
    $this->view->mailChimpForm = $mailChimpForm = new Updates_Form_Admin_Services_Mailchimp($mailChimpData);
    $mailChimpForm->getDecorator('description')->setOption('escape', false);
    $mailChimpForm->setAttrib('class', '');

    $sendGridData['username'] = $settings->__get('updates.sendgrid.username');
    $sendGridData['password'] = $settings->__get('updates.sendgrid.password');
    $this->view->sendGridForm = $sendGridForm = new Updates_Form_Admin_Services_Sendgrid($sendGridData);
    $sendGridForm->setAttrib('class', '');

    if ($mailService == 'mailchimp')
    {
      $api = new MCAPI($mailChimpData['api_key']);

      if( $this->getRequest()->isPost() && $mailChimpForm->isValid($this->_getAllParams()))
      {
        $data = $this->_getAllParams();

        $apiKey = $data['api_key'];
        $api = new MCAPI($apiKey);
        if ($api->ping() != "Everything's Chimpy!") {
          $mailChimpForm->addError('UPDATES_Invalid api key! Please check it and try again.');
          return;
        }
        // create and add new webhook to list in mailchimp
        $view = Zend_Registry::get('Zend_View');
        $url = 'http://' .$_SERVER['HTTP_HOST']. $view->baseUrl() .'/updates/ajax/unsubscribe/';
        $actions = array('subscribe'=>false, 'unsubscribe'=>true, 'profile'=>false, 'cleaned'=>false, 'upemail'=>false, 'campaign'=>false);
        $source = array('user'=>true, 'admin'=>false, 'api'=>false);
        $resultWebHook = $api->listWebhookAdd($list_id, $url, $actions, $source);

        $result = $api->lists(array('list_name' => $data['list_name']));

        if ($api->errorCode) {
          $getList_error = 'error';
          $getList_error_details = " Code=".$api->errorCode;
          $getList_error_details .= " Msg=".$api->errorMessage;
          $mailChimpForm->addError('API Error: '.$getList_error_details);
          return;
        }


        if ($result['data'][0]['id'] == '') {
          $mailChimpForm->addError('UPDATES_List is not found! Please check it and try again.');
          return;
        }

        $settings->__set('updates.mailchimp.listname', $data['list_name']);
        $settings->__set('updates.mailchimp.apikey', $data['api_key']);
        $settings->__set('updates.mailchimp.listid', $result['data'][0]['id']);
        $settings->__set('updates.mailchimp.title', $data['title']);
        $settings->__set('updates.mailchimp.subject', $data['subject']);
        $settings->__set('updates.mailchimp.fromemail', $data['from_email']);
        $settings->__set('updates.mailchimp.fromname', $data['from_name']);

        $result = $api->lists(array('list_id' => $mailChimpData['list_id']));
        if ($result) {
          $this->view->existList = 1;
        }
        else {
          $this->view->existList = 0;
        }

        $mailChimpForm->addNotice('UPDATES_MailChimp changes have been successfully saved.');
      }
    }
    elseif ($mailService == 'sendgrid')
    {
      include_once 'application/modules/Updates/Api/Swift/swift_required.php';
      if( $this->getRequest()->isPost() && $sendGridForm->isValid($this->_getAllParams()))
      {
        $data = $this->_getAllParams();
        $settings->__set('updates.sendgrid.username', $data['username']);
        $settings->__set('updates.sendgrid.password', $data['password']);
        $sendGridForm->addNotice('UPDATES_SendGrid changes have been successfully saved.');
      }
    }
  }

  public function getListIdAction()
  {
    $apiKey = $this->_getParam('api_key');
    $list_name = $this->_getParam('list_name');

    $api = new MCAPI($apiKey);

    if ($api->ping() != "Everything's Chimpy!") {
      $this->view->api_key_error = 'error';
      return;
    }

    $result = $api->lists(array('list_name' => $list_name));

    if ($api->errorCode) {
      $getList_error = 'error';
      $getList_error_details = " Code=".$api->errorCode;
      $getList_error_details .= " Msg=".$api->errorMessage;
      $this->view->getList_error = $getList_error;
      $this->view->getList_error_details = $getList_error_details;
      return;
    }

    $list_id = $result['data'][0]['id'];
    $this->view->total = $result['total'];
    $this->view->list_id = $list_id;
  }

  public function setMailServiceAction()
  {
    $mailService = $this->_getParam('mailService');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->__set('updates.mailservice', $mailService);
    $this->view->set = 'Success';
    $this->view->mailService = $mailService;
    if ($mailService == 'mailchimp') {
      include_once 'application/modules/Updates/Api/MCAPI.class.php';
      $list_id = $settings->__get('updates.mailchimp.listid');
      $apiKey = $settings->__get('updates.mailchimp.apikey');
      $api = new MCAPI($apiKey);
      $result = $api->lists(array('list_id' => $list_id));
      if ($result) {
        $this->view->existList = 1;
      }
      else {
        $this->view->existList = 0;
      }
    }
  }

  public function generateListNameAction()
  {
    $this->view->listName = $_SERVER['HTTP_HOST'].'-'. mt_rand(999,9999);
  }

  public function exportMembersAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $apiKey = $settings->__get('updates.mailchimp.apikey');
    $listName = $settings->__get('updates.mailchimp.listname');
    $list_id = $settings->__get('updates.mailchimp.listid');
    $api = new MCAPI($apiKey);

    if ($api->ping() != "Everything's Chimpy!") {
      $this->view->api_key_error = 'error';
      return;
    }

    if ($api->errorCode) {
      echo "Unable to load lists!";
      echo "\n\tCode=".$api->errorCode;
      echo "\n\tMsg=".$api->errorMessage."\n";
    }

    $this->view->exportMembersForm = $exportMembersForm = new Updates_Form_Admin_Services_ExportMembers($listName);
    $this->view->listName = $listName;

    if( $this->getRequest()->isPost())
    {
      // get users
      $userTbl = Engine_Api::_()->getDbTable('users', 'user');
      $selectUsers = $userTbl->select()
        ->from(array($userTbl->info('name')))
        ->where('enabled = 1')
        ->where('approved = 1')
        ->where('verified = 1')
        ->where('updates_subscribed = 1');
      $users = $userTbl->fetchAll($selectUsers);

      $emails = array();
      foreach ($users as $user) {
        $display_name = explode(' ', $user->displayname);
        $first_name = isset($display_name[0]) ? array_shift($display_name) : '';
        $last_name = (count($display_name) > 0) ? implode(' ', $display_name) : '';

        $emails[] = array('EMAIL' => $user->email, 'FNAME' => $first_name, 'LNAME' => $last_name) ;
      }

      // get subscribers
      $subscribersTbl = Engine_Api::_()->getDbTable('subscribers', 'updates');
      $selectSubscribers = $subscribersTbl->select()
        ->from(array($subscribersTbl->info('name')));
      $subscribers = $subscribersTbl->fetchAll($selectSubscribers);

      foreach ($subscribers as $subscriber) {
        $emails[] = array('EMAIL' => $subscriber->email_address, 'FNAME' => $subscriber->email_address);
      }

      // subscribe members to list in MailChimp
     $api->listBatchSubscribe($list_id, $emails, false, true, false);

      if ($api->errorCode) {
        echo "\tUnable to load listBatchSubscribe()!\n\t";
        echo "Code=".$api->errorCode."\n\t";
        echo "Msg=".$api->errorMessage;
        exit;
      }

      $this->_forward('success', 'utility', 'core', array(
      	'smoothboxClose' => TRUE,
      	'parentRefresh' => TRUE,
      	'format'=> 'smoothbox',
      	'messages' => array($this->view->translate('UPDATES_Exporting members has been finished successfully')),
    	));
    }
  }
}