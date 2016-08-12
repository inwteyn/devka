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



class Hebadge_Widget_BestMembersController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('badges', 'hebadge');
    $viewer = Engine_Api::_()->user()->getViewer();

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('members', 'hebadge')->getBestMembers();

    $paginator->setItemCountPerPage( $this->_getParam('itemCountPerPage', 5) );
    $paginator->setCurrentPageNumber( $request->getParam('page') );

    // $paginator->getTotalItemCount() // TODO not working ..

    $item_ids = array();
    $badge_count = array();
    foreach ($paginator->getCurrentItems() as $item){
      $item_ids[] = array(
        'type' => $item->object_type,
        'id' => $item->object_id
      );
      $badge_count[$item->object_type . '_' . $item->object_id] = $item->badge_count;
    }

    $this->view->items = $items = Engine_Api::_()->hebadge()->getItems($item_ids);
    $this->view->badge_count = $badge_count;

    $this->getElement()->setAttrib('id', 'content_id_' . $this->view->identity);
    $this->view->name = $this->getElement()->getName();
    $this->view->simple_name = str_replace("-", "_", str_replace(".", "_", $this->getElement()->getName()));
    $this->view->paginator_type = $this->_getParam('paginator_type');

    if( count($item_ids) <= 0 ) {
      return $this->setNoRender();
    }


  }

}