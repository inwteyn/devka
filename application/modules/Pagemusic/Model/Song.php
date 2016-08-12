<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Song.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagemusic_Model_Song extends Core_Model_Item_Abstract
{
  protected $_type = 'song';

  protected $_parent_type = 'playlist';

  protected $_owner_type = 'user';

  public function getShortType($inflect = false)
  {
    return 'song'; // helps core_model_item get to primary key "song_id" (it adds "_id")
  }

  public function getOwner($recurseType = null)
  {
    return $this->getPlaylist()->getOwner();
  }


public function getHref($params = array())
	{
		$id = $this->getPlaylist()->getIdentity();
		$params = array_merge(array(
      'route' => 'page_view',
      'page_id' => $this->getParentPage()->url,
    	'content' => 'playlist',
    	'content_id' => $id
    ), $params);

    $route = @$params['route'];
    unset($params['route']);
		
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
	}

	public function getParentPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

	public function getRichContent($view = false, $params = array())
	{
		$api = Engine_Api::_()->pagemusic();
		$storage = Engine_Api::_()->storage();
		
		$embedded = '
			<div class="page-music-activity">
			<div class="page-music-info">
			<a href="'.$this->getHref().'">'.$this->getTitle().'</a>
			</div>
			<div class="page-music-embed">
			<object height="24" width="290" type="application/x-shockwave-flash" name="activity_song_'.$this->getIdentity().'"
				style="outline-color: -moz-use-text-color; outline-style: none; outline-width: medium;"
				data="'.$api->getBaseUrl().'/application/modules/Pagemusic/externals/standalone/player.swf" id="activity_song_'.$this->getIdentity().'">
				<param name="bgcolor" value="#FFFFFF">
				<param name="wmode" value="transparent">
				<param name="menu" value="false">
				<param name="flashvars" value="initialvolume=100&left=000000&lefticon=FFFFFF&soundFile='.$storage->get($this->file_id)->map().'&titles='.$this->getTitle().'&playerID=activity_song_'.$this->getIdentity().'">
			</object>
			</div>
			</div>';

		return $embedded;
	}

	public function getPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

	// ID3 reader
  public function readID3($mp3_filename)
  {
    // get file's ID3 tags
    set_include_path(
      get_include_path() . PS .
      APPLICATION_PATH . DS . 'application' . DS . 'libraries' . DS . 'php-reader' . PS .
      APPLICATION_PATH . DS . 'application' . DS . 'libraries' . DS . 'php-reader' . DS . 'ID3');

    require_once('libraries/php-reader/ID3v1.php');
    require_once('libraries/php-reader/ID3v2.php');
    $song_id3 = array();
    if (is_numeric($mp3_filename)) {
      $file = Engine_Api::_()->getItem('storage_file', $mp3_filename);
      if ($file)
        $mp3_filename = $file->storage_path;
      else
        return;
    }

    try {
      $id3 = new ID3v2($mp3_filename);
      if (!$id3)
        $song_id3 = array(
          'id3_v'   => 2,
          'title'   => $id3->TIT2->getText(),
          'artist'  => $id3->TPE1->getText(),
          /*
          'album'   => $id3->album,
          'date'    => $id3->year,
          'track'   => $id3->track,
          'comment' => $id3->comment,
          */
        );
      else {
        $id3 = new ID3v1($mp3_filename);
        if ($id3)
          $song_id3   = array(
            'id3_v'   => 1,
            'title'   => $id3->title,
            'artist'  => $id3->artist,
            'album'   => $id3->album,
            'date'    => $id3->year,
            'track'   => $id3->track,
            'comment' => $id3->comment,
          );
      }
      echo "<pre>";print_r($song_id3);
      return $song_id3;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function deleteUnused()
  {
    $file   = Engine_Api::_()->getItem('storage_file', $this->file_id);
    if ($file) {
      $table = Engine_Api::_()->getDbtable('songs', 'pagemusic');
      $count = $table->select()
                      ->from($table->info('name'), 'count(*) as count')
                      ->where('file_id = ?', $file->getIdentity())
                      ->query()
                      ->fetchColumn(0);
      if ($count <= 1)
        $file->remove();
    }

		$search_api = Engine_Api::_()->getDbTable('search', 'page');
		$search_api->deleteData($this);

		$this->removePlays();
    $this->delete();
  }

	public function removePlays()
	{
		$table = Engine_Api::_()->getDbTable('plays', 'pagemusic');
		$name = $table->info('name');
		$playlist = $this->getPlaylist();
		
		$select = $table->select()
			->setIntegrityCheck(false)
			->from($name, array('COUNT(*)'))
			->where('song_id = ?', $this->getIdentity());
		
		$count = $table->getAdapter()->fetchOne($select);
		if ($count){
			$playlist->play_count -=  $count;
		}

		$table->delete("song_id = {$this->getIdentity()}");

		$select = $table->select()
			->setIntegrityCheck(false)
			->from($name, array('COUNT(DISTINCT user_id)'))
			->where('playlist_id = ?', $playlist->getIdentity());

		$count = $table->getAdapter()->fetchOne($select);
		$playlist->listener_count = $count;

		$playlist->save();
	}

  public function getPlaylist()
  {
    if(!$this->playlist_id){
      return false;
    }

    return Engine_Api::_()->getItem('playlist', $this->playlist_id);
  }

  public function getFilePath()
  {
    $file = Engine_Api::_()->getItem('storage_file', $this->file_id);
    if( $file ) {
      return $file->map();
    }
  }

  public function playCountLanguagified()
  {
    return vsprintf(Zend_Registry::get('Zend_Translate')->_(array('%s play', '%s play', $this->play_count)),
      Zend_Locale_Format::toNumber($this->play_count)
    );
  }
}