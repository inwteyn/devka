<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Widget_MenuMainController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'touch')
      ->getNavigation('core_main');

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if(!Engine_Api::_()->getApi('settings', 'core')->__get('core.general.browse', 1) && !$viewer->getIdentity()){
      $navigation->removePage($navigation->findOneBy('route','user_general'));
    }

    if(!Engine_Api::_()->getApi('settings', 'core')->core_general_search){
      if( $viewer->getIdentity()){
        $this->view->search_check = true;
      }
      else{
        $this->view->search_check = false;
      }
    }
    else $this->view->search_check = true;

  }

  public function getCacheKey()
  {
    //return Engine_Api::_()->user()->getViewer()->getIdentity();
  }
}