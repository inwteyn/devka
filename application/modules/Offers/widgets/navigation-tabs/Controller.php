<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 13.06.12
 * Time: 14:55
 * To change this template use File | Settings | File Templates.
 */

class Offers_Widget_NavigationTabsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('offers_main');

    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $filter = !empty($p['filter']) ? $p['filter'] : 'upcoming';
    if ($filter != 'past' && $filter != 'upcoming' && $filter != 'mine' ) $filter = 'upcoming';

    /**
     * @var $page Zend_Navigation_Page_Mvc
     */
    foreach( $navigation->getPages() as $page ) {
      if ($page->route == "offers_".$filter) {
        $page->active = true;
      } else {
        $page->active = false;
      }
    }
  }
}