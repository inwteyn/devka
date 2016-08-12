<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 27.02.12
 * Time: 13:08
 * To change this template use File | Settings | File Templates.
 */
class Store_Widget_AdminMainMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), $this->_getParam('active'));


    $ordersTbl = Engine_Api::_()->getItemTable('store_order');
    $select = $ordersTbl->select()
        ->from($ordersTbl->info('name'), array('cnt'=>'COUNT(*)'))
        ->where("status in ('processing','shipping')")
    ;
    $ordersCount = $ordersTbl->fetchRow($select);
    $this->view->ordersCount = $ordersCount['cnt'];

    $requestsTbl = Engine_Api::_()->getItemTable('store_request');
    $select = $requestsTbl->select()
      ->from($requestsTbl->info('name'), array('cnt'=>'COUNT(*)'))
      ->where("status in ('pending','waiting')")
    ;
    $rCount = $ordersTbl->fetchRow($select);
    $this->view->rCount = $rCount['cnt'];
  }
}
