<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Creditbadges.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Model_DbTable_Creditbadges extends Engine_Db_Table
{
  protected $_rowClass = 'Hebadge_Model_Creditbadge';


  public function getPaginator($params = array(), $show_disabled = false)
  {
    $select = $this->select()
        ->from(array('b' => $this->info('name')), new Zend_Db_Expr('b.*'));

    $select->order('b.credit ASC');

    if (!empty($params['text'])){
      $select->where('b.title LIKE ?', '%' . $params['text'] . '%');
    }

    if (!$show_disabled){
      $select->where('b.enabled = 1');
    }


    return Zend_Paginator::factory($select);

  }

  public function getOwnerMembersByBadgeIds($badge_ids, Core_Model_Item_Abstract $owner)
  {
    if (empty($badge_ids)){
      return array();
    }
    if (!$owner->getIdentity()){
      return array();
    }


    $table = Engine_Api::_()->getDbTable('creditmembers', 'hebadge');

    $select = $table->select()
        ->where('creditbadge_id IN (?)', $badge_ids)
        ->where('object_type = ?', $owner->getType())
        ->where('object_id = ?', $owner->getIdentity());


    $data = array();
    foreach ($table->fetchAll($select) as $item){
      $data[$item->creditbadge_id] = $item;
    }
    return $data;

  }

  public function getMemberPaginator(Core_Model_Item_Abstract $owner, $params = array(), $approved = true)
  {
    $memberTable = Engine_Api::_()->getDbTable('creditmembers', 'hebadge');

    $select = $this->select()
        ->from(array('b' => $this->info('name')), new Zend_Db_Expr('b.*'))
        ->join(array('m' => $memberTable->info('name')), 'm.creditbadge_id = b.creditbadge_id AND m.object_type = "' . $owner->getType() . '" AND m.object_id = ' . $owner->getIdentity(), array())
        ->order('m.creation_date DESC');

    if ($approved){
      $select->where('m.approved = 1');
    }

    if (!empty($params['text'])){
      $select->where('b.title LIKE ?', '%' . $params['text'] . '%');
    }

    return Zend_Paginator::factory($select);

  }

  public function checkOwnerRank(Core_Model_Item_Abstract $owner)
  {
    $creditUser = $this->getOwnerCredit($owner);
    if (!$creditUser){
      return ;
    }

    $badge_ids = array(0);
    foreach (Engine_Api::_()->getDbTable('creditmembers', 'hebadge')->getMembersByOwner($owner) as $item){
      $badge_ids[] = $item->creditbadge_id;
    }

    $select = $this->select()
        ->where('credit <= ?', $creditUser->earned_credit)
        ->where('creditbadge_id NOT IN(?)', $badge_ids)
        ->where('enabled = 1');

    $new_member_badges_ids = array();
    foreach ($this->fetchAll($select) as $item){
      $new_member_badges_ids[] = $item->creditbadge_id;
    }


    foreach (Engine_Api::_()->hebadge()->getTableItems($this, $new_member_badges_ids) as $badge){
      $badge->addMember($owner);
      if (!Engine_Api::_()->getDbTable('notifications', 'activity')->getNotificationByObjectAndType($owner, $badge, 'hebadgecredit_new')){
        Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($owner, $owner, $badge, 'hebadgecredit_new');
      }
    }


  }

  protected $owner_credit = array();
  protected $owner_rank = array();
  protected $owner_next_rank = array();


  public function getOwnerCredit(Core_Model_Item_Abstract $owner)
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('credit')){
      return ;
    }
    if (empty($this->owner_credit[$owner->getGuid()])){
      $table = Engine_Api::_()->getDbTable('balances', 'credit');
      $select = $table->select()
          ->where('balance_id = ?', $owner->getIdentity());
      $this->owner_credit[$owner->getGuid()] = $table->fetchRow($select);

    }


    return $this->owner_credit[$owner->getGuid()];
  }


  public function getOwnerRank(Core_Model_Item_Abstract $owner)
  {
    if (empty($this->owner_rank[$owner->getGuid()])){

      $creditUser = $this->getOwnerCredit($owner);

      if (!$creditUser){
        return;
      }

      $select = $this->select()
          ->where('enabled = 1')
          ->where('credit <= ?', $creditUser->earned_credit)
          ->order('credit DESC')
          ->limit(1);

      $this->owner_rank[$owner->getGuid()] = $this->fetchRow($select);

    }

    return $this->owner_rank[$owner->getGuid()];

  }


  public function getOwnerNextRank(Core_Model_Item_Abstract $owner)
  {
    if (empty($this->owner_next_rank[$owner->getGuid()])){

      $creditUser = $this->getOwnerCredit($owner);
      $ownerRank = $this->getOwnerRank($owner);

      $select = $this->select()
          ->where('enabled = 1')
          ->order('credit ASC')
          ->limit(1);

      if ($ownerRank){
        $select->where('credit > ?', $ownerRank->credit);
      }

      $this->owner_next_rank[$owner->getGuid()] = $this->fetchRow($select);

    }

    return $this->owner_next_rank[$owner->getGuid()];

  }



}