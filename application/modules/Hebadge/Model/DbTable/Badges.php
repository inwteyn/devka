<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Badges.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Model_DbTable_Badges extends Engine_Db_Table
{
  protected $_rowClass = 'Hebadge_Model_Badge';

  public function getPaginator($params = array(), $order = 'popular', $show_disabled = false)
  {
    $select = $this->select()
        ->from(array('b' => $this->info('name')), new Zend_Db_Expr('b.*'));

    if ($order == 'popular'){
      $select->order('b.member_count DESC');
    } else if ($order == 'recent'){
      $select->order('b.badge_id DESC');
    }

    if (!empty($params['text'])){
      $select->where('b.title LIKE ?', '%' . $params['text'] . '%');
    }

    //We select badges as level_type
    if (empty($params['levels'])) {
      $select->where("level_type = ?", 0);
    } else {
      $select->where("level_type = ?", 1);
    }

    if (!$show_disabled){
      $select->where('b.enabled = 1');
    }

    return Zend_Paginator::factory($select);

  }

  public function getMemberPaginator(Core_Model_Item_Abstract $owner, $params = array(), $approved = true)
  {
    $memberTable = Engine_Api::_()->getDbTable('members', 'hebadge');
    if ($owner->getType() == 'user') {
//    //Check on adding of the user on the list of users of LevelBadges
      $memberTable->checkBeforeAdditionBadgeMembers($owner);
      $memberTable->checkBeforeRemovalBadgeMembers($owner);
    }


    $select = $this->select()
        ->from(array('b' => $this->info('name')), new Zend_Db_Expr('b.*'))
        ->join(array('m' => $memberTable->info('name')), 'm.badge_id = b.badge_id AND m.object_type = "' . $owner->getType() . '" AND m.object_id = ' . $owner->getIdentity(), array())
        ->order('m.creation_date DESC');
    if ($approved){
      $select->where('m.approved = 1');
    }

    if (!empty($params['text'])){
      $select->where('b.title LIKE ?', '%' . $params['text'] . '%');
    }
    $select->where('b.enabled = 1');

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

    $table = Engine_Api::_()->getDbTable('members', 'hebadge');
    $select = $table->select()
        ->where('badge_id IN (?)', $badge_ids)
        ->where('object_type = ?', $owner->getType())
        ->where('object_id = ?', $owner->getIdentity());

    $data = array();
    foreach ($table->fetchAll($select) as $item){
      $data[$item->badge_id] = $item;
    }

    return $data;

  }

  public function getFriendMemberPaginator(Core_Model_Item_Abstract $owner, $params = array())
  {
    $memberTable = Engine_Api::_()->getDbTable('members', 'hebadge');
    $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');

    $select = $memberTable->select()
        ->from(array('m' => $memberTable->info('name')), new Zend_Db_Expr('b.*'))
        ->join(array('mu' => $membershipTable->info('name')), 'm.object_type = "user" AND m.object_id = mu.resource_id AND mu.user_id = ' . $owner->getIdentity() . ' AND mu.active = 1', array())
        ->join(array('b' => $this->info('name')), 'b.badge_id = m.badge_id', array())
        ->order('b.member_count DESC');

    if (!empty($params['text'])){
      $select->where('b.title LIKE ?', '%' . $params['text'] . '%');
    }

    $select->where('b.enabled = 1');

    return Zend_Paginator::factory($select);

  }


  public function getOwnerNextBadges(Core_Model_Item_Abstract $owner)
  {
    $owner_badge_ids = array(0);
    foreach (Engine_Api::_()->getDbTable('members', 'hebadge')->getMembersByOwner($owner) as $item){
      $owner_badge_ids[] = $item->badge_id;
    }


    $requireTable = Engine_Api::_()->getDbTable('require', 'hebadge');
    $completeTable = Engine_Api::_()->getDbTable('complete', 'hebadge');

    $select = $requireTable->select()
        ->from(array('r' => $requireTable->info('name')), new Zend_Db_Expr('r.badge_id, FLOOR(COUNT(c.require_id)/COUNT(r.require_id)*100) AS procent'))
        ->join(array('b' => Engine_Api::_()->getDbTable('badges', 'hebadge')->info('name')), 'b.badge_id = r.badge_id', array())
        ->joinLeft(array('c' => $completeTable->info('name')), "c.object_type = '".$owner->getType()."' AND c.object_id = ".$owner->getIdentity()." AND r.require_id = c.require_id", array())
        ->where('r.badge_id NOT IN (?)', $owner_badge_ids)
        ->where('b.enabled = 1')
        ->group('r.badge_id')
        ->order('procent DESC');


    return Zend_Paginator::factory($select);

  }

  public function getForAdditionLevelBadges($subject)
  {
    $select = $this
      ->select()
      ->from(array('b' => $this->info('name')), 'b.*')
      ->where('b.level_type = ?', 1);
    $badges = $this->fetchAll($select);

    return $badges;
  }

  public  function getAddedLevelBadges($subject)
  {
    $memberTable = Engine_Api::_()->getDbTable('members', 'hebadge');

    $select = $this->select()
      ->from(array('b' => $this->info('name')), new Zend_Db_Expr('b.*'))
      ->join(array('m' => $memberTable->info('name')), 'm.badge_id = b.badge_id AND m.object_type = "' . $subject->getType() . '" AND m.object_id = ' . $subject->getIdentity(), array())
      ->where('b.level_type = 1');
    return $this->fetchAll($select);
  }
}