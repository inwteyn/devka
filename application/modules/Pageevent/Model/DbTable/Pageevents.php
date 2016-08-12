<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pageevents.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_Model_DbTable_Pageevents extends Engine_Db_Table
{

  protected $_name = 'page_events';
  protected $_rowClass = 'Pageevent_Model_Pageevent';


  /**
   * @return Zend_Paginator
   * */

  public function getPaginator($page_id, $show, $page = 1, $viewer_id = 0, $ipp)
  {
    $pageevent_ids = $this->getPageeventIds($page_id);

    $selectEvent = $this->select()
      ->where('page_id = ?', $page_id);
    if (!empty($pageevent_ids)) {
      $selectEvent->where('pageevent_id IN(?)', $pageevent_ids);
    } else {
      $selectEvent->where('pageevent_id = 0');
    }

    // Show Past Events
    if ($show == 'past'){
      $selectEvent->where('endtime < FROM_UNIXTIME(?)', time());
    }
    // Show User Events
    else if ($show == 'user' && $viewer_id)
    {
      $selectEvent->where('user_id = ?', $viewer_id);
    }
    // Show Upcoming Events
    else {
      $selectEvent->where('endtime > FROM_UNIXTIME(?)', time());
    }

    $selectEvent->order('starttime ASC');
    $paginator = Zend_Paginator::factory($selectEvent);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage($ipp);

    return $paginator;
  }


  // return events
  public function getEvents($events)
  {
    if (!$events) {
      return array();
    }
    else {
      $tableEvents = Engine_Api::_()->getDbTable('events', 'event');
      $selectEventEvents = $tableEvents->select()->where('event_id IN (?)', $events);
      $resultEvents = $tableEvents->fetchAll($selectEventEvents);
      return $resultEvents;
    }
  }

  // return page events
  public function getPageevents($pageevents)
  {
    if (!$pageevents) {
      return array();
    }
    else {
      $tablePageevents = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
      $selectPageevents = $tablePageevents->select()->where('pageevent_id IN (?)', $pageevents);
      $resultPageevents = $tablePageevents->fetchAll($selectPageevents);
      return $resultPageevents;
    }
  }

  public function getEventsMembership($events)
  {
    if (!$events) {
      return array();
    }
    else {
      $tableEventsMembership = Engine_Api::_()->getDbTable('events', 'event');
      $selectEventEventsMembership = $tableEventsMembership->select()->where('event_id  IN (?)', $events);
      $resultEventsMembership = $tableEventsMembership->fetchAll($selectEventEventsMembership);
      return $resultEventsMembership;
    }
  }

  public function getPageeventsMembership($pageevents)
  {
    if (!$pageevents) {
      return array();
    }
    else {
      $tablePageeventsMembership = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
      $selectEventPageeventsMembership = $tablePageeventsMembership->select()->where('pageevent_id  IN (?)', $pageevents);
      $resultPageeventsMembership = $tablePageeventsMembership->fetchAll($selectEventPageeventsMembership);
      return $resultPageeventsMembership;
    }
  }

  public function getCount($page_id)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = Engine_Db_Table::getDefaultAdapter();

    $selectPageevent = $db->select()->from(array('pe' => 'engine4_page_events'), array('count' => new Zend_Db_Expr('COUNT(*)')));
    $selectPageevent = $selectPageevent->joinInner(array('aa' => 'engine4_authorization_allow'), 'aa.resource_id = pe.pageevent_id
                                       AND aa.resource_type = \'pageevent\' AND aa.action = \'view\'', array());
    if ($viewer->getIdentity()) {
      $selectPageevent = $selectPageevent->joinLeft(array('pl' => 'engine4_page_lists'), 'pl.list_id = aa.role_id', array())
        ->joinLeft(array('pli' => 'engine4_page_listitems'), 'pl.list_id = pli.list_id AND pli.child_id = '. $viewer->getIdentity(), array())
        ->where('pe.page_id = (?) AND (aa.role IN (\'everyone\', \'registered\') OR pli.child_id IS NOT NULL)', $page_id)
        ->where('pe.endtime > FROM_UNIXTIME(?)', time())
        ->group('pe.pageevent_id');
    }
    else{
      $selectPageevent = $selectPageevent->where('pe.page_id = (?)', $page_id)
        ->where('aa.role IN (\'everyone\')')
        ->where('pe.endtime > FROM_UNIXTIME(?)', time())
        ->group('pe.pageevent_id');
    }

    $count = $selectPageevent->query()->fetch();
    return $count['count'];
  }

  public function getPageeventIds($page_id)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = Engine_Db_Table::getDefaultAdapter();

    $selectPageevent = $db->select()->from(array('pe' => 'engine4_page_events'));
    $selectPageevent = $selectPageevent->joinInner(array('aa' => 'engine4_authorization_allow'), 'aa.resource_id = pe.pageevent_id
                                       AND aa.resource_type = \'pageevent\' AND aa.action = \'view\'');
    if ($viewer->getIdentity()) {
      $selectPageevent = $selectPageevent->joinLeft(array('pl' => 'engine4_page_lists'), 'pl.list_id = aa.role_id');
      $selectPageevent = $selectPageevent->joinLeft(array('pli' => 'engine4_page_listitems'), 'pl.list_id = pli.list_id
                                          AND pli.child_id = '. $viewer->getIdentity());
      $selectPageevent = $selectPageevent->where('pe.page_id = (?) AND (aa.role IN (\'everyone\', \'registered\') OR pli.child_id IS NOT NULL)', $page_id);
      $selectPageevent = $selectPageevent->group('pe.pageevent_id');
    }
    else{
      $selectPageevent = $selectPageevent->where('pe.page_id = (?)', $page_id);
      $selectPageevent = $selectPageevent->where('aa.role IN (\'everyone\')');
      $selectPageevent = $selectPageevent->group('pe.pageevent_id');
    }
    return $db->fetchCol($selectPageevent);
  }

}
