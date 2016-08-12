<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Actions.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Model_DbTable_Actions extends Wall_Model_DbTable_Actions
{
  protected $_rowClass = 'Timeline_Model_Action';
  protected $_name = 'activity_actions';


  public function getActivityAbout(Core_Model_Item_Abstract $about, User_Model_User $user,
                                   array $params = array())
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $db = Engine_Db_Table::getDefaultAdapter();

    // Get all params to format the feed
    $limit = (empty($params['limit'])) ? $settings->getSetting('activity.length', 20) : (int)$params['limit'];
    $max_id = (empty($params['max_id'])) ? null : (int)$params['max_id'];
    $min_id = (empty($params['min_id'])) ? null : (int)$params['min_id'];
    $hideIds = (empty($params['hideIds'])) ? null : $params['hideIds'];
    $showTypes = (empty($params['showTypes'])) ? null : $params['showTypes'];
    $hideTypes = (empty($params['hideTypes'])) ? null : $params['hideTypes'];
    $min_date = (empty($params['min_date'])) ? null : $params['min_date'];
    $max_date = (empty($params['max_date'])) ? null : $params['max_date'];


    // Get allowed actions types
    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $select = $tableTypes->select()
        ->where('enabled = 1')
        ->where('displayable & 1 OR displayable & 2') // User and Object Profile
        ->where('module IN (?)', Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames());

    $total_types = $tableTypes->fetchAll($select);

    $types = array();
    foreach ($total_types as $item) {
      $types[] = $item->type;
    }
    if (!empty($showTypes) && is_array($showTypes)) {
      $types = array_intersect($types, $showTypes);
    }
    if (!empty($hideTypes) && is_array($hideTypes)) {
      $types = array_diff($types, $hideTypes);
    }
    $subjectActionTypes = array(0);
    $objectActionTypes = array(0);

    foreach ($total_types as $type) {
      if ($type->displayable & 1) {
        $subjectActionTypes[] = $type->type;
      }
      if ($type->displayable & 2) {
        $objectActionTypes[] = $type->type;
      }
    }
    if (empty($types)) {
      return null;
    }

    $action_ids = null;

    // Replace the dates to action ids
    if ($min_date !== null || $max_date !== null){

      // Get all action ids by the period
      $actionTable = Engine_Api::_()->getDbTable('actions', 'activity');
      $select = $actionTable
          ->select()
          ->from($actionTable->info('name'), array('action_id'));

      if (!empty($max_date)){
        $select->where('date < ?', $max_date);
      }
      if (!empty($min_date)){
        $select->where('date > ?', $min_date);
      }
      if (!empty($hideIds) && is_array($hideIds)){
        $select->where('action_id NOT IN (?)', $hideIds);
      }

      // if an action belongs to the user or object
      $select->where(new Zend_Db_Expr("
        (
          subject_type = '" . $about->getType() . "' AND
          subject_id = " . $about->getIdentity() . " AND
          type IN ('" . implode("','", $subjectActionTypes) . "')
        ) OR (
          object_type = '" . $about->getType() . "'
          AND object_id = " . $about->getIdentity() . " AND
          type IN ('" . implode("','", $objectActionTypes) . "')
        )"
      ))
          ->order('date DESC')
          ->limit(100) // take more than necessary
      ;
      $action_ids = array();
      foreach ($db->fetchAll($select) as $action){
        $action_ids[] = $action['action_id'];
      }
    }

    // Get relationship user with network
    // him friends, group / events memberships and etc
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
      'about' => $about
    ));
    $responses = (array)$event->getResponses();
    if (empty($responses)) {
      return null;
    }

    $streamTable = Engine_Api::_()->getDbTable('stream', 'activity');
    $union = new Zend_Db_Select($db);

    foreach ($responses as $response)
    {
      if (empty($response)) continue;
      $select = $streamTable->select()
          ->from($streamTable->info('name'), 'action_id')
          ->where('target_type = ?', $response['type']);

      if (empty($response['data'])) {
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        $select->where('target_id IN(?)', (array)$response['data']);
      } else {
        continue;
      }

      if (!empty($action_ids)){
        $select->where('action_id IN (?)', $action_ids); // actions ids in max_date and min_date period
      } else if (null !== $min_id) {
        $select->where('action_id >= ?', $min_id);
      } else if (null !== $max_id) {
        $select->where('action_id <= ?', $max_id);
      }
      if (!empty($hideIds) && is_array($hideIds)) {
        $select->where('action_id NOT IN (?)', $hideIds);
      }

      // if an action belongs to the user or object
      $select->where(new Zend_Db_Expr("
        (
          subject_type = '" . $about->getType() . "' AND
          subject_id = " . $about->getIdentity() . " AND
          type IN ('" . implode("','", $subjectActionTypes) . "')
        ) OR (
          object_type = '" . $about->getType() . "'
          AND object_id = " . $about->getIdentity() . " AND
          type IN ('" . implode("','", $objectActionTypes) . "')
        )"
      ));

      $select
          ->order('action_id DESC')
          ->limit($limit);

      // Hide all Page actions on user profile
      if ($about->getType() == 'user') {
        if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page')) {
          $data = Engine_Api::_()->getDbtable('membership', 'page')->getMembershipsOfIds($about);
          if (!empty($data)) {
            $select->where('!(object_type = "page" AND object_id IN (?))', $data);
          }
        }
      }
      $union->union(array('(' . $select->__toString() . ')'));
    }

    // Add to query tagged actions
    if ($about->getType() == 'user') {

      $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
      $tableTag = Engine_Api::_()->getDbTable('tags', 'wall');

      // get friends ids
      $friend_ids = array(0);
      $data = $data = $user->membership()->getMembershipsOfIds();
      if (!empty($data)) {
        $friend_ids = array_merge($friend_ids, $data);
      }

      $tagWhere = '
        (t.object_type = "user" AND t.object_id = ' . $about->getIdentity() . ')
        AND
        (
        (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
        OR ((p.privacy = "networks" OR p.privacy = "members") AND t.object_type = "user" AND t.object_id IN (' . implode(",", $friend_ids) . ') )
        OR ((p.privacy = "owner" OR p.privacy = "page") AND t.object_type = "user" AND t.object_id = ' . $user->getIdentity() . ')
        )
      ';

      $selectTag = $tableTag->select()
          ->setIntegrityCheck(false)
          ->from(array('t' => $tableTag->info('name')), array('t.action_id'))
          ->join(array('p' => $privacyTable->info('name')), 'p.action_id = t.action_id', array())
          ->where(new Zend_Db_Expr($tagWhere));

      if (!empty($action_ids)){
        $select->where('action_id IN (?)', $action_ids); // actions ids in max_date and min_date period
      } else if (null !== $min_id) {
        $selectTag->where('t.action_id >= ?', $min_id);
      } else if (null !== $max_id) {
        $selectTag->where('t.action_id <= ?', $max_id);
      }
      if (!empty($hideIds) && is_array($hideIds)) {
        $selectTag->where('t.action_id NOT IN (?)', $hideIds);
      }
      $selectTag->group('t.action_id');

      $union->union(array('(' . $selectTag->__toString() . ')'));
    }

    $union
        ->order('action_id DESC')
        ->limit($limit);

    $actions = $db->fetchAll($union);

    if (empty($actions)) {
      return null;
    }

    // Get actions themselves by action ids
    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    return $this->fetchAll(
      $this->select()
          ->where('action_id IN(' . join(',', $ids) . ')')
          ->order('action_id DESC')
          ->limit($limit)
    );
  }

  protected function _getInfo(array $params)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
      'limit' => $settings->getSetting('activity.length', 20),
      'action_id' => null,
      'max_id' => null,
      'min_id' => null,
      'max_date' => null,
      'min_date' => null,
      'showTypes' => null,
      'hideTypes' => null,
      'hideIds' => null,
    );
    $newParams = array();
    foreach ($args as $arg => $default) {
      if (!empty($params[$arg])) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
    }
    return $newParams;
  }
}