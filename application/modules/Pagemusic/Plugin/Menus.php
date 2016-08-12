<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagemusic_Plugin_Menus
{
  public function onMenuInitialize_PageMusicAll($row)
  {
      $subject = Engine_Api::_()->core()->getSubject();

	  return array(
	    'label' => 'pagemusic_Browse Music',
        'href' => $subject->getHref().'/content/pagemusic/',
	  	'onClick' => 'page_music.index(); return false;',
	  	'route' => 'page_music'
	  );
  }

  public function onMenuInitialize_PageMusicManage($row)
  {
  	$subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'music_posting');

		if ($isAllowedPost){
			return array(
		    'label' => 'pagemusic_Manage Playlists',
            'href' => 'javascript:void(0);',
		  	'onClick' => 'page_music.manage(); return false;',// for SEO by Kirill
				'route' => 'page_music'
	  	);
		}

		return false;
  }

  public function onMenuInitialize_PageMusicCreate($row)
  {
  	$subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'music_posting');
		if ($isAllowedPost){
			return array(
		    'label' => 'pagemusic_Create Playlist',
		  	'onClick' => 'page_music.create();',
            'href' => 'javascript:void(0);',
		  	'route' => 'page_music',
	  	);
		}

		return false;
  }

  public function onMenuInitialize_PagemusicMainManage($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    return true;
  }
}