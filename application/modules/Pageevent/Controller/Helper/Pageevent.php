<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pageevent.php 2011-10-20 15:19 michael $
 * @author     Alexander
 */

class Pageevent_Controller_Helper_Pageevent extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    $enabledModule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('event');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if($enabledModule && $settings->getSetting('page.browse.pageevent'))
    {
      if ($request->getModuleName() == 'event' && $request->getControllerName() == 'index' && ($request->getActionName() == 'browse' || $request->getActionName() == 'manage')) {
        $request->setModuleName('pageevent');
        $request->setControllerName('events');
      }
    }
  }
}