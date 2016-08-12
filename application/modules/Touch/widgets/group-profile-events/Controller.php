<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Widget_GroupProfileEventsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    // Don't render if event item not available
    if( !Engine_Api::_()->hasItemType('event') ) {
      return $this->setNoRender();
    }

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $group = Engine_Api::_()->core()->getSubject('group');
    if( !$group->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $search = $request->getParam('search');
    $page = (int) $request->getParam('page');
    $table = Engine_Api::_()->getDbTable('events', 'event');
    $tableName = $table->info('name');

    $tableEvent = Engine_Api::_()->getDbtable('events', 'event');
    $select = $tableEvent->select()
        ->where('parent_type = ?', 'group')
        ->where('parent_id = ?', $group->getIdentity());

    $this->view->form = $form = new Touch_Form_Search();
    if (!empty($search)){
      $form->getElement('search')->setValue($search);
      $select->where("$tableName.title LIKE ? OR $tableName.description LIKE ?", '%'.$search.'%');
    }

    // Get paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->canAdd = $canAdd = $group->authorization()->isAllowed(null,  'event') && Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($page);

    // Do not render if nothing to show and cannot upload
    if( $paginator->getTotalItemCount() <= 0 && !$canAdd && !$page && !$search) {
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