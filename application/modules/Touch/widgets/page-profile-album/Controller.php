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
    
class Touch_Widget_PageProfileAlbumController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
  protected $_widget_url;
	
  public function indexAction()
  {
    $api = Engine_Api::_()->core();
    $subject_id = ($api->hasSubject()) ? $api->getSubject()->getIdentity() : 0;

    if (!Engine_Api::_()->touch()->checkPageWidget($subject_id, 'touch.page-profile-album')){
      return $this->setNoRender();
    }
    $this->view->form_filter = new Touch_Form_Search();
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
    ),'page_album');

    $table = Engine_Api::_()->getItemTable('pagealbum');

    $select = $table->select()
      ->where('page_id = ?', $subject->getIdentity())
      ->order('modified_date DESC');
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('pagealbum', null, 'create');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $total_item_count = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($total_item_count);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    // Do not render if nothing to show and no search
    if( $total_item_count <= 0 ) {
      return $this->setNoRender();
    }

    if ($this->_getParam('titleCount', false) && $total_item_count > 0){
      $this->_childCount = $total_item_count;
    }
  }

  public function getApi()
  {
		return $this->api = Engine_Api::_()->getApi('core', 'pagealbum');
  }
  
	public function getTable()
  {
  	return Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
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