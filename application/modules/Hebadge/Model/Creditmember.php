<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Creditmember.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Model_Creditmember extends Core_Model_Item_Abstract
{

  protected $_searchTriggers = array();

  public function setApproved($approved = true)
  {
    $badge = Engine_Api::_()->getItem('hebadge_creditbadge', $this->creditbadge_id);

    if ($approved){
      $this->approved = 1;
      $badge->member_count++;
    } else {
      $this->approved = 0;
      $badge->member_count--;
    }
    $this->save();
    $badge->save();

  }


  public function getObjectGuid($asArray = false)
  {
    if ($asArray){
      return array($this->object_type, $this->object_id);
    } else {
      return sprintf('%s_%d', $this->object_type, $this->object_id);
    }
  }


}