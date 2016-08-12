<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 27.02.12
 * Time: 13:08
 * To change this template use File | Settings | File Templates.
 */
class Store_Widget_AdminAddonsMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_addons', array(), $this->_getParam('active'));
  }
}
