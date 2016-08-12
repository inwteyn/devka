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



class Hebadge_Widget_LastMembersController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('members', 'hebadge')->getLastMembers($viewer);
    $paginator->setItemCountPerPage(5);

    $item_ids = array();
    $badge_ids = array();

    foreach ($paginator->getCurrentItems() as $item){
      $item_ids[] = array(
        'type' => $item->object_type,
        'id' => $item->object_id
      );
      $badge_ids[] = array(
        'type' => 'hebadge_badge',
        'id' => $item->badge_id
      );
    }

    if( count($item_ids) <= 0 ) {
      return $this->setNoRender();
    }

    $this->view->objects = $objects = $this->prepareKeys(Engine_Api::_()->hebadge()->getItems($item_ids));
    $this->view->badges = $badges = $this->prepareKeys(Engine_Api::_()->hebadge()->getItems($badge_ids));

  }

  public function prepareKeys($array)
  {
    $new_array = array();
    foreach ($array as $item){
      if (empty($item) || !($item instanceof Core_Model_Item_Abstract)){
        continue ;
      }
      $new_array[$item->getGuid()] = $item;
    }
    return $new_array;
  }


}