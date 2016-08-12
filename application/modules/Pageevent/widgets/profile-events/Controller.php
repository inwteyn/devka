<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pageevent_Widget_ProfileEventsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
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
    if( !($subject instanceof User_Model_User) ) {
      return $this->setNoRender();
    }

    $params['page'] = $this->_getParam('page', 1);
    $params['ipp'] = $this->_getParam('itemCountPerPage', 5);
    $params['view'] = 2;
    $params['owner'] = $subject;

    $paginator = Engine_Api::_()->getApi('core', 'pageevent')->getEventsPaginator($params);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

    $this->view->paginator = $paginator;
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}