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



class Hebadge_Widget_BadgesManageController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('badges', 'hebadge');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()){
      return $this->setNoRender();
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $this->view->params = $params = array(
      'text' => $request->getParam('text')
    );

    $this->view->paginator = $paginator = $table->getMemberPaginator($viewer, $params, false);

    $paginator->setItemCountPerPage( $this->_getParam('itemCountPerPage', 12) );
    $paginator->setCurrentPageNumber( $request->getParam('page') );

    $ids = array();
    foreach ($paginator->getCurrentItems() as $item){
      $ids[] = $item->badge_id;
    }

    if ($viewer->getIdentity()){
      $this->view->members = $table->getOwnerMembersByBadgeIds($ids, $viewer);
    }

    $this->getElement()->setAttrib('id', 'content_id_' . $this->view->identity);
    $this->view->name = $this->getElement()->getName();
    $this->view->simple_name = str_replace("-", "_", str_replace(".", "_", $this->getElement()->getName()));
    $this->view->paginator_type = $this->_getParam('paginator_type');

  }



}