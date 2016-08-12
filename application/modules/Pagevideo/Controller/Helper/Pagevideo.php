<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagevideo.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pagevideo_Controller_Helper_Pagevideo extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown( Zend_Controller_Request_Abstract $request )
  {
    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('video');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    if( $module && $module->enabled && $settings->getSetting('page.browse.pagevideo') ) {
      if( $request->getModuleName() == 'video' && $request->getControllerName() == 'index' && ($request->getActionName() == 'browse' || $request->getActionName() == 'manage') ) {
        $request->setModuleName('pagevideo');
        $request->setControllerName('videos');
      }
    }
  }
}