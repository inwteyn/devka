<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();
  }

  protected function _bootstrap($resource = null)
  {
    parent::_bootstrap($resource);
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Touch_Plugin_Core, 201);

		// Initialize contextSwitch helper
    if(Engine_Api::_()->getApi('core', 'touch')->isTouchMode()){
      Zend_Controller_Action_HelperBroker::addHelper(new Touch_Controller_Action_Helper_ContextSwitch());
      Zend_Controller_Action_HelperBroker::addHelper(new Touch_Controller_Action_Helper_TouchRedirector());
    }
  }
}