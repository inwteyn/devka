<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_Plugin_Core
{

  public function removePage($event)
  {
    $payload = $event->getPayload();
	  $page = $payload['page'];

    if ( $page instanceof Page_Model_Page ) {

      $tbl = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
      $select = $tbl->select()
          ->where('page_id = ?', $page->getIdentity());

      $events = $tbl->fetchAll($select);

      foreach ($events as $event){
        $event->delete();
      }

    }

  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();

    if ( $payload instanceof User_Model_User ) {

      $tbl = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
      $select = $tbl->select()
          ->where('user_id = ?', $payload->getIdentity());

      $events = $tbl->fetchAll($select);

      foreach ($events as $event){
        $event->delete();
      }

      $membershipApi = Engine_Api::_()->getDbtable('pageeventmembership', 'pageevent');
      foreach( $membershipApi->getMembershipsOf($payload) as $event ) {
        $membershipApi->removeMember($event, $payload);
      }

    }

  }

  public function page_onPageEditPrivacy($event)
  {
    $payload = $event->getPayload();
    $page = $payload['page'];
    $values = $payload['values'];

    $auth = Engine_Api::_()->authorization()->context;
    $availableValues = array('everyone', 'registered', 'likes', 'team');

    $tbl = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
    $select = $tbl->select()
                  ->where('page_id = ?', $page->getIdentity());

    $pageevents = $tbl->fetchAll($select);

    foreach ($pageevents as $pageevent) {
      foreach ($availableValues as $index => $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        }
        elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

        $page_auth_view = array_search($values['auth_view'], $availableValues);
        $page_auth_comment = array_search($values['auth_comment'], $availableValues);
        $page_auth_posting = array_search($values['auth_posting'], $availableValues);

        if ($index < $page_auth_view && 1 === $auth->isAllowed($pageevent, $role, 'view')) {
          $auth->setAllowed($pageevent, $role, 'view');
        }

        $auth->setAllowed($pageevent, $role, 'comment', (int)($index >= $page_auth_comment));
        $auth->setAllowed($pageevent, $role, 'posting', (int)($index >= $page_auth_posting));
      }
    }
  }

}