<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2015-10-06 16:58:20  $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Headvancedmembers_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    // Add view helper and action helper paths
    parent::__construct($application);
    $this->initViewHelperPath();
    $view = Zend_Registry::get('Zend_View');
    $view->headScript()->appendFile($view->advmembersBaseUrl() . 'application/modules/Headvancedmembers/externals/scripts/core.js');
  }

  public function _bootstrap($resource = null)
  {
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Headvancedmembers_Plugin_Core, 254);
  }
}