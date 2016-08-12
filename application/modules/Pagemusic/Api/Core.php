<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagemusic_Api_Core extends Page_Api_Core
{
  public function getInitJs($content_info, $subject = null)
  {
    if (empty($content_info)){
      return false;
    }

    $content = $content_info['content'];
    $content_id = $content_info['content_id'];
    $res = "page_music.init_music();";

    if( $subject->is_timeline ) {
      $tbl = Engine_Api::_()->getDbTable('content', 'page');
      $id = $tbl->select()->from($tbl->info('name'), array('content_id'))
        ->where('page_id = ?', $subject->getIdentity())
        ->where("name = 'pagemusic.profile-music'")
        ->where('is_timeline = 1')
        ->query()
        ->fetch();
      $res = "tl_manager.fireTab('{$id['content_id']}');";
    }
    if ($content == 'playlist'){
      $playlist = Engine_Api::_()->getItem('playlist', $content_id);

      if (!$playlist){
        return $res;
      }

      return $res.'page_music.view('.$content_id.');';
//  		return "page_music.playlist_id = {$content_id}; page_music.init_music(); page_music.view({$content_id});";
    }else if($content == 'pagemusic'){
      if($content_id == 1)
        return $res;
      return $res;
    }elseif($content == 'music_page'){
      return $res;
    }

    return false;
  }

  public function createSong($file, $params=array())
  {
    // upload to storage system
    $song_path = pathinfo($file['name']);
    $params    = array_merge(array(
      'type'        => 'pagemusicsong',
      'name'        => $file['name'],
      'parent_type' => 'pagemusicsong',
      'parent_id'   => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'user_id'     => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'extension'   => substr($file['name'], strrpos($file['name'], '.')+1),
    ), $params);

    $song = Engine_Api::_()->storage()->create($file, $params);

    return $song;
  }

  public function isListenedSong(Core_Model_Item_Abstract $song, $viewer = null)
  {
    if (!$song){
      return null;
    }

    if (!$viewer){
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    $table = Engine_Api::_()->getDbTable('plays', 'pagemusic');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('count' => 'COUNT(*)'))
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('song_id = ?', $song->getIdentity());

    return (bool)($table->getAdapter()->fetchOne($select));
  }

  public function isListenedPlaylist(Core_Model_Item_Abstract $playlist, $viewer = null)
  {
    if (!$playlist){
      return null;
    }

    if (!$viewer){
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    $table = Engine_Api::_()->getDbTable('plays', 'pagemusic');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('count' => 'COUNT(*)'))
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('playlist_id = ?', $playlist->getIdentity());

    return (bool)($table->getAdapter()->fetchOne($select));
  }

  public function uploadPhoto($photo, $params = array())
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';


    $params    = array_merge(array(
      'name'        => $name,
      'parent_type' => 'pagemusicart',
      'parent_id'   => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'user_id'     => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'extension'   => $extension,
    ), $params);

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(174, 174)
      ->write($path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(126, 126)
      ->write($path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(48, 48)
      ->write($path . DIRECTORY_SEPARATOR . $base . '_ti.' . $extension)
      ->destroy();

    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(34, 34)
      ->write($path . DIRECTORY_SEPARATOR . $base . '_tm.' . $extension)
      ->destroy();

    // Store
    $iMain       = $storage->create($path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension,  $params);
    $iProfile    = $storage->create($path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension,  $params);
    $iMini       = $storage->create($path . DIRECTORY_SEPARATOR . $base . '_ti.' . $extension,  $params);
    $iIcon       = $storage->create($path . DIRECTORY_SEPARATOR . $base . '_tm.' . $extension,  $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iMini, 'thumb.mini');
    $iMain->bridge($iIcon, 'thumb.icon');

    // Remove temp files
    @unlink($path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension);
    @unlink($path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension);
    @unlink($path . DIRECTORY_SEPARATOR . $base . '_ti.' . $extension);
    @unlink($path . DIRECTORY_SEPARATOR . $base . '_tm.' . $extension);
    @unlink($file);

    return $iMain;
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

  public function getBaseUrl()
  {
    return Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'home', true);
  }

  public function getMusicPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getMusicSelect($params));

    if( !empty($params['page']) ) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if( !empty($params['ipp']) ) {
      $paginator->setItemCountPerPage($params['ipp']);
    }

    return $paginator;
  }

  public function getMusicSelect($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !empty($params['show']) && $params['show'] == 2) {
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

    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('music');

    // Get Tables
    $pagemusicTbl = Engine_Api::_()->getItemTable('playlist');
    $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
    $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');
    $viewer = Engine_Api::_()->user()->getViewer();

    if( $module && $module->enabled ) {
      $musicTbl = Engine_Api::_()->getItemTable('music_playlist');

      //Music select
      $musicselect = $musicTbl->select()
        ->from($musicTbl->info('name'), array('playlist_id', 'creation_date', 'play_count', new Zend_Db_Expr("'music' as type")))
        ->where('search = ?', '1');

      if( !empty($params['show']) && $params['show'] == 2) {
        $musicselect->where('owner_id in (?)', new Zend_Db_Expr($str));
      } elseif( !empty($params['show']) && $params['show']== 3 ) {
        $musicselect->where('owner_id = ?', $params['owner']->getIdentity());
      }

      if( !empty($params['search']) ) {
        $musicselect->where("title LIKE ? OR description LIKE ?", '%'.$params['search'].'%');
      }

    }

    // Pagemusic select
    $pagemusicselect = $pagemusicTbl->select()
      ->from(array('pm' => $pagemusicTbl->info('name')), array('playlist_id', 'creation_date', 'play_count', new Zend_Db_Expr("'page' as type")))
      ->where("search = 1")
      ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_type = 'page' AND a.resource_id = pm.page_id AND a.action = 'view'", array())
      ->joinLeft(array('li' => $listitemTbl->info('name')), "a.role_id = li.list_id", array())
      ->where("a.role = 'everyone' OR a.role = 'registered' OR li.child_id = ?", $viewer->getIdentity())
      ->group('pm.playlist_id');

    if( !empty($params['show']) && $params['show'] == 2) {
      $pagemusicselect->where('owner_id IN (?)', new Zend_Db_Expr($str));
    } elseif( !empty($params['show']) && $params['show'] == 3 ) {
      $pagemusicselect->where('owner_id = ?', $params['owner']->getIdentity());
    }

    if( !empty($params['search']) ) {
      $pagemusicselect->where("title LIKE ? OR description LIKE ?", '%'.$params['search'].'%');
    }

    $unionselect = $pagemusicselect;

    if( $module && $module->enabled) {
      $unionselect = Engine_Db_Table::getDefaultAdapter()->select()->union(array($musicselect, $pagemusicselect));
    }

    if(!empty($params['sort']) && $params['sort'] == 'popular') {
      $params['sort'] = 'play_count';
    } else {
      $params['sort'] = 'creation_date';
    }


    // Order
    if( $unionselect ) $unionselect->order($params['sort'].' DESC');

    return $unionselect;
  }

  public function isAllowedPost( $page ) {
    if( !$page )
      return false;
    $auth = Engine_Api::_()->authorization()->context;
    return $auth->isAllowed($page, Engine_Api::_()->user()->getViewer(), 'music_posting');
  }

}
