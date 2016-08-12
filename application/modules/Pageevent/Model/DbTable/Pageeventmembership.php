<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pageeventmembership.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_Model_DbTable_Pageeventmembership extends Core_Model_DbTable_Membership
{
  protected $_name = 'page_eventmembership';
  protected $_type = 'pageevent';


  public function getMemberSelect(Core_Model_Item_Abstract $resource, $rsvp = 2, $only_friends = false)
  {
    $tbl = Engine_Api::_()->getDbTable('users', 'user');

    $select = $tbl->select()
        ->setIntegrityCheck(false)
        ->from(array('u' => $tbl->info('name')), new Zend_Db_Expr('u.*'))
        ->join(array('em' => $this->info('name')), 'em.user_id = u.user_id', array())
        ->where('em.resource_id = ?', $resource->getIdentity())
        ->where('em.active = 1')
        ->where('em.rsvp = ?', $rsvp)
        ->group('u.user_id');

    if ($only_friends){
      $friend_tbl = Engine_Api::_()->getDbTable('membership', 'user');
      $select
          ->join(array('m' => $friend_tbl->info('name')), 'm.user_id = u.user_id', array())
          ->where('m.active = 1');
    }

    return $select;
  }

  public function getMemberPaginator(Core_Model_Item_Abstract $resource, $rsvp = null, $only_friends = false)
  {
    $select = $this->getMemberSelect($resource, $rsvp, $only_friends);
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(4);
    return $paginator;
  }

  public function getWaitingCount(Core_Model_Item_Abstract $resource)
  {
     $select = $this->select()
        ->from($this->info('name'), array('member_count' => new Zend_Db_Expr('COUNT(user_id)')))
        ->where('resource_id = ?', $resource->getIdentity())
        ->where('active = 0');

    return $this->fetchRow($select)->member_count;
  }

  public function isResourceApprovalRequired(Core_Model_Item_Abstract $resource)
  {
    return $resource->approval;
  }

  public function isUserApproved(Core_Model_Item_Abstract $resource, User_Model_User $user)
  {
    $row = $this->getRow($resource, $user);
    if (!$row){
      return false;
    }
    return $row->user_approved;
  }

  public function getInviteMembersSelect(Core_Model_Item_Abstract $resource, User_Model_User $viewer)
  {
    $tbl = Engine_Api::_()->getDbTable('users', 'user');
    $member_tbl = Engine_Api::_()->getDbTable('membership', 'user');

    $select = $tbl->select()
        ->setIntegrityCheck(false)
        ->from(array('u' => $tbl->info('name')), new Zend_Db_Expr('u.*'))
        ->join(array('m' => $member_tbl->info('name')), 'm.user_id = u.user_id', array())
        ->where('m.resource_id = ?', $viewer->getIdentity())
        ->where('m.active = 1');

    return $select;

  }

  public function getInviteMembersSelectDisabled(Core_Model_Item_Abstract $resource, User_Model_User $viewer)
  {
    $tbl = Engine_Api::_()->getDbTable('users', 'user');
    $member_tbl = Engine_Api::_()->getDbTable('membership', 'user');

    return $tbl->select()
        ->setIntegrityCheck(false)
        ->from(array('u' => $tbl->info('name')), new Zend_Db_Expr('u.user_id'))
        ->join(array('m' => $member_tbl->info('name')), 'm.user_id = u.user_id', array())
        ->join(array('em' => $this->info('name')), 'em.user_id = u.user_id', array())
        ->where('m.resource_id = ?', $viewer->getIdentity())
        ->where('m.active = 1')
        ->where('em.resource_id = ?', $resource->getIdentity());
  }

  public function isFriends(Core_Model_Item_Abstract $resource, User_Model_User $viewer)
  {
    return $this->getInviteMembersSelect($resource, $viewer)->limit(1)->query()->rowCount();
  }


}