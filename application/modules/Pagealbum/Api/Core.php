<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagealbum_Api_Core extends Page_Api_Core
{
	const IMAGE_WIDTH = 900;
  const IMAGE_HEIGHT = 900;

  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;

	public function getAlbumTable()
	{
		return Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
	}

    public function getUserAlbums($user)
    {
        $table = Engine_Api::_()->getItemTable('pagealbum');
        return $table->fetchAll($table->select()->where("user_id = ?", $user->user_id));
    }

  public function getPhotoTable()
  {
    return Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum');
  }

	public function getAlbums($pageObject)
	{
		$pageObject = $this->getPage($pageObject);
		$table = $this->getAlbumTable();
		$params = array('page_id' => $pageObject->getIdentity());

		return $table->getAlbums($params);
	}

  public function getInitJs($content_info, $subject = null)
  {
  	if (empty($content_info)){
  		return false;
  	}

  	$content = $content_info['content'];
  	$content_id = $content_info['content_id'];

    $res = "page_album.init_album();";
    if( $subject->isTimeline() ) {
      /**
       * @var $tbl Page_Model_DbTable_Content
       */
      $tbl = Engine_Api::_()->getDbTable('content', 'page');
      $id = $tbl->select()->from($tbl->info('name'), array('content_id'))
        ->where('page_id = ?', $subject->getIdentity())
        ->where("name = 'pagealbum.profile-album'")
        ->where('is_timeline = 1')
        ->query()
        ->fetch();
      $res = "tl_manager.fireTab('{$id['content_id']}');";
    }
  	if ($content == 'pagealbumphoto'){
  	  $photo = Engine_Api::_()->getItem('pagealbumphoto', $content_id);
  	  if (!$photo) {
  		  return false;
  	  }

  		return $res;
  	}elseif ($content == 'pagealbum'){
      $album = Engine_Api::_()->getItem('pagealbum', $content_id);

      if(!$album) {
          return $res;
      }
      return "page_album.view({$content_id}); " . $res;
    }elseif ($content == 'pagealbums'){// for SEO by Kirill
          return $res;
    }elseif($content == 'album_page') {
          return $res;
		}
    // for SEO by Kirill
    return false;
  }

  public function createPhoto($params, $file)
  {
    if( $file instanceof Storage_Model_File )
    {
      $params['file_id'] = $file->getIdentity();
    }
    else
    {
      // Get image info and resize
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');

      $mainName  = $path.'/m_'.$name . '.' . $extension;

      $thumbName = $path.'/t_'.$name . '.' . $extension;
			$thumbMiniName = $path.'/tm_'.$name . '.' . $extension;
      $thumbIconName = $path.'/ti_'.$name . '.' . $extension;

      $image = Engine_Image::factory();

      $image->open($file['tmp_name'])
          ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
          ->write($mainName)
          ->destroy();

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
          ->write($thumbName)
          ->destroy();

			$image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(34, 34)
          ->write($thumbMiniName)
          ->destroy();

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(48, 48)
          ->write($thumbIconName)
          ->destroy();

      // Store photos
      $photo_params = array(
        'parent_id' => $params['owner_id'],
        'parent_type' => 'user',
      );

      try {
        $photoFile = Engine_Api::_()->storage()->create($mainName,  $photo_params);
        $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
				$thumbMiniFile = Engine_Api::_()->storage()->create($thumbMiniName, $photo_params);
        $thumbIconFile = Engine_Api::_()->storage()->create($thumbIconName, $photo_params);
      } catch (Exception $e) {
        if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE)
        {
          echo $e->getMessage();
          exit();
        }
      }

      $photoFile->bridge($thumbFile, 'thumb.normal');
			$photoFile->bridge($thumbMiniFile, 'thumb.mini');
      $photoFile->bridge($thumbIconFile, 'thumb.icon');

      // Remove temp files
      @unlink($mainName);
      @unlink($thumbName);
			@unlink($thumbMiniName);

      $params['file_id']  = $photoFile->file_id; // This might be wrong
      $params['pagealbumphoto_id'] = $photoFile->file_id;
    }

    $row = $this->getPhotoTable()->createRow();
    $row->setFromArray($params);

    $row->save();

    return $row;
  }

	public function createAlbum(array $values)
	{
		return $this->getAlbumTable()->createAlbum($values);
	}

	public function getComments($page = null, $subject = null)
	{
		if ($subject == null){
		  $subject = Engine_Api::_()->core()->getSubject();
		}

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

  public function getAlbumPaginator($params = array())
  {
    $select = $this->getAlbumSelect($params);
    $paginator = Zend_Paginator::factory($select);

    if( !empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if( !empty($params['ipp'])) {
      $paginator->setItemCountPerPage($params['ipp']);
    }

    return $paginator;
  }

  public function getAlbumSelect($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !empty($params['sort']) && $params['sort'] == 'popular'){
      $params['sort'] = 'view_count';
    } else {
      $params['sort'] = 'creation_date';
    }

    $albumModule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('album');

    // get Tables
    $pagealbumTbl = Engine_Api::_()->getItemTable('pagealbum');
    $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
    $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');

    if( !empty($params['view']) && $params['view'] == 2) {
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

    if( $albumModule && $albumModule->enabled){
      $albumTbl = Engine_Api::_()->getItemTable('album');
      // select album
      $albumselect = $albumTbl->select()
        ->from($albumTbl->info('name'), array('album_id', 'modified_date', 'creation_date', 'view_count', new Zend_Db_Expr("'album' as type")))
        ->where('search = ?', '1');

      if( !empty($params['search']) ) {
        $albumselect->where('title LIKE ? OR description LIKE ?', '%'.$params['search'].'%');
      }

      if( !empty($params['view']) && $params['view'] == 2) {
        $albumselect->where('owner_id in (?)', new Zend_Db_Expr($str));
      } elseif(!empty($params['view']) && $params['view'] == 3) {
        $albumselect->where('owner_id = ?', $params['owner']->getIdentity());
      }

      if( !empty($params['category_id']) && $params['category_id'] != 0) {
        $albumselect->where('category_id = ?', $params['category_id']);
      }

      $unionselect = $albumselect;
    }

    if( empty($params['category_id'])  || $params['category_id'] == 0) {
      // Select pagealbum
      $pagealbumselect = $pagealbumTbl->select()
        ->from(array('pa' => $pagealbumTbl->info('name')), array('album_id' => 'pagealbum_id', 'modified_date', 'creation_date', 'view_count', new Zend_Db_Expr("'page' as type")))
        ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_type = 'page' AND a.resource_id = pa.page_id AND a.action = 'view'", array())
        ->joinLeft(array('li' => $listitemTbl->info('name')), 'a.role_id = li.list_id', array())
        ->where("(a.role = 'everyone' OR a.role = 'registered') OR li.child_id = ?", $viewer->getIdentity())
        ->group('pa.pagealbum_id');

      if( !empty($params['search']) ) {
        $pagealbumselect->where('title LIKE ? OR description LIKE ?', '%'.$params['search'].'%');
      }

      if( !empty($params['view']) && $params['view'] == 2 ) {
        $pagealbumselect->where('user_id in (?)', new Zend_Db_Expr($str));
      } elseif( !empty($params['view']) && $params['view'] == 3) {
        $pagealbumselect->where('user_id = ?',  $params['owner']->getIdentity());
      }
      $unionselect = $pagealbumselect;
    }

    if($albumModule && $albumModule->enabled != 0 && $pagealbumselect) {
      $unionselect = Engine_Db_Table::getDefaultAdapter()->select()->union(array($pagealbumselect, $albumselect));
    }

    if( $unionselect ) $unionselect->order($params['sort'].' DESC');

    return $unionselect;
  }

  public function isAllowedPost( $page ) {
    if( !$page )
      return false;
    $auth = Engine_Api::_()->authorization()->context;
    return $auth->isAllowed($page, Engine_Api::_()->user()->getViewer(), 'album_posting');
  }
}
