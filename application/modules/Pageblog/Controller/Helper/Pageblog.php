<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pageblog.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
class Pageblog_Controller_Helper_Pageblog extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('blog');
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if ($module && $module->enabled && $settings->getSetting('page.browse.pageblog')) {
            if ($request->getModuleName() == 'blog' && $request->getControllerName() == 'index' && ($request->getActionName() == 'index' || $request->getActionName() == 'manage')) {
                $request->setModuleName('pageblog');
                $request->setControllerName('blogs');

                if ($request->getActionName() == 'index')
                    $request->setActionName('browse');
            }
        }

        $ynblog = Engine_Api::_()->getDbTable('modules', 'core')->getModule('ynblog');
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if ($ynblog && $ynblog->enabled && $settings->getSetting('page.browse.pageblog')) {
            if ($request->getModuleName() == 'ynblog' && $request->getControllerName() == 'index' && ($request->getActionName() == 'index' || $request->getActionName() == 'manage')) {
                $request->setModuleName('pageblog');
                $request->setControllerName('blogs');

                if ($request->getActionName() == 'index')
                    $request->setActionName('browse');
            }
        }
    }
}