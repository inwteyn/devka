<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Plugin_Menus
{
  public function onMenuInitialize_PageVideoAll($row)
  {
      $subject = Engine_Api::_()->core()->getSubject();

	  return array(
	    'label' => 'Browse Videos',
        'href' => $subject->getHref().'/content/pagevideos/',
	  	'onClick' => 'javascript:page_video.all(); return false;',
	  	'route' => 'page_video'
	  );
  }

  public function onMenuInitialize_PageVideoManage($row)
  {
  	$subject = Engine_Api::_()->core()->getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'video_posting');

		if ($isAllowedPost){
			return array(
		    'label' => 'Manage Videos',
            'href' => 'javascript:void(0);',
		  	'onClick' => 'javascript:page_video.manage(); return false;',// for SEO by Kirill
				'route' => 'page_video'
	  	);
		}

		return false;
  }
  
  public function onMenuInitialize_PageVideoCreate($row)
  {
  	$subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'video_posting');

		if ($isAllowedPost){
			return array(
		    'label' => 'Add Video',
            'href' => 'javascript:void(0);',
		  	'onClick' => 'page_video.create();',
		  	'route' => 'page_video',
	  	);
		}

		return false;
  }

  public function onMenuInitialize_PagevideoMainManage($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    return true;
  }
}