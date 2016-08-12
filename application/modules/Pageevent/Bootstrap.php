<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

    $front =  Zend_Controller_Front::getInstance();
    $plugin =  new Pageevent_Controller_Helper_Pageevent();
    //$front->registerPlugin(new Pageevent_Plugin_Core());
    $front->registerPlugin($plugin);
  }


}