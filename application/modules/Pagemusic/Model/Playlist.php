<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Playlist.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagemusic_Model_Playlist extends Core_Model_Item_Abstract
{
  protected $_type = 'playlist';

  protected $_parent_type = 'page';
  
  protected $_owner_type = 'user';
  
  public function getShortType($inflect = false)
  {
    return 'playlist';
  }
  
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',
      'page_id' => $this->getParentPage()->url,
    	'content' => 'playlist',
    	'content_id' => $this->getIdentity(),
    ), $params);

    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }
  
	public function getDescription()
	{
		return $this->description;
	}

	public function getLink()
	{
		return sprintf("<a href='%s'>%s</a>", $this->getHref(), $this->getTitle());
	}

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function getCommentCount()
  {
    return $this->comments()->getCommentCount();
  }

  public function getAuthorizationItem()
  {
    return $this->getPage();
  }

	public function getPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

	public function getParentPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

  public function getSongs($file_id = null)
  {
    $table  = Engine_Api::_()->getDbtable('songs', 'pagemusic');
		
    $select = $table->select()
                    ->where('playlist_id = ?', $this->getIdentity())
                    ->order('order ASC');
    
    if (!empty($file_id)){
      $select->where('file_id = ?', $file_id);
    }

    return $table->fetchAll($select);
  }

  public function getPhoto()
  {
    if (!$this->photo_id){
      return false;
    }

    $storage = Engine_Api::_()->storage();
    return $storage->get($this->photo_id);
  }

  public function getSong($file_id)
  {
    $songs = $this->getSongs($file_id);
    return $songs[0];
  }

  public function addSong($file_id)
  {
    if ($file_id instanceof Storage_Model_File)
      $file = $file_id;
    else
      $file = Engine_Api::_()->getItem('storage_file', $file_id);
    
    if ($file){
      $playlist_song = Engine_Api::_()->getDbtable('songs', 'pagemusic')->createRow();
      $playlist_song->playlist_id = $this->getIdentity();
      $playlist_song->file_id     = $file->getIdentity();
      $playlist_song->title       = preg_replace('/\.(mp3|m4a|aac|mp4)$/i', '', $file->name);
      $playlist_song->order       = count($this->getSongs());
      $playlist_song->page_id     = $this->page_id;
      $playlist_song->save();

      $file->parent_type = 'pagemusicsong';
      $file->parent_id = $playlist_song->getIdentity();
    }
    
    return $playlist_song;
  }

  public function removePhotos()
  {
    $storage = Engine_Api::_()->storage();
    $file_id = $this->photo_id;

    $file = $storage->get($file_id);
    if ($file){
      $file->delete();
    }

    $file = $storage->get($file_id, 'thumb.profile');
    if ($file){
      $file->delete();
    }

		$file = $storage->get($file_id, 'thumb.mini');
    if ($file){
      $file->delete();
    }

    $file = $storage->get($file_id, 'thumb.icon');
    if ($file) {
      $file->delete();
    }
  }

  public function removeSongs()
  {
    $songs = $this->getSongs();
    if ($songs){
      foreach ($songs as $song){
        $song->deleteUnused();
      }
    }
  }

	public function removeTags()
	{
		$tagTable = Engine_Api::_()->getDbTable('tagMaps', 'page');
		$tagTable->delete(array('resource_id = ?' => $this->getIdentity(), 'resource_type = ?' => $this->getType()));
	}

	public function removeActivity()
	{	
	}
    
  public function setPhoto($photo)
  {
    $params = array(
      'parent_type' => 'pagemusic',
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->owner_id
    );

    $iMain = Engine_Api::_()->pagemusic()->uploadPhoto($photo, $params);
    
    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id      = $iMain->getIdentity();
    $this->save();

    return $this;
  }
	
  public function _delete()
  {
    $this->removePhotos();
    $this->removeSongs();
		$this->removeTags();
		$this->removeActivity();

		$search_api = Engine_Api::_()->getDbTable('search', 'page');
		$search_api->deleteData($this);

    parent::_delete();
  }

  public function tags()
  {
  	return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'page'));
  }

  public function isViewable()
  {
    return $this->authorization()->isAllowed(null, 'view');
  }

  public function isEditable()
  {
    return $this->authorization()->isAllowed(null, 'edit');
  }

  public function isDeletable()
  {
    return $this->authorization()->isAllowed(null, 'delete');
  }
  
  public function getMediaType()
  {
    return $this->getType();
  }
}