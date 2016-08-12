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

class Touch_Widget_MainHeaderController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $params = $this->_getAllParams();
    $this->view->params = $params;
    if(isset($params['title']))
      $this->view->title = $params['title'];
    unset($params['title']);

		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		if( $viewer->getIdentity() )
		{
      $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
    }

		$request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->notificationOnly = $request->getParam('notificationOnly', false);
    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.notificationupdate');		
  }
}