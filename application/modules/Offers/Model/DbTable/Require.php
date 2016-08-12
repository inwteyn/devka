<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Require.php 2012-06-08 10:50 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Model_DbTable_Require extends Engine_Db_Table
{
  protected $_rowClass = "Offers_Model_Require";

  protected $_serializedColumns = array('params');

  public function getCompleteRequireIds(Core_Model_Item_Abstract $owner, Offers_Model_Offer $offer, $page_id = 0)
  {
    $completeTable = Engine_Api::_()->getDbTable('complete', 'offers');

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('r' => $this->info('name')), new Zend_Db_Expr('r.*'))
      ->join(array('c' => $completeTable->info('name')), 'c.require_id = r.require_id AND c.object_type = "' . $owner->getType() . '" AND object_id = ' . $owner->getIdentity() . '')
      ->where('r.offer_id = ?', $offer->getIdentity());

    if ($page_id > 0) {
      $select->where('c.page_id = ?', $page_id);
    }

    $ids = array();

    foreach ($this->fetchAll($select) as $data) {
      $ids[] = $data->require_id;
    }

    return $ids;
  }
}