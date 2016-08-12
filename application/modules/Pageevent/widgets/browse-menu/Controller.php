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

class Pageevent_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('pageevent_main');

    $filter = Zend_Controller_Front::getInstance()->getRequest()->getParam('filter');

    foreach( $navigation->getPages() as $page ) {
      if( $page->route == "pageevent_upcoming" && $filter == "future" ) {
        $page->active = true;
      }

      if( $page->route == "pageevent_past" && $filter == "past" ) {
        $page->active = true;
      }

      if( $page->route == "pageevent_manage" && $filter == "my" ) {
        $page->active = true;
      }
    }
  }
}
