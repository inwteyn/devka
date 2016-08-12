<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Category.php 2012-07-25 16:28 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Model_Category extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  public function getUsedCount()
  {
    $eventTable = Engine_Api::_()->getItemTable('offer');
    return $eventTable->select()
        ->from($eventTable, new Zend_Db_Expr('COUNT(offer_id)'))
        ->where('category_id = ?', $this->category_id)
        ->query()
        ->fetchColumn();
  }

  public function isOwner(Core_Model_Item_Abstract $owner)
  {
    return false;
  }

  public function getOwner($recurseType = null)
  {
    return $this;
  }
}
