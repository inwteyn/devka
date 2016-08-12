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



class Hebadge_Widget_ProfileMembersController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    $subject = Engine_Api::_()->core()->getSubject();

    if (!($subject instanceof Hebadge_Model_Badge)){
      return $this->setNoRender();
    }

    $this->view->paginator = $paginator = $subject->getMembersPaginator();
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber( $this->_getParam('page') );

    $items = array();
    foreach ($paginator->getCurrentItems() as $item){
      $items[] = array(
        'type' => $item->object_type,
        'id' => $item->object_id
      );
    }
    $this->view->members = $members = Engine_Api::_()->hebadge()->getItems($items);

    if (Zend_Controller_Front::getInstance()->getRequest()->getParam('tab') == 'members'){
      $this->view->is_active = true;
    }

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