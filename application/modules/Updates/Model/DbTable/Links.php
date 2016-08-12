<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Links.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Model_DbTable_Links extends Engine_Db_Table
{
  protected $_name = 'updates_links';
  
  public function getReferredLinks($date = 0, $type='updates')
  {
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from($this->info('name'), array('sum(referred_count) AS referred_count', 'link'))
      ->where('type=?', $type)
      ->group('link')
      ->order('referred_count DESC')
      ->limit(10);

  	if ($date)
  	{
  		$select->where('referred_date = ?', $date);
  	}

  	return $this->fetchAll($select);
  }

  public function getReferredModules($date = 0, $type='updates')
  {
     $select = $this->select()
        ->setIntegrityCheck(false)
        ->from($this->info('name'), array('sum(referred_count) AS referred_count', 'module'))
        ->where('type=?', $type)
        ->group('module')
        ->order('referred_count DESC')
        ->limit(10);

  	if ($date)
  	{
      $select->where('referred_date = ?', $date);
  	}

  	return $this->fetchAll($select);
  }

  public function getTotalReferreds($date = 0, $type='updates')
  {
   $select = $this->select()
        ->setIntegrityCheck(false)
        ->from($this->info('name'), array('sum(referred_count) AS referreds'))
        ->where('type=?', $type)
        ->limit(1);

  	if ($date)
  	{
      $select->where('referred_date = ?', $date);
  	}

  	return $this->fetchRow($select);
  }
}
