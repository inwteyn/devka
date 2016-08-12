<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Subscribe.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Form_Widgets_Subscribe extends Engine_Form
{
  public function init()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $this
        ->clearDecorators()
        ->addDecorator('FormElements')
        ->addDecorator('Form')
        ->setAttrib('class', 'subscribe_updates')
        ->setAttrib('id', 'subscribe_updates');

    
    $this->addElement('text', 'updates_email_box', array(
      'autocomplete'=>'on',
      'value' => $translate->_('UPDATES_email...'),
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
      'trim' => true,
      'decorators'=>array(
        'ViewHelper',
      ),
      'class' => 'updates_subscribe_input',
    ));

    $this->addElement('Button', 'subscribe', array(
      'label' => 'UPDATES_Subscribe',
      'type' => 'submit',
      'value' => 1,
      'ignore' => true,
      'decorators'=>array(
        'ViewHelper',
      ),
      'class'=>'updates_subs_button'
    ));

    $this->addElement('Button', 'unsubscribe', array(
      'label' => 'UPDATES_Unsubscribe',
      'type' => 'submit',
      'value' => 1,
      'ignore' => true,
      'decorators'=>array(
        'ViewHelper',
      ),
      'class'=>'updates_subs_button'
    ));
  }
  
  public function subscription($task)
  {
    $email_address = trim($_POST['updates_email_box']);
    $position = strpos($email_address, '@');
    $name = substr($email_address, 0, $position);
    //$name = trim($_POST['updates_name_box']);
    $subscriberTbl = Engine_Api::_()->getDbtable('subscribers', 'updates');
    $translate = Zend_Registry::get('Zend_Translate');

    if ($this->getElement('updates_email_box')->isValid($email_address))
    {
      $subscriber = $subscriberTbl->fetchRow($subscriberTbl->select()->where("email_address=?", $email_address)->limit(1));

      if (isset($subscriber->email_address) && strlen($subscriber->email_address)>0 && $task == 'subscribe') {

        return array('status' => 0, 'message' => $translate->_('UPDATES_Failed! An email address is already subscribed.'));

      } elseif (!isset($subscriber->email_address) && $task == 'unsubscribe') {

        return array('status' => 0, 'message' =>$translate->_('UPDATES_Failed! An email address is not subscribed yet.'));
      }

      $user = Engine_Api::_()->user()->getViewer();

      if ($task == 'subscribe')
      {
        $subscriber = $subscriberTbl->createRow();
        $subscriber->email_address = $email_address;
        $subscriber->name = $name;
        $subscriber->user_id = $user->getIdentity();

        if ($subscriber->save())
        {
          include_once 'application/modules/Updates/Api/MCAPI.class.php';
          $settings = Engine_Api::_()->getApi('settings', 'core');
          $apiKey = $settings->__get('updates.mailchimp.apikey');
          $api = new MCAPI($apiKey);

          if ($api->ping() == "Everything's Chimpy!") {
            $list_id = $settings->__get('updates.mailchimp.listid');
            $merge_vars = array('FNAME'=>'', 'LNAME'=> '');
            $api->listSubscribe($list_id, $email_address, $merge_vars, 'html', false, false, false,false);

            if ($api->errorCode) {
              $error = "Unable to load listUnsubscribe()!\n";
              $error .= "\tCode=".$api->errorCode."\n";
              $error .= "\tMsg=".$api->errorMessage."\n";
              print_log($error);
            }
          }
          return array('status' => 1, 'message' =>$translate->_('UPDATES_SUBSCRIPTION_SUCCESS_MESSAGE'));
        }
        else {
          return array('status' => 0, 'message' =>$translate->_('UPDATES_SUBSCRIPTION_ERROR_MESSAGE'));
        }
      }
      elseif($task == 'unsubscribe')
      {
        if ($subscriberTbl->delete(array('email_address = ?' => $email_address)))
        {
          include_once 'application/modules/Updates/Api/MCAPI.class.php';
          $settings = Engine_Api::_()->getApi('settings', 'core');
          $apiKey = $settings->__get('updates.mailchimp.apikey');
          $api = new MCAPI($apiKey);

          if ($api->ping() == "Everything's Chimpy!") {
            $list_id = $settings->__get('updates.mailchimp.listid');
            $api->listUnsubscribe($list_id, $email_address, true, false, false);

            if ($api->errorCode) {
              $error = "Unable to load listUnsubscribe()!\n";
              $error .= "\tCode=".$api->errorCode."\n";
              $error .= "\tMsg=".$api->errorMessage."\n";
              print_log($error);
            }
          }
          return array('status' => 1, 'message' =>$translate->_('UPDATES_UNSUBSCRIPTION_SUCCESS_MESSAGE'));
        }
        else {
          return array('status' => 0, 'message' =>$translate->_('UPDATES_UNSUBSCRIPTION_ERROR_MESSAGE'));
        }
      }
    }
    else {
      return array('status' => 0, 'message' =>$translate->_('UPDATES_Failed! An email address is not valid!'));
    }
  }
}