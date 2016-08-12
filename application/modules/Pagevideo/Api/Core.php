<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Api_Core extends Page_Api_Core
{
	public function getTable()
	{
		return Engine_Api::_()->getDbTable('pagevideos', 'pagevideo');
	}

	public function getVideos($pageObject)
	{
		$pageObject = $this->getPage($pageObject);
		$table = $this->getTable();
		$params = array('page_id' => $pageObject->getIdentity());

		return $table->getVideos($params);
	}

	public function getBaseUrl()
	{
	  return Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'home');
	}

  public function getInitJs($content_info, $subject = null)
  {
    if (empty($content_info)){
      return false;
    }

    $content = $content_info['content'];
    $content_id = $content_info['content_id'];
    $res = "page_video.init_video();";

    if( $subject->is_timeline ) {
      $tbl = Engine_Api::_()->getDbTable('content', 'page');
      $id = $tbl->select()->from($tbl->info('name'), array('content_id'))
        ->where('page_id = ?', $subject->getIdentity())
        ->where("name = 'pagevideo.profile-video'")
        ->where('is_timeline = 1')
        ->query()
        ->fetch();
      $res = "tl_manager.fireTab('{$id['content_id']}');";
    }
    if ($content == 'video') {
      $video = Engine_Api::_()->getItem('pagevideo', $content_id);
      if (!$video) {
        return false;
      }
      return "page_video.init_comments(); page_video.view_comments({$content_id}); " . $res;
    } elseif($content == 'pagevideos') {
      if($content_id == 1) {
        return $res;
      } else {
        return $res;
      }
    } elseif($content == 'video_page') {
      return $res;
    }

    return false;
  }

  public function createVideo($params, $file, $values)
  {
    if( $file instanceof Storage_Model_File ) {
      $params['file_id'] = $file->getIdentity();
    } else {
    // create video item
      $video = Engine_Api::_()->getDbtable('pagevideos', 'pagevideo')->createRow();
      $file_ext = pathinfo($file['name']);
      $file_ext = $file_ext['extension'];
      $video->code = $file_ext;
      $video->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $video->page_id = $values['page_id'];

        // Store video in temporary storage object for ffmpeg to handle
      $storage = Engine_Api::_()->getItemTable('storage_file');
      $storageObject = $storage->createFile($file, array(
        'parent_id' => $video->getIdentity(),
        'parent_type' => $video->getType(),
        'user_id' => $video->user_id,
      ));
      // Remove temporary file
      @unlink($file['tmp_name']);

      $video->file_id = $storageObject->file_id;
      $video->save();

// Store video in temp directory for ffmpeg to handle
//      $tmp_file = APPLICATION_PATH . '/temporary/pagevideo/'.$video->getIdentity().".".$file_ext;
//
//      $tmp_path = dirname($tmp_file);
//      if( !file_exists($tmp_path) ) {
//        mkdir($tmp_path, 0777, true);
//      }
//      $src_fh = fopen($file['tmp_name'], 'r');
//      $tmp_fh = fopen($tmp_file, 'w');
//      stream_copy_to_stream($src_fh, $tmp_fh);
//      chmod($tmp_file, 0777);

	  Engine_Api::_()->getDbtable('jobs', 'core')->addJob('pagevideo_encode', array(
        'pagevideo_id' => $video->getIdentity(),
      ));
    }

    return $video;

  }

  public function deleteVideo($video)
  {
    // check to make sure the video did not fail, if it did we wont have files to remove
    if ($video->status == 1){
      // delete storage files (video file and thumb)
      if ($video->type == 3) Engine_Api::_()->getItem('storage_file', $video->file_id)->remove();
      if ($video->photo_id) Engine_Api::_()->getItem('storage_file', $video->photo_id)->remove();
    }

    $attachmentTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $name = $attachmentTable->info('name');
    $select = $attachmentTable->select()
      ->setIntegrityCheck(false)
      ->from($name, array('action_id'))
      ->where('type = ?', "pagevideo")
      ->where('id = ?', $video->getIdentity());

    $action_id = (int)$attachmentTable->getAdapter()->fetchOne($select);
    $where = array('action_id = ?' => $action_id);

    $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $actionsTable->delete($where);

    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $streamTable->delete($where);

    $attachmentTable->delete($where);

    $where = array('resource_id = ?' => $action_id);

    $commentTable = Engine_Api::_()->getDbtable('comments', 'activity');
    $commentTable->delete($where);

    $likeTable = Engine_Api::_()->getDbtable('likes', 'activity');
    $likeTable->delete($where);

    // delete activity feed and its comments/likes
    Engine_Api::_()->getItem('pagevideo', $video->getIdentity())->delete();
  }

	public function getComments($page = null)
	{
		$subject = Engine_Api::_()->core()->getSubject();

    if( null !== $page)
    {
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id ASC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(10);
    }
    else
    {
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);
    }

    return $comments;
	}

  public function getVideoSelect($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !empty($params['view']) && $params['view'] == 2 ) {
      // Get an array of friend ids
      $table = Engine_Api::_()->getItemTable('user');
      $select = $viewer->membership()->getMembersSelect('user_id');
      $friends = $table->fetchAll($select);
      // Get stuff
      $ids = array();
      foreach( $friends as $friend )
      {
        $ids[] = $friend->user_id;
      }
      $str = "'".join("', '", $ids)."'";
    }

    // get Tables
    $pagevideoTbl = Engine_Api::_()->getItemTable('pagevideo');
    $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
    $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');

    //Check video plugin
    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('video');

    if( $module && $module->enabled ) {
      $videoTbl = Engine_Api::_()->getItemTable('video');

      // video select
      $videoselect = $videoTbl->select()
        ->from($videoTbl->info('name'), array('video_id', 'creation_date', 'view_count', 'rating', new Zend_Db_Expr("'video' as type")))
        ->where('search = 1');
      if( !empty($params['text']) ){
        $videoselect->where('title LIKE ? OR description LIKE ?', '%'.$params['text'].'%');
      }

      if(!empty($params['view']) && $params['view'] == 2) {
        $videoselect->where('owner_id in (?)', new Zend_Db_Expr($str));
      } elseif( !empty($params['view']) && $params['view'] == 3 ) {
        $videoselect->where('owner_id = ?', $params['owner']->getIdentity());
      }

      if( !empty($params['category']) && $params['category'] != 0 ) {
        $videoselect->where('category_id = ?', $params['category']);
      }

      $unionselect = $videoselect;
    }

    if( empty($params['category']) || $params['category'] == 0 ) {
      // Pagevideo select
      $pagevideoselect = $pagevideoTbl->select()
        ->from(array('pv' => $pagevideoTbl->info('name')), array('video_id' => 'pagevideo_id', 'creation_date', 'view_count', new Zend_Db_Expr('0 as rating'), new Zend_Db_Expr("'page' as type")))
        ->where('search = ?', 1)
        ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_type = 'page' AND a.resource_id = pv.page_id AND a.action = 'view'", array())
        ->joinLeft(array('li' => $listitemTbl->info('name')), 'a.role_id = li.list_id', array())
        ->where("(a.role = 'everyone' OR a.role = 'registered') OR li.child_id = ?", $viewer->getIdentity())
        ->group('pv.pagevideo_id');

      if( !empty($params['text']) ){
        $pagevideoselect->where('title LIKE ? OR description LIKE ?', '%'.$params['text'].'%');
      }

      if( !empty($params['view']) && $params['view'] == 2) {
        $pagevideoselect->where('user_id in (?)', new Zend_Db_Expr($str));
      }elseif( !empty($params['view']) && $params['view'] == 3 ) {
        $pagevideoselect->where('user_id = ?', $params['owner']->getIdentity());
      }

      $unionselect = $pagevideoselect;

    }

    if( $module && $module->enabled && $pagevideoselect ) {
      // Union
      $unionselect = Engine_Db_Table::getDefaultAdapter()->select()
        ->union(array($videoselect, $pagevideoselect));
    }

    // Order
    if( empty($params['orderby']) ) {
      $params['orderby'] = 'creation_date';
    }

    if( $unionselect ) $unionselect->order($params['orderby'].' DESC');

    return $unionselect;
  }

  public function getVideoPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getVideoSelect($params));

    if( !empty($params['page']) ) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if( !empty($params['ipp']) ) {
      $paginator->setItemCountPerPage($params['ipp']);
    }

    return $paginator;
  }

  public function isAllowedPost( $page ) {
    if( !$page )
      return false;
    $auth = Engine_Api::_()->authorization()->context;
    return $auth->isAllowed($page, Engine_Api::_()->user()->getViewer(), 'video_posting');
  }
}
