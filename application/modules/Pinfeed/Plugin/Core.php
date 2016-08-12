<?php
class Pinfeed_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {

    if (!(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('apptouch') &&
        Engine_Api::_()->apptouch()->isApptouchMode()) &&
      (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('touch') &&
        Engine_Api::_()->touch()->isTouchMode() ||
        Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('mobile') &&
        Engine_Api::_()->mobile()->isMobileMode())
    ) {
      return false;
    }


    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();

    /**
     * @var $settings Core_Api_Settings
     */
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if ($module == 'user' && $controller == 'index' && $action == 'home') {
      if(!Engine_Api::_()->hasModuleBootstrap('apptouch') || !Engine_Api::_()->apptouch()->isApptouchMode()){
      if ($settings->__get('Pinfeed.use_homepage', 'choice') == 1 ) {
        $request->setModuleName('pinfeed')->setActionName('index');
        return;
      }
      }

    }
    if ($module == 'user' && $controller == 'profile' && $action == 'index') {
      if(!Engine_Api::_()->hasModuleBootstrap('apptouch') || !Engine_Api::_()->apptouch()->isApptouchMode()){
      if ($settings->__get('Pinfeed.profile.usage', '0') == 1 && $settings->__get('timeline.usage', 'choice') != 'force') {
        $request->setModuleName('pinfeed')->setControllerName('index')->setActionName('profile');
        return;
      }
      }

    }
  }
}
?>