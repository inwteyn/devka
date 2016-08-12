<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagetopic.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Model_Pagetopic extends Core_Model_Item_Abstract
{
	protected $_shortType = 'topic';
  protected $_parent_type = 'page';
  protected $_owner_type = 'user';
  protected $_type = 'pagediscussion_pagetopic';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',
      'page_id' => $this->getParentPage()->url,
    	'tab' => 'discussion',
    	'content_id' => $this->getIdentity(),
    ), $params);

    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getLink($params = array())
  {
    return sprintf("<a href='%s'>%s</a>", $this->getHref($params), $this->getTitle());
  }

  public function getPostPaginator($page, $post_id = null)
  {
    // Settings
    $perPage = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('pagediscussion.perpage.post', 10);

    $tbl = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');

    $select = $tbl->select()
      ->where('topic_id = ?', $this->getIdentity())
      ->order('creation_date ASC');

    if ($post_id) {
      $subSelect = $tbl->select()
          ->from($tbl->info('name'), array('number' => new Zend_Db_Expr('COUNT(*)')))
          ->where('topic_id = ?', $this->getIdentity())
          ->where('post_id < ?', $post_id)
          ->order('creation_date ASC');
      $orderPost = (int)$tbl->getAdapter()->fetchOne($subSelect) + 1;
      $page = ceil($orderPost/$perPage);
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setDefaultItemCountPerPage($perPage);
    $paginator->setCurrentPageNumber($page);

    return $paginator;

  }

  public function isWatching($user_id)
  {
    if (!$user_id) {
      return false;
    }

    return (bool) Engine_Api::_()->getDbTable('pagetopicwatches', 'pagediscussion')
        ->select()
        ->where('resource_id = ?', $this->page_id)
        ->where('topic_id = ?', $this->getIdentity())
        ->where('user_id = ?', $user_id)
        ->where('watch = 1')
        ->query()
        ->rowCount();
  }

  public function getParentPage()
  {
    return Engine_Api::_()->getDbTable('pages', 'page')->findRow($this->page_id);
  }

  public function getDescription()
  {
    $firstPost = $this->getFirstPost();
    return ( null !== $firstPost ? $firstPost->getDescription() : '' );
  }

  public function getChildIds()
  {
    $table = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');
    $tbl = $table->getAdapter();

    $tbl->setFetchMode(Zend_Db::FETCH_NUM);

    $select = $tbl->select()
        ->from($table->info('name'), new Zend_Db_Expr('post_id'))
        ->where('topic_id = ?', $this->getIdentity());

    return $tbl->fetchCol($select);

  }

  public function getFirstPost()
  {
    $table = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');
    $select = $table->select()
      ->where('topic_id = ?', $this->getIdentity())
      ->order('post_id ASC')
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getLastPost()
  {
    $table = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');
    $select = $table->select()
      ->where('topic_id = ?', $this->getIdentity())
      ->order('post_id DESC')
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getLastPoster()
  {
    return Engine_Api::_()->getItem('user', $this->lastposter_id);
  }

  public function getCountPost()
  {
    return $this->post_count-1;
  }

  protected function _delete()
  {
    $table = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');
    $select = $table->select()
        ->where('topic_id = ?', $this->getIdentity());

    $posts = $table->fetchAll($select);

    $searchApi = Engine_Api::_()->getApi('search', 'core');
    $pageApi = Engine_Api::_()->getDbTable('search', 'page');
    $tbl = Engine_Api::_()->getDbTable('attachments', 'activity');

    foreach ($posts as $post)
    {
      // Delete Actions
      $action_ids = $tbl->select()
          ->from($tbl->info('name'), new Zend_Db_Expr('action_id'))
          ->where('type = ?', $post->getType())
          ->where('id = ?', $post->getIdentity())
          ->query()
          ->fetchAll(Zend_Db::FETCH_COLUMN);

      $tbl->delete(array(
        'type = ?' => $post->getType(),
        'id = ?' => $post->getIdentity()
      ));

      if ($action_ids){
        Engine_Api::_()->getDbTable('actions', 'activity')->delete(array(
          'action_id IN (?)' => $action_ids
        ));
      }

      // Call Hooks
      Engine_Hooks_Dispatcher::getInstance()->callEvent('onItemDeleteBefore', $post);

      // Delete Global Search
      $searchApi->unindex($post);

      // Delete Page Search
      $pageApi->deleteData(array(
        'object' => $post->getType(),
        'object_id' => $post->getIdentity(),
        'page_id' => $post->page_id
      ));

      $post->disableHooks()->delete();
    }

    // Delete Watches
    $tbl_watch = Engine_Api::_()->getDbTable('pagetopicwatches', 'pagediscussion');

    $tbl_watch->delete(array(
      'resource_id = ?' => $this->page_id,
      'topic_id = ?' => $this->getIdentity()
    ));

    // Delete Page Search
    $pageApi->deleteData(array(
      'object' => $this->getType(),
      'object_id' => $this->getIdentity(),
      'page_id' => $this->page_id
    ));

    parent::_delete();
  }

}