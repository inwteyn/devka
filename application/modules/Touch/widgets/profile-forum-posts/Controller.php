<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 8221 2011-01-15 00:24:02Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Touch_Widget_ProfileForumPostsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {

    if (!Engine_Api::_()->touch()->isModuleEnabled('forum')) {
      $this->setNoRender();
      return ; 
    }
 
    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');
    
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Get paginator
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $search = $request->getParam('search');
    $page = (int) $request->getParam('page');

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
    $postsTable = Engine_Api::_()->getDbtable('posts', 'forum');
    $tableName = $postsTable->info('name');

    $postsSelect = $postsTable->select()
      ->where('user_id = ?', $subject->getIdentity())->order('creation_date DESC');

    $this->view->form = $form = new Touch_Form_Search();
    if (!empty($search)){
      $form->getElement('search')->setValue($search);
      $postsSelect->where("$tableName.body LIKE ?", '%'.$search.'%');
    }
    $postsSelect->order('creation_date DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($postsSelect);
    $paginator->setCurrentPageNumber($page);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}