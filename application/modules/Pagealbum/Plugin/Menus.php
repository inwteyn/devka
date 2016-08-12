<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagealbum_Plugin_Menus
{
  public function onMenuInitialize_PageAlbumAll($row)
  {
      $subject = Engine_Api::_()->core()->getSubject();

	  return array(
	    'label' => 'Browse Albums',
	  	'onClick' => 'javascript:page_album.list(); return false;',
      'href' => $subject->getHref().'/content/pagealbums/',
	  	'route' => 'page_album'
	  );
  }


  public function onMenuInitialize_PageAlbumMine($row)
  {
  	$subject = Engine_Api::_()->core()->getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
  	$isAllowedPost = $auth->isAllowed($subject, $viewer, 'album_posting');

		if ($isAllowedPost) {
			return array(
		    'label' => 'Manage Albums',
        'href' => 'javascript:void(0);',
		  	'onClick' => 'javascript:page_album.manage();',
				'route' => 'page_album'
	  	);
		}

		return false;
  }
  
  public function onMenuInitialize_PageAlbumCreate($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'album_posting');

    if ($isAllowedPost){
			return array(
		    'label' => 'Add Photos',
		  	'onClick' => 'page_album.create();',
        'href' => 'javascript:void(0);',
		  	'route' => 'page_album',
	  	);
		}

		return false;
  }

  public function onMenuInitialize_PagealbumMainManage($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    return true;
  }
}