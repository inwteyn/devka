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
class Headvancedmembers_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {

    if ((Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('apptouch') &&
        Engine_Api::_()->apptouch()->isApptouchMode())) {
      return false;
    }

//    print_die('asdf');

    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();

    /**
     * @var $settings Core_Api_Settings
     */
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if ($module == 'user' && $controller == 'index' && $action == 'browse') {

      if ($settings->__get('headvancedmembers.usage', '1') == '1' || 1) {
        $request->setModuleName('headvancedmembers');
        return;
      }
    }
  }
}
