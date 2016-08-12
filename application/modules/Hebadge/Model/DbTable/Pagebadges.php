<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagebadges.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Model_DbTable_Pagebadges extends Engine_Db_Table
{
  protected $_rowClass = 'Hebadge_Model_Pagebadge';


  public function getPaginator($params = array(), $show_disabled = false)
  {
    $select = $this->select()
        ->from(array('b' => $this->info('name')), new Zend_Db_Expr('b.*'));

    $select->order('b.pagebadge_id DESC');

    if (!empty($params['text'])){
      $select->where('b.title LIKE ?', '%' . $params['text'] . '%');
    }

    if (!$show_disabled){
      $select->where('b.enabled = 1');
    }

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


    $table = Engine_Api::_()->getDbTable('pagemembers', 'hebadge');

    $select = $table->select()
        ->where('pagebadge_id IN (?)', $badge_ids)
        ->where('page_id = ?', $owner->getIdentity());


    $data = array();
    foreach ($table->fetchAll($select) as $item){
      $data[$item->pagebadge_id] = $item;
    }
    return $data;

  }

  public function getMemberPaginator(Core_Model_Item_Abstract $owner, $params = array(), $approved = true)
  {
    $memberTable = Engine_Api::_()->getDbTable('pagemembers', 'hebadge');

    $select = $this->select()
        ->from(array('b' => $this->info('name')), new Zend_Db_Expr('b.*'))
        ->join(array('m' => $memberTable->info('name')), 'm.pagebadge_id = b.pagebadge_id AND m.page_id = ' . $owner->getIdentity(), array())
        ->order('m.creation_date DESC');

    if ($approved){
      $select->where('m.approved = 1');
    }

    if (!empty($params['text'])){
      $select->where('b.title LIKE ?', '%' . $params['text'] . '%');
    }

    return Zend_Paginator::factory($select);

  }

}