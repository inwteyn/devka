<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Follows.php 2012-09-18 16:50 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Model_DbTable_Follows extends Engine_Db_Table
{
  protected $_rowClass = 'Offers_Model_Follow';

  public function getFollows()
  {
    $select = $this->select()
      ->where('follow_status = ?', 'active');
    return $this->fetchAll($select);
  }

  public function getFollow($offer_id, $user_id)
  {
    $select = $this->select()
      ->where('offer_id = ?', $offer_id)
      ->where('user_id = ?', $user_id);
    return $this->fetchRow($select);
  }

  public function setFollowStatus($offer_id, $user_id, $status_type)
  {
    $followsTbl = Engine_Api::_()->getDbTable('follows', 'offers');
    $select = $followsTbl->select()
      ->from(array('f'=>$followsTbl->info('name')))
      ->where('offer_id = ?', $offer_id)
      ->where('user_id = ?', $user_id);
    $follow = $followsTbl->fetchRow($select);
    if ($follow) {
      if ($status_type == 'active') {
        $followsTbl->update(array('follow_status' => 'active'), array('offer_id = ?' => $offer_id, 'user_id = ?' => $user_id));
      }
      else {
        $followsTbl->update(array('follow_status' => 'finished'), array('offer_id = ?' => $offer_id, 'user_id = ?' => $user_id));
      }
    }
    else {
      $followsTbl->insert( array('offer_id' => $offer_id, 'user_id' => $user_id, 'follow_status' => 'active'));
    }
  }

  public function getFollowStatus($offer_id, $user_id)
  {
    $followsTbl = Engine_Api::_()->getDbTable('follows', 'offers');
    $select = $followsTbl->select()
      ->from(array('f'=>$followsTbl->info('name')))
      ->where('f.offer_id = ?', $offer_id)
      ->where('f.user_id = ?', $user_id);

    $follow = $followsTbl->fetchRow($select);
    if ($follow) {
      return $follow->follow_status;
    } else {
      return false;
    }
  }

}