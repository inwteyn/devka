<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pagemusic_Widget_ProfileMusicsController extends Engine_Content_Widget_Abstract
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

    $params['page'] = $this->_getParam('page', 1);
    $params['ipp'] = $this->_getParam('itemCountPerPage', 4);
    $params['show'] = 3;
    $params['owner'] = $subject;

    // Get paginator
    $paginator = Engine_Api::_()->getApi('core', 'pagemusic')->getMusicPaginator($params);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    $this->view->paginator = $paginator;

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    $this->view->storage = Engine_Api::_()->storage();
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}