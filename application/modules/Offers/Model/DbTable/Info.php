<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Info.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Offers_Model_DbTable_Info extends Engine_Db_Table
{
  public function getInfo(User_Model_User $user)
  {
    $select = $this->select()
        ->where('user_id = ?', $user->getIdentity());

    $row = $this->fetchRow($select);

    if (!$row){
      $row = $this->createRow(array('user_id' => $user->getIdentity()));
      $row->save();
    }

    return $row;

  }

}