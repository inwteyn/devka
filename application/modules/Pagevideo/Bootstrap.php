<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

    $front =  Zend_Controller_Front::getInstance();
    $plugin =  new Pagevideo_Controller_Helper_Pagevideo();
    $front->registerPlugin($plugin);
  }
}