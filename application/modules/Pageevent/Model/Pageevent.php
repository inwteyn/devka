<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pageevent.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_Model_Pageevent extends Core_Model_Item_Abstract
{

  public function getDescription()
  {
    $tmpBody = Engine_String::strip_tags($this->description);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',
      'page_id' => $this->getPage()->url,
      'tab' => 'page_event',
      'content_id' => $this->getIdentity(),
    ), $params);

    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function view()
  {
    $this->view_count++;
    $this->save();
  }

  public function getParent($recurseType = null)
  {
    return $this->getPage();
  }


  /**
   * @return Page_Model_Page
   * */

  public function getPage()
  {
    return Engine_Api::_()->getDbTable('pages', 'page')->findRow($this->page_id);
  }

  public function membership()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbTable('pageeventmembership', 'pageevent'));
  }

  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function getAuthorizationItem()
  {
    return $this;
  }

  public function _delete()
  {
    // Delete Members
    $this->membership()->removeAllMembers();

    // Delete Page Search
    Engine_Api::_()->getDbTable('search', 'page')->deleteData($this);

    // Delete Actions
    $tbl = Engine_Api::_()->getDbTable('attachments', 'activity');
    $action_ids = $tbl->select()
        ->from($tbl->info('name'), new Zend_Db_Expr('action_id'))
        ->where('type = ?', $this->getType())
        ->where('id = ?', $this->getIdentity())
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    $tbl->delete(array(
      'type = ?' => $this->getType(),
      'id = ?' => $this->getIdentity()
    ));

    if ($action_ids){
      Engine_Api::_()->getDbTable('actions', 'activity')->delete(array(
        'action_id IN (?)' => $action_ids
      ));
    }

    // Remove Photo
    if ($this->photo_id){
      $photo = Engine_Api::_()->storage()->get($this->photo_id);
      if ($photo){ $photo->delete(); }
    }

    parent::_delete();

  }

  public function _postInsert()
  {
    parent::_postInsert();

    // Add Page Search
    Engine_Api::_()->getDbTable('search', 'page')->saveData($this);
    Engine_Api::_()->page()->sendNotification($this, 'post_pageevent');
  }

  public function _postUpdate()
  {
    parent::_postUpdate();

    // Add Page Search
    Engine_Api::_()->getDbTable('search', 'page')->saveData($this);
  }


  public function setPrivacy(array $values)
  {
    $page = $this->getPage();
    $auth = Engine_Api::_()->authorization()->context;

    // View privacy
    $roles = array('team', 'likes', 'registered', 'everyone');
    $viewMax = array_search($values['auth_view'], $roles);

  	foreach( $roles as $i => $role ){
      if( $role === 'team' ) {
      	$role = $page->getTeamList();
      }
      elseif ( $role === 'likes' ) {
      	$role = $page->getLikesList();
      }
      $auth->setAllowed($this, $role, 'view', (int)($i <= $viewMax));
    }

    if ($values['auth_view'] == 'registered' || $values['auth_view'] == 'everyone'){
      $auth->setAllowed($this, $page->getLikesList(), 'view', 1);
    }

    $auth->setAllowed($this, $page->getTeamList(), 'view', 1);

    // Comment privacy
    $roles = array('team', 'likes', 'registered');
  	$commentMax = array_search($values['auth_comment'], $roles);
    foreach( $roles as $i => $role ){
      if( $role === 'team' ) {
      	$role = $page->getTeamList();
      }
      elseif ( $role === 'likes' ) {
      	$role = $page->getLikesList();
      }

      if(1 === $auth->isAllowed($page, $role, 'comment')){
        $auth->setAllowed($this, $role, 'comment', (int)($i <= $commentMax) );
      }
    }

    if ($values['auth_comment'] == 'registered'){
      $auth->setAllowed($this, $page->getLikesList(), 'comment', 1);
    }

    $auth->setAllowed($this, $page->getTeamList(), 'comment', 1);

    // Posting privacy
    $roles = array('team', 'likes', 'registered');
  	$postingMax = array_search($values['auth_posting'], $roles);
    foreach ($roles as $i => $role) {
      if( $role === 'team' ) {
      	$role = $page->getTeamList();
      }
      elseif ( $role === 'likes' ) {
      	$role = $page->getLikesList();
      }
      if (1 === $auth->isAllowed($page, $role, 'event_posting')) {
        $auth->setAllowed($this, $role, 'posting', (int)($i <= $postingMax) );
      }
    }

    if ($values['auth_posting'] == 'registered'){
      $auth->setAllowed($this, $page->getLikesList(), 'posting', 1);
    }

    $auth->setAllowed($this, $page->getTeamList(), 'posting', 1);
  }
}