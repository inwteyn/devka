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

class Pagemusic_IndexController extends Touch_Controller_Action_Standard
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
		$this->view->ipp = $ipp = $this->_getParam('ipp', 10);

		$this->_params = array('page_id' => $page_id, 'p' => $p, 'ipp' => $ipp);

		if ($page_id){
			$subject = Engine_Api::_()->getItem('page', $page_id);
			$this->view->isAllowedView = $this->getApi()->isAllowedView($subject);

			if (!$this->view->isAllowedView){
				$this->view->isAllowedComment = false;
				return ;
			}
			$this->view->isAllowedComment = $this->getApi()->isAllowedComment($subject);
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
  
  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('playlists', 'pagemusic');
    $this->view->playlists = $table->getPaginator($this->_params);
  }

  public function manageAction()
  {
    $table = Engine_Api::_()->getDbTable('playlists', 'pagemusic');
    $this->_params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->playlists = $table->getPaginator($this->_params);
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
  }

}