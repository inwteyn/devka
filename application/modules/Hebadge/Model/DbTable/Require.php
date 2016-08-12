<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Require.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Model_DbTable_Require extends Engine_Db_Table
{
  protected $_rowClass = 'Hebadge_Model_Require';
  protected $_serializedColumns = array('params');


  public function getCompleteRequireIds(Core_Model_Item_Abstract $owner, Hebadge_Model_Badge $badge)
  {
    $completeTable = Engine_Api::_()->getDbTable('complete', 'hebadge');

    $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('r' => $this->info('name')), new Zend_Db_Expr('r.*'))
        ->join(array('c' => $completeTable->info('name')), 'c.require_id = r.require_id AND c.object_type = "' . $owner->getType() . '" AND object_id = '. $owner->getIdentity() . '')
        ->where('r.badge_id = ?', $badge->getIdentity());

    $ids = array();
    foreach ($this->fetchAll($select) as $data){
      $ids[] = $data->require_id;
    }

    return $ids;

  }




}