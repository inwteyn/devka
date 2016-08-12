<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IndexController.php 8190 2011-01-11 00:18:46Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Music_IndexController extends Touch_Controller_Action_Standard
{
  protected $_roles = array(
    'everyone'            => 'Everyone',
    'registered'          => 'All Registered Members',
    'owner_network'       => 'Friends and Networks',
    'owner_member_member' => 'Friends of Friends',
    'owner_member'        => 'Friends Only',
    'owner'               => 'Just Me'
  );
  public function init()
  {
    // Check auth
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
      return;
    }

    // Get viewer info
    $this->view->viewer     = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->is_mp3music_enabled  = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('mp3music');
  }
  
  public function browseAction()
  {
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('music_main');
    // Get browse params
    $this->view->formFilter = $formFilter = new Music_Form_Search();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);

    // Show
    $viewer = Engine_Api::_()->user()->getViewer();
    if( @$values['show'] == 2 && $viewer->getIdentity() ) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }
  
  public function manageAction()
  {
    // only members can manage music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('music_main');

    // Can create?
    $this->view->canCreate = false; //Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');
    
    // Get browse params
    $this->view->formFilter = $formFilter = new Music_Form_Search();
    $formFilter->removeElement('show');
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);

    // Get paginator
    $values['user'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function createAction()
  {
    // only members can upload music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid() ) {
      return;
    }

    // catch uploads from FLASH fancy-uploader and redirect to uploadSongAction()
    if( $this->getRequest()->getQuery('ul', false) ) {
      return $this->_forward('upload', 'song', null, array('format' => 'json'));
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('music_main');

    // Get form
    $this->view->form = $form = new Music_Form_Create();
    $this->view->playlist_id = $this->_getParam('playlist_id', '0');

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $playlist = $this->saveValues($form);
      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }

    return $this->_helper->redirector->gotoUrl($playlist->getHref(), array('prependBase' => false));
  }
  public function saveValues($form)
  {
    $playlist = null;
    $values   = $form->getValues();
    $translate= Zend_Registry::get('Zend_Translate');

    if(!empty($values['playlist_id']))
      $playlist = Engine_Api::_()->getItem('music_playlist', $values['playlist_id']);
    else {
      $playlist = Engine_Api::_()->getDbtable('playlists', 'music')->createRow();
      $playlist->title = trim($values['title']);
      if (empty($playlist->title))
        $playlist->title = $translate->_('_MUSIC_UNTITLED_PLAYLIST');

      $playlist->owner_type    = 'user';
      $playlist->owner_id      = Engine_Api::_()->user()->getViewer()->getIdentity();
      $playlist->description   = trim($values['description']);
      $playlist->search        = $values['search'];
      $playlist->save();
      $values['playlist_id']   = $playlist->playlist_id;

      // Assign $playlist to a Core_Model_Item
      $playlist = Engine_Api::_()->getItem('music_playlist', $values['playlist_id']);

      // get file_id list while store songs
      $file_ids = $this->storeSongs();

      // Attach songs (file_ids) to playlist
      if (!empty($file_ids)){
        $order = 0;
        foreach ($file_ids as $file_id){
          $this->addSong($playlist, $file_id, $order);
          $order ++;
        }
      }
      // Only create activity feed item if "search" is checked
      if ($playlist->search) {
        $activity = Engine_Api::_()->getDbtable('actions', 'activity');
        $action   = $activity->addActivity(
            Engine_Api::_()->user()->getViewer(),
            $playlist,
            'music_playlist_new',
            null,
            array('count' => count($file_ids))
        );
        if (null !== $action)
          $activity->attachActivity($action, $playlist);
      }
    }




    // Authorizations
    $auth = Engine_Api::_()->authorization()->context;
    $prev_allow_comment = $prev_allow_view = false;
    foreach ($this->_roles as $role => $role_label) {
      // allow viewers
      if ($values['auth_view'] == $role || $prev_allow_view) {
        $auth->setAllowed($playlist, $role, 'view', true);
        $prev_allow_view = true;
      } else{
        $auth->setAllowed($playlist, $role, 'view', 0);

      }

      // allow comments
      if ($values['auth_comment'] == $role || $prev_allow_comment) {
        $auth->setAllowed($playlist, $role, 'comment', true);
        $prev_allow_comment = true;
      } else
        $auth->setAllowed($playlist, $role, 'comment', 0);
    }
    // Rebuild privacy
    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
    foreach( $actionTable->getActionsByObject($playlist) as $action ) {
      $actionTable->resetActivityBindings($action);
    }



    if (!empty($values['art']))
      $playlist->setPhoto($form->art);

    return $playlist;
  }

  public function addSong($playlist, $file_id, $order)
  {
    $file = Engine_Api::_()->getItem('storage_file', $file_id['id']);

    if( $file ) {
      $playlist_song = Engine_Api::_()->getDbtable('playlistSongs', 'music')->createRow();
      $playlist_song->playlist_id = $playlist->getIdentity();
      $playlist_song->file_id     = $file->getIdentity();
      $playlist_song->title       = preg_replace('/\.(mp3|m4a|aac|mp4)$/i', '', $file_id['name']);
      $playlist_song->order       = $order;
      $playlist_song->save();
      return $playlist_song;
    }

    return false;
  }

  function storeSongs(){
    if (!empty($_FILES['file'])) {

      if (is_array($_FILES['file']['tmp_name'])) {
        foreach ($_FILES['file']['tmp_name'] as $k => $v) {
          $file['name'] = $_FILES['file']['name'][$k];
          $file['type'] = $_FILES['file']['type'][$k];
          $file['tmp_name'] = $_FILES['file']['tmp_name'][$k];
          $file['error'] = $_FILES['file']['error'][$k];
          $file['size'] = $_FILES['file']['size'][$k];

          // Process
          $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
          $db->beginTransaction();

          try {
            $song = Engine_Api::_()->getApi('core', 'music')->createSong($file);
            $db->commit();

          } catch( Music_Model_Exception $e ) {
            $db->rollback();

          } catch( Exception $e ) {
            $db->rollback();
            throw $e;
          }

          $file_ids[] = array('id'=>$song->file_id, 'name'=>$file['name']);
        }
      }
    }
    return $file_ids;
  }
  function getCreateForm(){
    $form = new Music_Form_Create();
    $form->removeElement('file');
    $form->removeElement('submit');
    $form->removeElement('fancyuploadfileids');
    $form->addElement('File', 'file', array(
			'label' => 'Add Music',
		));
    $form->file->addValidator('Extension', false, 'mp3,ogg, wav');
    $form->addElement('Button', 'submit', array(
      'label' => 'Save Music to Playlist',
      'type'  => 'submit',
    ));

    return $form;
  }
}