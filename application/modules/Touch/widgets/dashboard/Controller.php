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

class Touch_Widget_DashboardController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'touch')
      ->getNavigation('core_dashboard');
    

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if(!Engine_Api::_()->getApi('settings', 'core')->__get('core.general.browse', 1) && !$viewer->getIdentity()){
      $navigation->removePage($navigation->findOneBy('route','user_general'));
    }
  }
}