<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Updates.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
  
class Updates_Model_DbTable_Campaigns extends Engine_Db_Table
{
  protected $_rowClass = "Updates_Model_Campaign";
  protected $_serializedColumns = array('recievers');

  public function getInsertId()
  {
		$status = $this->_db->query("SHOW TABLE STATUS LIKE '".$this->info('name'). "' ")->fetch();
		return $status['Auto_increment'];
  }

  public function getCampaign($id)
  {
    return $this->fetchRow($this->select()->where('campaign_id = ?', $id)->limit(1));
  }

  public function getActiveCampaign()
  {
    $date  = date('Y-m-d H:i:s', strtotime(Engine_Api::_()->updates()->getDatetime()));

    $select = $this->select()
          ->where('finished = ?', 0)
          ->where("planned_date <= ?", $date)
          ->order('type DESC')
          ->order('planned_date ASC')
          ->order('creation_date ASC')
          ->limit(1);

    return $this->fetchRow($select);
  }

  public function getLastEditedCampaign()
  {
    return $this->fetchRow($this->select()->order('creation_date DESC')->limit(1));
  }

  public function getActiveCampaigns()
  {
    $templateTb = Engine_Api::_()->getDbtable('templates', 'updates');
    $date  = date('Y-m-d H:i:s', strtotime(Engine_Api::_()->updates()->getDatetime()));

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c'=>$this->info('name')))
      ->joinLeft(array('t'=>$templateTb->info('name')), 't.template_id=c.template_id', array('t.subject'))
      ->where('c.finished = ?', 0)
      ->where("c.planned_date <= ?", $date)
      ->order('c.type DESC')
      ->order('c.planned_date ASC')
      ->order('c.creation_date ASC');

    return $select;
  }

  public function getScheduleCampaigns()
  {
    $templateTb = Engine_Api::_()->getDbtable('templates', 'updates');
    $date  = date('Y-m-d H:i:s', strtotime(Engine_Api::_()->updates()->getDatetime()));

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c'=>$this->info('name')))
      ->joinLeft(array('t'=>$templateTb->info('name')), 't.template_id=c.template_id', array('t.subject'))
      ->where('c.type=?', 'schedule')
      ->where('c.finished=?', 0)
      ->where("c.planned_date > ?", $date)
      ->order('c.planned_date DESC');

    return $select;
  }

  public function getSentCampaigns()
  {
    $templateTb = Engine_Api::_()->getDbtable('templates', 'updates');

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c'=>$this->info('name')))
      ->joinLeft(array('t'=>$templateTb->info('name')), 't.template_id=c.template_id', array('t.subject'))
      ->where('c.type=?', 'instant')
      ->where('c.finished=?', 1)
      ->order('c.creation_date DESC');

    return $select;
  }

  public function getTotalActiveCampaigns()
  {
    $date  = date('Y-m-d H:i:s', strtotime(Engine_Api::_()->updates()->getDatetime()));

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from($this->info('name'), 'COUNT(campaign_id) as total')
      ->where('finished = ?', 0)
      ->where("planned_date <= ?", $date)
      ->group('type')
      ->order('type ASC');

    return $this->fetchAll($select);
  }

  public function getTotalFutureScheduleCampaigns()
  {
    $date  = date('Y-m-d H:i:s', strtotime(Engine_Api::_()->updates()->getDatetime()));

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from($this->info('name'), 'COUNT(campaign_id) as total')
      ->where('type=?', 'schedule')
      ->where('finished=?', 0)
      ->where("planned_date > ?", $date)
      ->group('type');

    return $this->fetchRow($select);
  }

  public function getLastSentScheduleCampaign()
  {
    $templateTb = Engine_Api::_()->getDbtable('templates', 'updates');
    $date  = date('Y-m-d H:i:s', strtotime(Engine_Api::_()->updates()->getDatetime()));

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c'=>$this->info('name')))
      ->joinLeft(array('t'=>$templateTb->info('name')), 't.template_id=c.template_id', array('t.subject'))
      ->where('c.finished = ?', 1)
      ->where("c.planned_date <= ?", $date)
      ->where('c.type = ?', 'schedule')
      ->order('c.planned_date DESC')
      ->limit(1);

    return $this->fetchRow($select);
  }

  public function getNextSendScheduleCampaign()
  {
    $templateTb = Engine_Api::_()->getDbtable('templates', 'updates');
    $date  = date('Y-m-d H:i:s', strtotime(Engine_Api::_()->updates()->getDatetime()));

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c'=>$this->info('name')))
      ->joinLeft(array('t'=>$templateTb->info('name')), 't.template_id=c.template_id', array('t.subject'))
      ->where('c.finished = ?', 0)
      ->where("c.planned_date > ?", $date)
      ->where('c.type = ?', 'schedule')
      ->order('c.planned_date ASC')
      ->limit(1);
    
    return $this->fetchRow($select);
  }
}