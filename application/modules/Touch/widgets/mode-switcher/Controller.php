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

class Touch_Widget_ModeSwitcherController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->standard = $this->_getParam('standard', 'Standard');
		$this->view->touch = $this->_getParam('touch', 'TOUCH_MODE');
		$this->view->mobile = $this->_getParam('mobile', 'Mobile');

		$this->view->isMobileEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobile');
  }
}