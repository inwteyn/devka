<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageblog_Plugin_Menus
{
  public function onMenuInitialize_PageBlogAll($row)
  {
      $subject = Engine_Api::_()->core()->getSubject();
      
	  return array(
	    'label' => 'pageblog_Browse Blogs',
      'href' => $subject->getHref().'/content/pageblogs/',
	  	'onClick' => 'javascript:page_blog.list(); return false;',
	  	'route' => 'page_blog'
	  );
  }


  public function onMenuInitialize_PageBlogMine($row)
  {
  	$subject = Engine_Api::_()->core()->getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'blog_posting');
    
		if ($isAllowedPost){
			return array(
		    'label' => 'pageblog_Manage Blogs',
        'href' => 'javascript:void(0);',
		  	'onClick' => 'javascript:page_blog.my_blogs();',
				'route' => 'page_blog'
	  	);
		}

		return false;
  }
  
  public function onMenuInitialize_PageBlogCreate($row)
  {
  	$subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'blog_posting');

		if ($isAllowedPost){
			return array(
		    'label' => 'pageblog_Compose New Blog Entry',
		    'href' => 'javascript:void(0);',
		  	'onClick' => 'page_blog.create();',
		  	'route' => 'page_blog',
	  	);
		}

		return false;
  }

  public function onMenuInitialize_PageblogMainManage($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    return true;
  }
}