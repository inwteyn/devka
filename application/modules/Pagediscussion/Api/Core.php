<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Api_Core extends Core_Api_Abstract
{
  public function getInitJs($content_info, $subject = null)
	{
  	    if (empty($content_info))
  		    return false;

        $content = $content_info['content'];
        $content_id = $content_info['content_id'];
    $res = "Pagediscussion.init_discussions();";

    if( $subject->is_timeline ) {
      $tbl = Engine_Api::_()->getDbTable('content', 'page');
      $id = $tbl->select()->from($tbl->info('name'), array('content_id'))
        ->where('page_id = ?', $subject->getIdentity())
        ->where("name = 'pagediscussion.profile-discussion'")
        ->where('is_timeline = 1')
        ->query()
        ->fetch();
      $res = "tl_manager.fireTab('{$id['content_id']}');";
    }

        if ($content == 'discussion'){
            $discussion = Engine_Api::_()->getItem('pagediscussion_pagetopic', $content_id);
            if (!$discussion)
  		        return false;
//  	    	return "Pagediscussion.init_discussions();";/// for SEO by Kirill
      return "Pagediscussion.goDiscussionTab({$content_id});" . $res;
        }elseif ($content == 'pagediscussions')/// for SEO by Kirill
      return $res;
        elseif($content=='discussion_page'){
      return $res;
        }
        return false;
  }

//    if ($content == 'discussion') {
//      if ($child_id) {
//        $this->view->initJs = 'Pagediscussion.goDiscussionTab('.$content_id.', '.$child_id.');';
//      } else {
//        $this->view->initJs = 'Pagediscussion.goDiscussionTab('.$content_id.');';
//      }
//    }

  public function isAllowedPost( $page ) {
    if( !$page )
      return false;
    $auth = Engine_Api::_()->authorization()->context;
    return $auth->isAllowed($page, Engine_Api::_()->user()->getViewer(), 'disc_posting');
  }

}
