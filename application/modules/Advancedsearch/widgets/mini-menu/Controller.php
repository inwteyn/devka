<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Advancedsearch_Widget_MiniMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if(!$require_check){
      if( $viewer->getIdentity()){
        $this->view->search_check = true;
      }
      else{
        $this->view->search_check = false;
      }
    }
    else $this->view->search_check = true;

    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'advancedsearch')
      ->getNavigation('core_mini');

    if( $viewer->getIdentity() )
    {
      $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
    }

      $adapter = Engine_Api::_()->authorization()->isAllowed($viewer);

      $this->view->authorization=$adapter;

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->notificationOnly = $request->getParam('notificationOnly', false);
    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.notificationupdate');
    $string_for_array_mini_menu = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
    $mini_menu_array = explode(',',$string_for_array_mini_menu);
    $this->view->types = $mini_menu_array;
  }
}