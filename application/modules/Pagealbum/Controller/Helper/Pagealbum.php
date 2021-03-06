<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagealbum.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pagealbum_Controller_Helper_Pagealbum extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('album');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if( $module && $module->enabled && $settings->getSetting('page.browse.pagealbum')) {
      if( $request->getModuleName() == 'album' && $request->getControllerName() == 'index' && ($request->getActionName() == 'browse' || $request->getActionName() == 'manage')) {
        $request->setModuleName('pagealbum');
        $request->setControllerName('albums');
      }
    }
  }
}