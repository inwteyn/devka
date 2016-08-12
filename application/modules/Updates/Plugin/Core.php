<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
	public function onMenuInitialize_UserSettingsUpdates($row)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		
    if (!Engine_Api::_()->authorization()->isAllowed('updates', null, 'use'))
    {
      return false;
    }
    
		return true;
	}
	
	public function onUserDeleteBefore($update)
	{
		$user = $update->getPayload();
		$subscriberTb = Engine_Api::_()->getDbtable('subscribers', 'updates');
		$subscriberSl = $subscriberTb->select()->where('user_id = ?', $user->user_id);

		$subscribers = $subscriberTb->fetchAll($subscriberSl);
		foreach ($subscribers as $subscriber)
		{
			$subscriber->delete();
		}

    include_once 'application/modules/Updates/Api/MCAPI.class.php';
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $apiKey = $settings->__get('updates.mailchimp.apikey');
    $api = new MCAPI($apiKey);

    if ($api->ping() != "Everything's Chimpy!") {
      // mailchimp api error
      return;
    }

    $list_id = $settings->__get('updates.mailchimp.listid');
    $api->listUnsubscribe($list_id, $user->email, true, false, false);

    if ($api->errorCode) {
      $error = "Unable to load listUnsubscribe()!\n";
      $error .= "\tCode=".$api->errorCode."\n";
      $error .= "\tMsg=".$api->errorMessage."\n";
      print_log($error);
    }
	}

  public function onUserCreateAfter()
  {
    include_once 'application/modules/Updates/Api/MCAPI.class.php';
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $apiKey = $settings->__get('updates.mailchimp.apikey');
    $api = new MCAPI($apiKey);

    if ($api->ping() != "Everything's Chimpy!") {
      // mailchimp api error
      return;
    }

    $userTbl = Engine_Api::_()->getDbTable('users','user');
    $select = $userTbl->select()
      ->from(array($userTbl->info('name')), array('email'))
      ->order('user_id DESC');
    $user = $userTbl->fetchRow($select);

    $list_id = $settings->__get('updates.mailchimp.listid');
    $merge_vars = array('FNAME'=>'', 'LNAME'=> '');

    $api->listSubscribe($list_id, $user->email, $merge_vars, 'html', false, false, false,false);

    if ($api->errorCode){
      $error = "Unable to load listSubscribe()!\n";
      $error .= "\tCode=".$api->errorCode."\n";
      $error .= "\tMsg=".$api->errorMessage."\n";
      print_log($error);
    }
  }
public function onUserUpdateAfter(){

}
}