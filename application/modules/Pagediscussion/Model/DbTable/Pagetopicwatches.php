<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagetopicwatches.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Model_DbTable_Pagetopicwatches extends Engine_Db_Table
{
  protected $_name = 'page_topicwatches';

  public function setWatch($page_id, $topic_id, $user_id, $watch)
  {
    if (!$page_id || !$topic_id || !$user_id) {
      return false;
    }
    $this->delete(array(
      'resource_id = ?' => $page_id,
      'topic_id = ?' => $topic_id,
      'user_id = ?' => $user_id
    ));

    return $this->createRow(array(
      'resource_id' => $page_id,
      'topic_id' => $topic_id,
      'user_id' => $user_id,
      'watch' => $watch
    ))->save();

  }

  public function notifyAll($topic, $post, $viewer)
  {
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    $select = $this->select()
        ->where('resource_id = ?', $topic->page_id)
        ->where('topic_id = ?', $post->topic_id)
        ->where('watch = ?', 1);

    $watch_list = $this->fetchAll($select);

    $topic = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion')
        ->findRow($post->topic_id);

    $view = Zend_Registry::get('Zend_View');

    if (!$topic) {
      return ;
    }
    $topicOwner = $topic->getOwner();

    foreach ($watch_list as $watch) {

      $userRow = Engine_Api::_()->getDbTable('users', 'user')
          ->findRow($watch->user_id);

      if (!$userRow || $userRow->isSelf($viewer)) {
        continue;
      }

      if ($topicOwner->isSelf($userRow)) {
        $type = 'page_discussion_response';
      } else {
        $type = 'page_discussion_reply';
      }
      $href = $topic->getHref(array('child_id' => $post->getIdentity()));

      Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($userRow, $viewer, $topic, $type, array('message' => $view->BBCode($post->body), 'href' => $href));
    }
  }
}