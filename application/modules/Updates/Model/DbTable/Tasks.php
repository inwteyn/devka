<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Tasks.php 2012-03-13 10:45 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Model_DbTable_Tasks extends Engine_Db_Table
{
  public function getCurrentTask($updcamp_id, $type)
  {
    $select = '';
    if ($type == 'updates') {
    $select = $this->select()
      ->where('updcamp_id = ?', $updcamp_id)
      ->where('type = ?', $type);
    }
    elseif ($type == 'campaign') {
      $select = $this->select()
      ->where('updcamp_id = ?', $updcamp_id)
      ->where('type = ?', $type);
    }
    return $this->fetchRow($select);
  }

  public function getActiveTask()
  {
    $campaignsTbl = Engine_Api::_()->getDbTable('campaigns', 'updates');
    $now  = date('Y-m-d H:i:s', Engine_Api::_()->updates()->getTimestamp());

    $select = $this->select()
      ->where('finished = 0')
      ->where('cancelled = 0')
      ->order('task_id ASC');
    $tasks = $this->fetchAll($select);

    foreach ($tasks as $task) {
      if ($task->scheduled == 1) {
        $select = $campaignsTbl->select()
          ->from(array($campaignsTbl->info('name')), array('campaign_id'))
          ->where('planned_date < ?', $now)
          ->where('campaign_id = ?', $task->updcamp_id);
        $campaign = $this->fetchRow($select);

        if ($campaign) {
          $select = $this->select()
            ->where('finished = 0')
            ->where('cancelled = 0')
            ->where('updcamp_id = ?', $campaign->campaign_id)
            ->order('task_id ASC')
            ->limit(1);

          return $this->fetchRow($select);
        }
      }
      else {
        $select = $this->select()
          ->where('task_id = ?', $task->task_id);

        return $this->fetchRow($select);
      }
    }
  }

  public function cancelTask($task_id)
  {
    $this->update(array('cancelled' => 1), array('task_id = ?' => $task_id));

		$select = $this->select()
      ->order('task_id DESC');
    return $select;
  }

  public function restartTask($task_id)
  {
    $this->update(array('cancelled' => 0), array('task_id = ?' => $task_id));

		$select = $this->select()
      ->order('task_id DESC');
    return $select;
  }

  public function deleteTask($task_id)
  {
    $select = $this->select()
      ->from(array($this->info('name')), array('updcamp_id'))
      ->where('task_id = ?', $task_id);

    $campaign = $this->fetchRow($select);
    $campaign_id = $campaign->updcamp_id;

    $campaignTbl = Engine_Api::_()->getDbTable('campaigns', 'updates');
    $campaignTbl->update(array('finished' => 1), array('campaign_id = ?' => $campaign_id));

    $this->delete(array('task_id = ?' => $task_id));

    return $this;
  }


}