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

class Touch_Widget_MiniSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		$require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if($require_check && !$viewer->getIdentity()){
			$this->view->require_check = 1;
    }
		
    if( $viewer->getIdentity() )
    {
      $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
    }
  }
}