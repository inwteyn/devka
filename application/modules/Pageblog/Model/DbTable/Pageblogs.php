<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageBlogs.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageblog_Model_DbTable_Pageblogs extends Engine_Db_Table
{
	protected $_name = 'page_blogs';

	protected $_rowClass = 'Pageblog_Model_Pageblog';

	public function getBlogs($params = array())
	{
		if (!empty($params['count']) && $params['count']){
			return $this->getAdapter()->fetchOne($this->getSelect($params));
		}

		return $this->getPaginator($params);
	}

	public function getBlog($params = array())
	{
		$select = $this->getSelect($params);
		return $this->fetchRow($select);
	}

	public function getSelect($params = array())
	{
		$select = $this->select();

		$select
			->setIntegrityCheck(false);

    $prefix = $this->getTablePrefix();

		if (!empty($params['count']) && $params['count']){
			$select
				->from($prefix.'page_blogs', array('count' => 'COUNT(*)'))
				->group($prefix.'page_blogs.page_id');
		}else{
			$select
				->from($prefix.'page_blogs');
		}

		$select
			->joinLeft($prefix.'users', $prefix.'users.user_id = '.$prefix.'page_blogs.user_id', array());

		if (!empty($params['page_id'])) {
			$select
				->where($prefix."page_blogs.page_id = {$params['page_id']}");
		}

		if (!empty($params['user_id'])) {
			$select
				->where($prefix."page_blogs.user_id = {$params['user_id']}");
		}

		if (!empty($params['blog_id'])) {
			$select
				->where($prefix."page_blogs.pageblog_id = {$params['blog_id']}");
		}

    $select->order($prefix."page_blogs.pageblog_id DESC");

		return $select;
	}

	public function getPaginator($params = array())
	{
		$select = $this->getSelect($params);
		$paginator = Zend_Paginator::factory($select);
		if (!empty($params['ipp'])) {
			$paginator->setItemCountPerPage($params['ipp']);
		}

		if (!empty($params['p'])) {
			$paginator->setCurrentPageNumber($params['p']);
		}
		return $paginator;
	}

	public function postBlog(array $values)
	{
		if (empty($values)) {
			return false;
		}

    $user = Engine_Api::_()->user()->getViewer();
    $title = $values['title'];
    $body = $values['body'];
    $page_id = $values['page_id'];
    $photo_id = $values['photo_id'];

    $tags = preg_split('/[,]+/', $values['tags']);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $page = Engine_Api::_()->getItem('page', $page_id);

    try{
    	$row = null;

    	if (!empty($values['blog_id']) && $values['blog_id']){
    		$row = $this->getBlog(array('blog_id' => $values['blog_id']));
    	}

    	if (!$row){
    		$row = $this->createRow();
    		$row->user_id = $user->getIdentity();
    	}

      $row->page_id = $page_id;
      $row->creation_date = date('Y-m-d H:i:s');
      $row->modified_date = date('Y-m-d H:i:s');
      $row->title = $title;
      $row->body = $body;
      $row->photo_id = $photo_id;
      $row->save();

			$search_api = Engine_Api::_()->getDbTable('search', 'page');
			$search_api->saveData($row);

      Engine_Api::_()->page()->sendNotification($row, 'post_pageblog');

      $row->pageblog_id;

      if ($tags) {
        $row->tags()->setTagMaps($user, $tags);
      }

    	if (empty($values['blog_id'])) {
    		$this->addActivity($row, $page, $user, 'pageblog_new');
    	}

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $row;
	}

	public function addActivity(Core_Model_Item_Abstract $blog, Core_Model_Item_Abstract $page, Core_Model_Item_Abstract $user, $type)
	{
		$api = Engine_Api::_()->getDbtable('actions', 'activity');
		$link = $blog->getLink();

    $action = $api->addActivity($user, $page, $type, null, array('body' => Engine_String::strip_tags($blog->body), 'link' => $link));

    if( $action ) {
      $api->attachActivity($action, $blog, Activity_Model_Action::ATTACH_DESCRIPTION);
    }
	}

}
