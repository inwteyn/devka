<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagemusic.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pagemusic_Controller_Helper_Pagemusic extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('music');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if( $module && $module->enabled && $settings->getSetting('page.browse.pagemusic')) {
      if( $request->getModuleName() == 'music' && $request->getControllerName() == 'index' && ($request->getActionName() == 'browse' || $request->getActionName() == 'manage')) {
        $request->setModuleName('pagemusic');
        $request->setControllerName('musics');
      }
    }
  }
}