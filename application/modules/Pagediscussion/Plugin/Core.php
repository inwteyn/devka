<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Plugin_Core
{

  public function removePage($event)
  {
    $payload = $event->getPayload();
	  $page = $payload['page'];

    if ( $page instanceof Page_Model_Page ) {

      $tbl_topic = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');
      $tbl_post = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');

      $select = $tbl_topic->select()
          ->where('page_id = ?', $page->getIdentity());

      $topics = $tbl_topic->fetchAll($select);
      foreach ($topics as $topic) {
        $topic->delete();
      }

      $select = $tbl_post->select()
          ->where('page_id = ?', $page->getIdentity());

      $posts = $tbl_post->fetchAll($select);
      foreach ($posts as $post) {
        $post->delete();
      }

    }

  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();

    if ( $payload instanceof User_Model_User ) {

      $tbl_topic = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');
      $tbl_post = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');

      $select = $tbl_topic->select()
          ->where('user_id = ?', $payload->getIdentity());

      $topics = $tbl_topic->fetchAll($select);
      foreach ($topics as $topic) {
        $topic->delete();
      }

      $select = $tbl_post->select()
          ->where('user_id = ?', $payload->getIdentity());
      $posts = $tbl_post->fetchAll($select);

      foreach ($posts as $post) {
        $post->delete();
      }

    }

  }

}