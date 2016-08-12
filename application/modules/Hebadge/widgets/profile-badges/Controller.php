<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Widget_ProfileBadgesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
    } else {
      $subject = $viewer;
    }


    if (!$subject->getIdentity()){
      return $this->setNoRender();
    }


    $table = Engine_Api::_()->getDbTable('badges', 'hebadge');

    $this->view->paginator = $paginator = $table->getMemberPaginator($subject);
    $paginator->setItemCountPerPage(12);
    $paginator->setCurrentPageNumber( $this->_getParam('page') );

    if( $paginator->getTotalItemCount() <= 0 ) {
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