<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Datas.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class SocialBoost_Model_DbTable_Datas extends Engine_Db_Table
{
  protected $_rowClass = "SocialBoost_Model_Data";

  public function getUsersData($user = null, $params = array())
  {
    if( !$user ) {
      $user = Engine_Api::_()->user()->getViewer();
    }

    if( !$user instanceof User_Model_User ) {
      return null;
    }

    $select = $this->select()
      ->where('user_id = ?', $user->getIdentity());

    if( isset($params['reward']) && $params['reward'] == 1 ) {
      $select->where('credit > 0 OR offer_id > 0');
    }

    return $this->fetchAll($select);
  }

  public function addUserData( $params = array() )
  {
    if( !isset($params['user_id']) || !$params['user_id']) {
      return false;
    }

    if( !isset($params['type']) || !$params['type'] ) {
      return false;
    }

    if( !isset($params['status']) || !$params['status'] ) {
      return false;
    }

    $row = $this->createRow();
    $row->user_id = $params['user_id'];
    $row->status = $params['status'];
    $row->type = $params['type'];

    $row->save();

    return true;
  }
}