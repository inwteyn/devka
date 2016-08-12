<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchAction.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Touch_View_Helper_TouchAction extends Zend_View_Helper_Action
{

	public function touchAction($action, $controller, $module = null, array $params = array())
  {
		$this->resetObjects();

		if (null === $module) {
				$module = $this->defaultModule;
		}

		// clone the view object to prevent over-writing of view variables
		$viewRendererObj = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		Zend_Controller_Action_HelperBroker::addHelper(clone $viewRendererObj);

		$this->request->setParams($params)
									->setModuleName($module)
									->setControllerName($controller)
									->setActionName($action)
									->setDispatched(true);

		$moduleDir = Engine_Api::_()->touch()->getPath($module);

		if ( is_dir($moduleDir) ) {
			$moduleDir .= DIRECTORY_SEPARATOR . Zend_Controller_Front::getInstance()->getModuleControllerDirectoryName();
			$this->dispatcher->setControllerDirectory($moduleDir, $module);
		} else {
			return '';
		}

		$this->dispatcher->dispatch($this->request, $this->response);

		// reset the viewRenderer object to it's original state
		Zend_Controller_Action_HelperBroker::addHelper($viewRendererObj);

		if (!$this->request->isDispatched()
				|| $this->response->isRedirect())
		{
				// forwards and redirects render nothing
				return '';
		}

		$return = $this->response->getBody();
		$this->resetObjects();

		return $return;
  }
}