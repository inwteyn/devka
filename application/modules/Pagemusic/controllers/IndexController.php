<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagemusic_IndexController extends Core_Controller_Action_Standard
{
  protected $_params;

  public function init()
  {
		if( $this->getRequest()->getQuery('ul', false) ) {
      return $this->_forward('upload-song', null, null, array('format' => 'json'));
    }

    if( $this->getRequest()->getQuery('ua', false) ) {
      return $this->_forward('upload-art', null, null, array('format' => 'json'));
    }
		
		$this->view->page_id = $page_id = $this->_getParam('page_id');
		$this->view->p = $p = $this->_getParam('p');
    $ipp = $this->_getParam('ipp', 10);    
		$this->_params = array('page_id' => $page_id, 'p' => $p, 'ipp' => $ipp);

		if ($page_id){
			$this->view->pageObject = $subject = Engine_Api::_()->getItem('page', $page_id);
			$this->view->isAllowedView = $this->getPageApi()->isAllowedView($subject);

			if (!$this->view->isAllowedView){
				$this->view->isAllowedPost = false;
				$this->view->isAllowedComment = false;
				return ;
			}

			$this->view->isAllowedPost = $this->getApi()->isAllowedPost($subject);
			$this->view->isAllowedComment = $this->getPageApi()->isAllowedComment($subject);
		}

		$this->view->playlist_id = $playlist_id = $this->_getParam('playlist_id');
		$this->view->song_id = $song_id = $this->_getParam('song_id');
		if ($playlist_id || $song_id){
			if( !Engine_Api::_()->core()->hasSubject() ){
				if ($playlist_id !== null){
					$subject = Engine_Api::_()->getItem('playlist', $playlist_id);
					if (!$subject){
						$song = Engine_Api::_()->getItem('song', $song_id);
						$subject = $song->getPlaylist();
					}
					if( $subject && $subject->getIdentity() ){
						Engine_Api::_()->core()->setSubject($subject);
					}
				}
			}
		}
  }

	protected function getApi()
  {
  	return Engine_Api::_()->getApi('core', 'pagemusic');
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }

  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('playlists', 'pagemusic');
    $this->view->playlists = $table->getPaginator($this->_params);
    $this->view->html = $this->view->render('index.tpl');
  }

  public function manageAction()
  {
    $table = Engine_Api::_()->getDbTable('playlists', 'pagemusic');
    $this->_params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->playlists = $table->getPaginator($this->_params);
    $this->view->html = $this->view->render('manage.tpl');
  }

  public function deleteAction()
  {
    $this->view->eval = 'page_music.manage();';
    
    $playlist_id = $this->_getParam('playlist_id');
    $db = Engine_Api::_()->getDbTable('playlists', 'pagemusic')->getAdapter();
    $db->beginTransaction();
    
    try {
      $playlist = Engine_Api::_()->getItem('playlist', $playlist_id);
      $playlist->delete();
      $db->commit();
    }
    catch(Exception $e){
      $this->view->message = 'pagemusic_Playlist was not deleted.';
      $this->view->html = $this->view->render('error.tpl');
      throw $e;
    }

		$this->view->eval .= " page_music.inc_count(-1);";
    $this->view->message = 'pagemusic_Playlist was successfully deleted.';
    $this->view->html = $this->view->render('success.tpl');    
  }

  public function orderAction()
  {
    $order = $this->_getParam('order');
    $ids = array_map(array($this, 'getIdsFromString'), explode(',', $order));

    if (empty($ids)){
      return ;
    }

    foreach ($ids as $key => $id){
      $order = $key+1;
      $item = Engine_Api::_()->getItem('song', $id);
      $item->order = $order;
      $item->save();
    }
  }

  public function renameAction()
  {
    $song_id = $this->_getParam('song_id');
    $title = $this->_getParam('title');

    if (!$song_id || !trim($title)){
      return ;
    }
    
    $song = Engine_Api::_()->getItem('song', $song_id);
    $song->title = $title;

    $song->save();

		$search_api = Engine_Api::_()->getDbTable('search', 'page');
		$search_api->saveData($song);
  }

  protected function getIdsFromString($string)
  {
    return (int)substr(trim($string), 14);
  }

  public function saveAction()
  {
    $this->view->eval = 'page_music.manage();';
    
    $form = new Pagemusic_Form_Music();  
    $form->populate($this->_getAllParams());

    // Process
    $db = Engine_Api::_()->getDbTable('playlists', 'pagemusic')->getAdapter();
    $db->beginTransaction();
    try {
      $form->saveValues();
      $db->commit();
    } catch( Exception $e ) {
      $this->view->message = 'pagemusic_Playlist was not created.';
      $this->view->html = $this->view->render('error.tpl');
      
      $db->rollback();
      throw $e;
    }

		if (!$this->_getParam('playlist_id')){
			$this->view->eval .= " page_music.inc_count(1);";
		}
    
    $this->view->message = 'pagemusic_Playlist was successfully created.';
    $this->view->html = $this->view->render('success.tpl');
  }
  
  public function editAction()
  {
    $playlist_id = $this->_getParam('playlist_id');

    $playlist = Engine_Api::_()->getItem('playlist', $playlist_id);
    $this->view->songs = $playlist->getSongs();
    $this->view->playlist = $playlist->toArray();
    $this->view->photo = $playlist->getPhoto();

    $tags = $playlist->tags()->getTagMaps();
    $tagString = '';
    foreach( $tags as $tagmap ){
      if( $tagString !== '' ) $tagString .= ', ';
      $tag = $tagmap->getTag();
			if ($tag){
				$tagString .= $tag->getTitle();
			}
    }
    $this->view->playlist['tags'] = $tagString;

    $this->view->songs_html = count($this->view->songs) > 0 ? $this->view->render('edit_songs.tpl') : false;
    $this->view->photo_html = $playlist->photo_id ? $this->view->render('edit_art.tpl') : false;
  }

  public function viewAction()
  {
    $playlist_id = $this->_getParam('playlist_id');
		$song_id = $this->_getParam('song_id');
		$page_id = $this->_getParam('page_id');

		$this->view->storage = Engine_Api::_()->storage();
    $this->view->playlist = $playlist = Engine_Api::_()->getItem('playlist', $playlist_id);
		
		if (!$playlist && $song_id){
			$song = Engine_Api::_()->getItem('song', $song_id);
			$this->view->song_id = $song_id = $song->getIdentity();
			$this->view->playlist = $playlist = $song->getPlaylist();
		}

		if (!$playlist){
			$this->view->eval = "page_music.index();";
			$this->view->message = "pagemusic_This playlist were deleted.";
			$this->view->html = $this->view->render("error.tpl");
			return ;
		}

		if (!Engine_Api::_()->core()->hasSubject()){
			Engine_Api::_()->core()->setSubject($playlist);
		}

		$this->view->subject = $subject = $playlist;
		$this->view->pageObject = Engine_Api::_()->getItem('page', $page_id);
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->songs = $playlist->getSongs();
		
		$this->view->musicTags = $playlist->tags()->getTagMaps();

		$path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

		$this->view->comment_form_id = "music-comment-form";

		$this->view->likes = $playlist->likes()->getLikePaginator();

//		$page = $this->_getParam('page');
//    $this->view->comments = $this->getApi()->getComments($page, $playlist);

		$this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
    $this->view->likes = $likes = $subject->likes()->getLikePaginator();
		$this->view->page = $page = $this->_getParam('page');
		$this->view->comments = $this->getApi()->getComments($page);

		if($this->view->isAllowedComment){
      $this->view->form = $form = new Core_Form_Comment_Create();
      $form->addElement('Hidden', 'form_id', array('value' => 'music-comment-form'));
      $form->populate(array(
        'identity' => $playlist->getIdentity(),
        'type' => $playlist->getType(),
      ));
    }

		// $this->view->subject = Engine_Api::_()->core()->getSubject();
		$this->view->likeHtml = $this->view->render('comment/list.tpl');
		$this->view->likeUrl = $this->view->url(array('action' => 'like'), 'like_comment');
		$this->view->unlikeUrl = $this->view->url(array('action' => 'unlike'), 'like_comment');
		$this->view->hintUrl = $this->view->url(array('action' => 'hint'), 'like_comment');
		$this->view->showLikesUrl = $this->view->url(array('action' => 'list'), 'like_comment');
		$this->view->postCommentUrl = $this->view->url(array('action' => 'create'), 'like_comment');
		
		if($this->_getParam('format') == 'json')
            $this->view->html = $this->view->render('index/view.tpl');
  }

  public function uploadSongAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    
    // only members can upload music
    if( !$this->_helper->requireUser()->checkRequire() ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_Max file size limit exceeded or session expired.');
      return;
    }

    if( !$this->getRequest()->isPost() ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_Invalid Upload or file too large');
      return;
    }

    if( !preg_match('/\.(mp3|m4a|aac|mp4)$/', $_FILES['Filedata']['tmp_name']) ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_Invalid file type');
    }
    
    $db = Engine_Api::_()->getDbtable('playlists', 'pagemusic')->getAdapter();
    $db->beginTransaction();
    try {
      $song = $this->view->song = Engine_Api::_()->getApi('core', 'pagemusic')->createSong($_FILES['Filedata']);
      $this->view->status   = true;
      $this->view->song_id  = $song->getIdentity();
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      $this->view->status  = false;
      $this->view->message = $translate->_('pagemusic_Upload failed by database query');
      throw $e;
    }

  }

  public function removeSongAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
		
    if (!$this->getRequest()->isPost()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('isGet');
      exit();
    }

		$song_id = $this->getRequest()->getParam('song_id');
		if (!$song_id){
			$this->view->success = false;
      $this->view->error   = $translate->_('pagemusic_There is no neccessary parameters.');
      exit();
		}
		
    $song = Engine_Api::_()->getItem('song', $song_id);
    if ($song){
      $playlist = $song->getPlaylist();
    }else{
      $playlist = false;
    }

    $file = Engine_Api::_()->getItem('storage_file', $this->getRequest()->getParam('song_id'));

    $db = Engine_Api::_()->getDbTable('playlists', 'pagemusic')->getAdapter();
    $db->beginTransaction();
		
    try {
      if ($playlist){
        $playlist->track_count--;
        $playlist->save();
      }

      if ($song){
        $song->deleteUnused();
      }else{
        if ($file){
          $file->delete();
        }
      }

      $db->commit();
      $this->view->success = true;
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      $this->view->error   = $translate->_('pagemusic_Unknown database error');
      throw $e;
    }
  }

  public function uploadArtAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');

    // only members can upload music
    if( !$this->_helper->requireUser()->checkRequire() ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_Max file size limit exceeded or session expired.');
      return;
    }

    if( !$this->getRequest()->isPost() ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_Invalid Upload or file too large');
      return;
    }

