<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Touch_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
	public function routeStartup(Zend_Controller_Request_Abstract $request)
  {

		//Redirect upload action'
		$uploadAction = $request->getParam('touch-upload-action', null);

		if ( Engine_Api::_()->touch()->isTouchMode() && $uploadAction !== null)
		{
			$request->setRequestUri($uploadAction);
		}
    $this->detectSimulator($request);
	}

  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    if (!Engine_Api::_()->touch()->isTouchMode() )

    {
      return;
    }

		$session = new Zend_Session_Namespace('mobile');

		if ( $session->mobile ){
			$session->mobile = false;
			$request = Engine_Api::_()->touch()->resetMobi($request);
		}

    $module = $request->getModuleName();
    $controller = $request->getControllerName();
		$action = $request->getActionName();
    if(!$request->getParam('tab') && Engine_Api::_()->touch()->isModuleEnabled('timeline')){
      $settings = Engine_Api::_()->getApi('settings', 'core');

      if ($module == 'user' && $controller == 'profile' && $action == 'index') {
        if($settings->__get('timeline.usage', 'choice') == 'force'){
          $request->setModuleName('timeline');
        } else {
          $id  = $request->getParam('id');
          $user = Engine_Api::_()->user()->getUser($id);
          if ($user->getIdentity()) {
            $user = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($user->getIdentity());
          }
          if( $user->getIdentity() && Engine_Api::_()->getDbTable('settings', 'user')->getSetting($user, 'timeline-usage')){
            $request->setModuleName('timeline');
          }
        }
      }
    }
		if ($module == 'hecore' && $controller == 'module' && $action == 'license'){
			return;
		}
		
    if (preg_match('/^admin-/', $controller)){
      return;
    }
   	// Ignoring all module customizations --=: Ulan :=--
    $oldmodule = $module;
    $module = $this->ignoreCustomizations($request);
    // ProfileUrlShortener
	  Zend_Registry::set('pus_redirect', false);

    // Mode Switch
    if (($module == 'mobile' && $controller == 'index' && $action == 'mode-switch')
        || ($module == 'touch' && $controller == 'index' && $action == 'touch-mode-switch')){
      return ;
    }

    // DashBoard
    if ($module == 'mobile' && $controller == 'index' && $action == 'index'){
      $request->setModuleName('touch');
    }
    $redirect_success = true;


    $sr_response = null;
    if ($module != 'touch' && $module != 'mobile' ) {
      $sr_response = Engine_Api::_()->touch()->setupRequest($request);
      $redirect_success = $sr_response['level'] > 0;
        //Engine_Api::_()->touch()->redirectController($module);
      //@todo
      //$redirect_success = Engine_Api::_()->touch()->setupRequest($request);
      if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('touch.integrations.only', false) && false === $redirect_success)
      {
        if($module!=$oldmodule){
          $request->setParam('customization', true);
          $request->setParam('original', $oldmodule);
          $request->setParam('custom', $module);
        }
        $request->setModuleName('touch');
        $request->setControllerName('error');
        $request->setActionName('notfound');
      }
    }

		if ($request->getParam('format') == 'smoothbox')
		{
			$request->setParam('format', 'html');
		}
    Engine_Api::_()->touch()->setLayout();
    if(!$redirect_success){

      $request->setParam('not_touch_integrated', true);
    }
  }

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
    if (Engine_Api::_()->touch()->siteMode() !== 'touch')
    {
      return;
    }
    $this->ignoreCustomizations($request);
		if ($request->getControllerName() == 'error' && ($request->getModuleName() == 'core' || $request->getModuleName() == 'mobile')){
      $request->setModuleName('touch');
		}
	}

  private function detectSimulator(Zend_Controller_Request_Abstract $request)
  {
    $view = Zend_Registry::get('Zend_View');
    if ($view instanceof Zend_View) {
      $user = Engine_Api::_()->user()->getViewer();
      if (isset($user->level_id) && $user->level_id < 4 && $request->getParam('format') != 'touchajax') {
        $script = "
        Cookie.write('windowwidth', window.getWidth());
        window.onfocus = function () {
        Cookie.write('windowwidth', window.getWidth());
        };
        ";

        $view->headScript()
          ->appendScript($script);
      }
    }
  }

 	// Ignore all module customizations --=: Ulan :=--
  protected function ignoreCustomizations($request)
  {
    $module = $request->getModuleName();

    //Ignoring Social DNA plugin (User customization)
    if ($module == 'socialdna'){
      $module = 'user';
    }
  	// Ignoring SocialEngineAddOns Sitealbum plugin (Std. Album customization)
    if($module == 'sitealbum'){
		  $module = 'album';
	  }

  	// Ignoring YouNet Advanced Music plugin (Std. Music customization)
    if($module == 'ynmusic'){
		  $module = 'music';
	  }

    // Ignoring YouNet Advanced Group plugin (Std. Group customization)
    if($module == 'advgroup'){
		  $module = 'group';
	  }

    // Ignoring YouNet Avatar plugin (Std. Profile customization)
    if($module == 'avatar'){
		  $module = 'user';
	  }

    // Ignoring YouNet Advanced Search plugin (Std. Search customization)
    if($module == 'ynadvsearch'){
		  $module = 'core';
	  }

    // Ignoring YouNet Advanced Group plugin (Std. Group customization)
    if($module == 'ynevent'){
		  $module = 'event';
	  }

    // Ignoring YouNet Advanced Group plugin (Std. Group customization)
    if($module == 'ynblog'){
		  $module = 'blog';
	  }

    // Ignoring YouNet Advanced Group plugin (Std. Group customization)
    if($module == 'nyvideo'){
		  $module = 'video';
	  }
    if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled($module))
      $request->setModuleName($module);
    return $module;
  }

}