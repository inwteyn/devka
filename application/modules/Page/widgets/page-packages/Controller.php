<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2013-20-03 9:30:11 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Page_Widget_PagePackagesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    if( !$settings->getSetting('page.package.enabled', 0) ) {
      return $this->setNoRender();
    }

    $this->view->packages = Engine_Api::_()->getDbtable('packages', 'page')->getPackages(array('all' => true));
  }
}