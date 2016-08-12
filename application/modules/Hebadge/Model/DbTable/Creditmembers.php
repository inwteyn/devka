<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Creditmembers.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Model_DbTable_Creditmembers extends Engine_Db_Table
{
  protected $_rowClass = 'Hebadge_Model_Creditmember';
  protected $_members = array();

  public function getMembersByOwner(Core_Model_Item_Abstract $owner)
  {
    if (empty($this->_members[$owner->getGuid()])){

      $select = $this->select()
          ->where('object_type = ?', $owner->getType())
          ->where('object_id = ?', $owner->getIdentity());

      $members = $this->fetchAll($select);

      $this->_members[$owner->getGuid()] = $members;
    }
    return $this->_members[$owner->getGuid()];
  }


}