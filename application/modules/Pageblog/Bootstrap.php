<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageblog_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
	{
		parent::__construct($application);
		$this->initViewHelperPath();

    $front =  Zend_Controller_Front::getInstance();
    $plugin =  new Pageblog_Controller_Helper_Pageblog();
    $front->registerPlugin($plugin);
	}
}