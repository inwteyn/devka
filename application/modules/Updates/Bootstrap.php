<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();
    
 	 // Add main user javascript
   $headScript = new Zend_View_Helper_HeadScript();
   $headScript->appendFile('application/modules/Updates/externals/scripts/core.js');
  }
}

