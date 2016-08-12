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

class Touch_Widget_EventProfileMembersController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    $this->getElement()->removeDecorator('Title');

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    $subject = Engine_Api::_()->core()->getSubject('event');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    $request = Zend_Registry::get('Zend_Controller_Front')->getRequest();

    $this->view->page = $page = $request->getParam('page', 1);
    $this->view->event = $event = Engine_Api::_()->core()->getSubject();

    $search = $this->view->search = $request->getParam('search');
    $waiting = $this->view->waiting = $request->getParam('waiting') ? 1 : 0;
    $tab = $this->view->tab = $request->getParam('tab');

    $this->view->form = $form = new Touch_Form_Search;
    $form->search->setValue($search);

    $tbl = $event->membership()->getReceiver();
    $select = $event->membership()
        ->getMembersObjectSelect(false)
        ->reset(Zend_Db_Select::COLUMNS)
        ->columns(new Zend_Db_Expr("COUNT(*)"));

    $this->view->waiting_count = $tbl->getAdapter()->fetchOne($select);

    $select = $event->membership()->getMembersObjectSelect(!$waiting);

    if (!empty($search)){
      $select->where("username LIKE ? OR displayname LIKE ?", '%'.$search.'%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator
        ->setItemCountPerPage(5)
        ->setCurrentPageNumber($page);

    $this->view->members = $paginator;

    if( $paginator->getTotalItemCount() <= 0 && empty($search) && !$waiting) {
      return $this->setNoRender();
    }
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}