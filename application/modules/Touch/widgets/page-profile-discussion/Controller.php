<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Touch_Widget_PageProfileDiscussionController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  protected $_widget_url;

  public function indexAction()
  {
    $api = Engine_Api::_()->core();
    $subject_id = ($api->hasSubject()) ? $api->getSubject()->getIdentity() : 0;

    if (!Engine_Api::_()->touch()->checkPageWidget($subject_id, 'touch.page-profile-discussion')){
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->isAllowedPost = $isAllowedPost = $subject->authorization()->isAllowed($viewer, 'posting');

    $this->view->can_create = $isAllowedPost;

    $subject = Engine_Api::_()->core()->getSubject('page');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')){
      return $this->setNoRender();
    }

    $this->_widget_url = $this->view->url(array(
      'action' => 'index',
      'page_id' => $subject->getIdentity()
    ),'page_discussion');

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion')
        ->getPaginator($subject->getIdentity(), $this->_getParam('page', 1));

    $paginator->setDefaultItemCountPerPage(10);
    
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->paginator = $paginator;

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

  }

  public function getChildCount()
  {
    return $this->_childCount;
  }

  public function getHref()
  {
    return $this->_widget_url;
  }

}