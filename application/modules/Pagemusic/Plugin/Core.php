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

class Pagemusic_Plugin_Core
{
  public function removePage($event)
  {
	  $payload = $event->getPayload();
	  $page = $payload['page'];
	  
	  $table = Engine_Api::_()->getDbTable('playlists', 'pagemusic');
	  $select = $table->select()->where('page_id = ?', $page->getIdentity());
	  $playlists = $table->fetchAll($select);
	  
	  foreach ($playlists as $playlist){
	  	$playlist->delete();
	  }
  }
    
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if ( $payload instanceof User_Model_User ){
      $table = Engine_Api::_()->getDbTable('playlists', 'pagemusic');
      $select = $table->select()->where('owner_id = ?', $payload->getIdentity());
      $playlists = $table->fetchAll($select);

      foreach ($playlists as $playlist){
        $playlist->delete();
      }
    }
  }

  public function onRenderLayoutDefault($event)
  {
    // Arg should be an instance of Zend_View
    $view = $event->getPayload();
    $viewer = Engine_Api::_()->user()->getViewer();

    if( $view instanceof Zend_View && Engine_Api::_()->core()->hasSubject() ){
      $subject = Engine_Api::_()->core()->getSubject();
      
      if ($subject->getType() != 'page'){
        return ;
      }
      $auth = Engine_Api::_()->authorization()->context;
      $isAllowedPost = $auth->isAllowed($subject, $viewer, 'music_posting');

			$isAllowedComment = $subject->authorization()->isAllowed($viewer, 'comment');
      
      $script = "
        page_music.url.page = '".$subject->getHref()."';
        page_music.url.index = '".$view->url(array(), 'page_music')."';
        page_music.url.save = '".$view->url(array('action' => 'save'), 'page_music')."';
        page_music.url.view = '".$view->url(array('action' => 'view'), 'page_music')."';
        page_music.url.manage = '".$view->url(array('action' => 'manage'), 'page_music')."';
        page_music.url.delete_url = '".$view->url(array('action' => 'delete'), 'page_music')."';
        page_music.url.edit = '".$view->url(array('action' => 'edit'), 'page_music')."';
        page_music.url.order = '".$view->url(array('action' => 'order'), 'page_music')."';
        page_music.url.rename = '".$view->url(array('action' => 'rename'), 'page_music')."';
        page_music.url.remove_song = '".$view->url(array('action' => 'remove-song'), 'page_music')."';
        page_music.url.remove_art = '".$view->url(array('action' => 'remove-art'), 'page_music')."';
				page_music.url.play = '".$view->url(array('action' => 'play'), 'page_music')."';
        page_music.page_id = ".(int)$view->subject()->getIdentity().";
        page_music.user_id = ".(int)$viewer->getIdentity().";
				page_music.allowed_comment = ".(int)$isAllowedComment.";
				page_music.allowed_post = ".(int)$isAllowedPost.";
      ";

      $script .= "
        en4.core.runonce.add(function(){
          page_music.init();
        });
      ";

      $view->headScript()
        ->appendFile('application/modules/Pagemusic/externals/scripts/music.js')
        ->appendFile('application/modules/Pagemusic/externals/standalone/audio.js')
        ->appendScript($script);

      $view->headLink()
        ->prependStylesheet($view->baseUrl() . '/application/modules/Pagemusic/externals/styles/music.css');
    }
  }
  
}