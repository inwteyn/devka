<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchRedirector.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 
class Touch_Controller_Action_Helper_TouchRedirector extends  Zend_Controller_Action_Helper_Redirector
{
	public function gotoRoute(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
	{

		$view = Zend_Registry::get('Zend_View');
		$url = $view->url($urlOptions, $name, $reset, $encode);

		$front = Zend_Controller_Front::getInstance();
		$request = $front->getRequest();

		$params = array(
			'parentRedirect'=>$url,
		);

    $request->setActionName('simple')
						->setControllerName('utility')
    				->setModuleName('touch')
						->setParams($params)
            ->setDispatched(false);
	}

	public function gotoRouteLocation(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
	{
		$view = Zend_Registry::get('Zend_View');
		$url = $view->url($urlOptions, $name, $reset, $encode);

		$front = Zend_Controller_Front::getInstance();
		$request = $front->getRequest();

		$params = array(
			'locationHref'=>$url,
		);

    $request->setActionName('simple')
						->setControllerName('utility')
    				->setModuleName('touch')
						->setParams($params)
            ->setDispatched(false);
	}
}