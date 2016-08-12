<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Subscribers.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Model_DbTable_Subscribers extends Engine_Db_Table
{
  protected $_rowClass = "Updates_Model_Subscriber";
  protected $_allowed_levels = array(0);

	public function init()
	{
  	$levelsTb = Engine_Api::_()->getDbtable('levels', 'authorization');
  	$levels = $levelsTb->fetchAll($levelsTb->select());
  	foreach ($levels as $level)
  	{
  		if (Engine_Api::_()->getApi('core', 'authorization')->getPermission($level->level_id, 'updates', 'use'))
  		{
  			$this->_allowed_levels[] = $level->level_id;
  		}
  	}
	}

  public function getSubscriber($id = 0, $email = '')
  {
  	if ($id)
  	{
  		$select = $this->select()->where('subscriber_id = ?', $id)->limit(1);
  	}
  	elseif (trim($email) != '')
  	{
  		$select = $this->select()->where('email_address = ?', $email)->limit(1);
  	}
  	
  	return $this->fetchRow($select);
  }
  
  public function getSubscribedEmails($id, $limit=100, $type='updates')
  {
		$select = $this->select();

    if($type == 'updates') {
			$select->where('update_id!=?', $id);
    } elseif($type == 'campaign') {
      $select->where('campaign_id!=?', $id);
    }
		$select->limit($limit);

  	return $this->fetchAll($select);
  }

  public function getReceivedEmails($id, $type='updates')
  {
		$select = $this->select();

    if($type == 'updates') {
			$select->where('update_id=?', $id);
    } elseif($type == 'campaign') {
      $select->where('campaign_id=?', $id);
    }

  	return $this->fetchAll($select);
  }

	public function getTotalSubscribedEmails()
	{
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('s'=>$this->info('name')), array('COUNT(s.subscriber_id) AS subscriber_count'))
			->limit(1);

		$total = $this->fetchRow($select);
		return $total->subscriber_count;
	}
  
  public function getSubscribedUsers($update_id, $limit=100)
  {
  	$table = Engine_Api::_()->getItemTable('user');
    $select = $table->select()
      ->from($table->info('name'))
			->where("enabled=1")
			->where("updates_subscribed=1")
			->where("updates_update_id!=?", $update_id)
			->where("level_id IN(".implode(',',$this->_allowed_levels).")")
      //->where("user_id = 4")
			->order("user_id ASC")
			->limit($limit);

		return $table->fetchAll($select);
  }

  public function getReceivedUsers($id, $type='updates')
  {
  	$table = Engine_Api::_()->getItemTable('user');
    $select = $table->select()
      ->from($table->info('name'))
			->where("enabled=1")
			->where("updates_subscribed=1")
			->where("level_id IN(".implode(',',$this->_allowed_levels).")")
			->order("user_id ASC");

    if ($type == 'updates') {
      $select->where("updates_update_id=?", $id);
    } elseif($type == 'campaign') {
      $select->where("updates_campaign_id=?",$id);
    }
		return $table->fetchAll($select);
  }

  public function getReceivedUserCount($id, $type='updates')
  {
    $table = Engine_Api::_()->getItemTable('user');
    $select = $table->select()
      ->from($table->info('name'), array('COUNT(user_id) AS user_count'))
      ->where("enabled=1")
      ->where("updates_subscribed=1")
      ->where("level_id IN(" . implode(',',$this->_allowed_levels) . ")")
      ->order("user_id ASC");

    if ($type == 'updates') {
      $select->where("updates_update_id=?", $id);
    } elseif($type == 'campaign') {
      $select->where("updates_campaign_id=?",$id);
    }
    return $table->getAdapter()->fetchOne($select);
  }

  public function getReceivedEmailCount($id, $type='updates')
  {
    $select = $this->select()
      ->from($this->info('name'), array('COUNT(subscriber_id) AS subscriber_count'));

    if($type == 'updates') {
      $select->where('update_id=?', $id);
    } elseif($type == 'campaign') {
      $select->where('campaign_id=?', $id);
    }

    return $this->getAdapter()->fetchOne($select);
  }

  public function getSubscribedMembers()
  {
  	$table = Engine_Api::_()->getItemTable('user');
    $select = $table->select()
			->where("enabled=1")
			->where("updates_subscribed=1")
			->order("user_id ASC");

		return $table->fetchAll($select);
  }

	public function getTotalSubscribedUsers()
	{
		$table = Engine_Api::_()->getItemTable('user');
    $select = $table->select()
			->setIntegrityCheck(false)
			->from($table->info('name'), array('COUNT(user_id) AS user_count'))
			->where("enabled=1")
			->where("updates_subscribed=1")
			->where("level_id IN(".implode(',',$this->_allowed_levels).")")
			->limit(1);

		$total  = $table->fetchRow($select);

		return $total->user_count;
	}

  public function unsubscribeUser($unsubscribedEmail)
	{
    // Unsubscribe registered member
    $userTbl = Engine_Api::_()->getItemTable('user');
    $where = array('email = ?' => $unsubscribedEmail);
    $userTbl->update(array('updates_subscribed' => 0), $where);

    // Unsubscribe subscribers
    $subscriberTbl = Engine_Api::_()->getDbtable('subscribers', 'updates');
    $subscriberTbl->delete(array('email_address = ?' => $unsubscribedEmail));
  }

  public function unsubscribeUsers($unsubscribedEmails)
	{
    // Unsubscribe registered members
    foreach ($unsubscribedEmails as $email) {
      $userTbl = Engine_Api::_()->getItemTable('user');
      $userTbl->update(array('updates_subscribed' => 0), array('email = ?' => $email));
    }

    include_once 'application/modules/Updates/Api/MCAPI.class.php';

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $apiKey = $settings->__get('updates.mailchimp.apikey');
    $listId = $settings->__get('updates.mailchimp.listid');
    $api = new MCAPI($apiKey);

    // Getting lists
    $listsTbl = Engine_Api::_()->getDbTable('mailchimplists', 'updates');
    $select = $listsTbl->select()
      ->from(array($listsTbl->info('name')), array('list_id'));
    $lists = $listsTbl->fetchAll($select);

    // Unsubscribe users from all lists
    $values = array();
    foreach ($lists as $list) {
      $values[] = $api->listBatchUnsubscribe($list->list_id, $unsubscribedEmails, false, false, false);
    }
  }

	public function checkSentItems()
	{
		$userTb = Engine_Api::_()->getItemTable('users');
		$subsTb = Engine_Api::_()->getDbtable('subscribers', 'updates');
		$userSl = $userTb->select()
			->setIntegrityCheck(false)
			->from($userTb->info('name'), array('COUNT('.$userTb->info('name').'.user_id) AS users_count', 'COUNT()'))
			->join($subsTb->info('name'))
		;
	}
}