/*
    if( !preg_match('/\.(jpg|jpeg|gif|png|tmp)$/', $_FILES['Filedata']['tmp_name']) ){
      $this->view->status = false;
      $this->view->error  = $translate->_('pagemusic_Invalid file type');
			return ;
    }
*/

    $playlist_id = $this->getRequest()->getParam('playlist_id');
    if ($playlist_id){
      $playlist = Engine_Api::_()->getItem('playlist', $playlist_id);
    }
    
    $db = Engine_Api::_()->getDbtable('playlists', 'pagemusic')->getAdapter();
    $db->beginTransaction();

    try {
      $this->view->photo = $photo = Engine_Api::_()->getApi('core', 'pagemusic')->uploadPhoto($_FILES['Filedata']);
      $this->view->status = true;
      $this->view->photo_id = $photo->getIdentity();
      $this->view->photo = $photo->toArray();

      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      $this->view->status  = false;
      $this->view->message = $translate->_('pagemusic_Upload failed by database query');
      throw $e;
    }

  }

  public function removeArtAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    if (!$this->getRequest()->isPost()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('isGet');
      exit;
    }

    $storage = Engine_Api::_()->storage();
    $photo_id = $this->getRequest()->getParam('photo_id');
		$playlist = null;
		
    if (!$photo_id){
      $playlist_id = $this->getRequest()->getParam('playlist_id');
      if ($playlist_id){
        $playlist = Engine_Api::_()->getItem('playlist', $playlist_id);
        if ($playlist){
          $photo_id = $playlist->photo_id;
        }
      }
    }

    if (!$photo_id){
      $this->view->success = false;
      $this->view->error   = $translate->_('pagemusic_Not a valid request data.');
      $this->view->post    = $_POST;
      return;      
    }

		$table = Engine_Api::_()->getDbTable('playlists', 'pagemusic');
		if (!$playlist){
			$select = $table->select()->where('photo_id = ?', $photo_id);
			$playlist = $table->fetchRow($select);
		}
    
    $db = $table->getAdapter();
    $db->beginTransaction();
    try{
      if ($playlist){
        $playlist->photo_id = 0;
        $playlist->save();

				$search_api = Engine_Api::_()->getDbTable('search', 'page');
				$search_api->saveData($playlist);
      }
      
      $photo = $storage->get($photo_id);
      if ($photo){
        $photo->delete();
      }

      $thumb = $storage->get($photo_id, 'thumb.profile');
      if ($thumb){
        $thumb->delete();
      }

			$thumb = $storage->get($photo_id, 'thumb.mini');
      if ($thumb){
        $thumb->delete();
      }

      $db->commit();
      $this->view->success = true;
    }
    catch(Exception $e){
      $db->rollback();
      $this->view->success = false;
      $this->view->error   = $translate->_('pagemusic_Unknown database error');
      throw $e;  
    }
  }

	public function playAction()
	{
		$song_id = $this->_getParam('song_id');

		if (!$song_id){
			return ;
		}

		$song = Engine_Api::_()->getItem('song', $song_id);
		if (!$song){
			return ;
		}

		$playlist = $song->getPlaylist();
		if (!$playlist){
			return ;
		}

		$page = $playlist->getPage();
		if (!$page){
			return ;
		}
		
		$table = Engine_Api::_()->getDbTable('plays', 'pagemusic');
		$api = Engine_Api::_()->pagemusic();
		$viewer = Engine_Api::_()->user()->getViewer();

		$play = $table->createRow();
		$play->user_id = $viewer->getIdentity();
		$play->page_id = $page->getIdentity();
		$play->playlist_id = $playlist->getIdentity();
		$play->song_id = $song->getIdentity();
		$play->date = date('Y-m-d H:i:s');

		$db = $table->getAdapter();
    $db->beginTransaction();
		try{
			$song->play_count++;
			$playlist->play_count++;

			if (!$api->isListenedSong($song)){
				$song->listener_count++;
			}

			if (!$api->isListenedPlaylist($playlist)){
				$playlist->listener_count++;
			}

			$play->save();
			$song->save();
			$playlist->save();
			
			$db->commit();
		}
		catch (Exception $e){
			$db->rollback();
			throw $e;
		}

		$this->veiw->song_listeners = $song->listener_count;
		$this->veiw->playlist_listeners = $playlist->listener_count;
		$this->veiw->playlist_plays = $playlist->play_count;
	}
}