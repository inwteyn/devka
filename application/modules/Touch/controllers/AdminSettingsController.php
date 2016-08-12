<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2011-12-14 14:02:00 ulan $
 * @author     Ulan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_AdminSettingsController extends Core_Controller_Action_Admin {

  protected $_basePath;


  public function indexAction(){
    $core_setting = Engine_Api::_()->getDbTable('settings', 'core');
    $this->view->form = $form = new Touch_Form_Admin_Settings_General();
    if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())){
      $core_setting->setSetting('touch.default', $this->_getParam('set_default'));
      $core_setting->setSetting('touch.integrations.only', $this->_getParam('integrations_only'));
      $core_setting->setSetting('touch.include.tablets', $this->_getParam('include_tablets'));
      $form->addNotice("TOUCH_Changes have been saved.");
    } else {
      $settings = array();
      $settings['set_default'] = $core_setting->getSetting('touch.default', false);
      $settings['integrations_only'] = $core_setting->getSetting('touch.integrations.only', false);
      $settings['include_tablets'] = $core_setting->getSetting('touch.include.tablets', false);
      $form->populate($settings);
    }
  }

  public function performanceAction(){
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('touch_admin_main', array(), 'touch_admin_main_performance_settings');

    $cacheSettings = array();
    $pref = 'touch.admin.cache.';
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->form = $form = new Touch_Form_Admin_Settings_Performance();
    if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())){
      $enable = $this->_getParam('enable');
      $min_lifetime = $this->_getParam('min_lifetime');
      $max_lifetime = $this->_getParam('max_lifetime');
      $cache_feature = $this->getRequest()->getPost('type');
      $err = false;
      try{
        if($min_lifetime <= 0 || $max_lifetime <= 0){
          $form->addError('TOUCH_Time must be positive');
          $err = true;
        }
        if($min_lifetime > 6000){
          $form->addError('TOUCH_Minimum lifetime is exceeded');
          $err = true;
        }
        if($max_lifetime > 6000){
          $form->addError('TOUCH_Maximum lifetime is exceeded');
          $err = true;
        }
        if($max_lifetime <= $min_lifetime){
          $form->addError('TOUCH_Maximum lifetime must be greater than minimum lifetime');
          $err = true;
        }
        if(isset($enable)){
          $settings->setSetting($pref.'enable', $enable);
        }
        if(isset($min_lifetime)){
          $settings->setSetting($pref.'min_lifetime', $min_lifetime);
        }
        if(isset($max_lifetime)){
          $settings->setSetting($pref.'max_lifetime', $max_lifetime);
        }
        if(isset($cache_feature)){
          $settings->setSetting($pref.'type', $cache_feature);
        }
        $form->addNotice("TOUCH_Changes have been saved.");
      } catch (Exception $e){
        if(!$err)
          $form->addError($e->getMessage());
        return;
      }
    }
    $cacheSettings['enable'] =  $settings->getSetting($pref.'enable');
    if($settings->getSetting($pref.'min_lifetime')){
      $cacheSettings['min_lifetime'] =  $settings->getSetting($pref.'min_lifetime');
    }
    if($settings->getSetting($pref.'max_lifetime')){
      $cacheSettings['max_lifetime'] =  $settings->getSetting($pref.'max_lifetime');
    }

    if($settings->getSetting($pref.'type')){
      $cacheSettings['type'] =  $settings->getSetting($pref.'type');
    }
    $form->populate($cacheSettings);
  }

  public function UITipsAction(){

  }

  public function appIconSetAction(){
    $path = DIRECTORY_SEPARATOR.
          'public'.
          DIRECTORY_SEPARATOR.
          'touch'.
          DIRECTORY_SEPARATOR.
          'homescreen';
    $this->view->form = $form = new Touch_Form_Admin_Settings_AppIconSet();
    // Check if folder exists and is writable
    $has_dir = true;

    // Creating touch folder if not exists
    if(!is_dir(APPLICATION_PATH . '/public/touch/')){
      $has_dir = mkdir(APPLICATION_PATH . '/public/touch/');
    }
    // Creating homescreen folder if not exists
    if($has_dir && !is_dir(APPLICATION_PATH . '/public/touch/homescreen/')){
      $has_dir = mkdir(APPLICATION_PATH . '/public/touch/homescreen/');
    }

    if(!$has_dir ||  !file_exists(APPLICATION_PATH . '/public/admin') ||
        !is_writable(APPLICATION_PATH . '/public/admin') ) {
      $form->addError('The public/admin folder does not exist or is not ' .
                  'writable. Please create this folder and set full permissions ' .
                  'on it (chmod 0777).');
      return;
    }
    // Set base path
    $this->_basePath = realpath(APPLICATION_PATH . $path);

    if( !$this->getRequest()->isPost() ) {
      if(!$this->view->homeScreen()){
        $form->removeElement('original');
        $form->removeElement('preview');
        $form->removeElement('enable');
      }
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    if( $form->Filedata->getValue() !== null ) {
      $fileElement = $form->Filedata;
      $fileName = $fileElement->getFileName();
      $extension = ltrim(strrchr(basename($fileName), '.'), '.');
      $original = $this->_getPath() . DIRECTORY_SEPARATOR . 'original.' . $extension;
      $icon_57x57 = $this->_getPath() . DIRECTORY_SEPARATOR . '57x57.' . $extension;
      $icon_114x114 = $this->_getPath() . DIRECTORY_SEPARATOR . '114x114.' . $extension;
      $icon_144x144 = $this->_getPath() . DIRECTORY_SEPARATOR . '144x144.' . $extension;
      if(file_exists($original))
        unlink($original);

      if(file_exists($icon_57x57))
        unlink($icon_57x57);

      if(file_exists($icon_114x114))
        unlink($icon_114x114);

      if(file_exists($icon_144x144))
        unlink($icon_144x144);

      rename($fileName, $original);

      // Resize 57 x 57
      $image = Engine_Image::factory();
      $image->open($original);

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;
      $w = $h = $size;

      $image->open($original)
        ->resample($x, $y, $w, $h, 57, 57)
        ->write($icon_57x57)
        ->destroy();

      // Resize 114 x 114
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 114, 114)
        ->write($icon_114x114)
        ->destroy();

      // Resize 144 x 144
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 144, 144)
        ->write($icon_144x144)
        ->destroy();

      Engine_Api::_()->getDbTable('settings', 'core')->setSetting('touch.homescreen.extension', $extension);
      if(file_exists($original))
        $form->getElement('submit');

    }
    elseif($form->getValue('coordinates') && $extension = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('touch.homescreen.extension', false)){
      $original = $this->_getPath(). DIRECTORY_SEPARATOR . 'original.' . $extension;
      $icon_57x57 = $this->_getPath(). DIRECTORY_SEPARATOR . '57x57.' . $extension;
      $icon_114x114 = $this->_getPath(). DIRECTORY_SEPARATOR . '114x114.' . $extension;
      $icon_144x144 = $this->_getPath(). DIRECTORY_SEPARATOR . '144x144.' . $extension;

      if(file_exists($icon_57x57))
        unlink($icon_57x57);

      if(file_exists($icon_114x114))
        unlink($icon_114x114);

      if(file_exists($icon_144x144))
        unlink($icon_144x144);

      list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
      $x +=.1;
      $y +=.1;
      $w -=.1;
      $h -=.1;
      // Resize 57 x 57
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 57, 57)
        ->write($icon_57x57)
        ->destroy();

      // Resize 114 x 114
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 114, 114)
        ->write($icon_114x114)
        ->destroy();

      // Resize 144 x 144
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 144, 144)
        ->write($icon_144x144)
        ->destroy();
    }

    if($form->getValue('enable') !== null)
      Engine_Api::_()->getDbTable('settings', 'core')->setSetting('touch.homescreen.enabled', $form->getValue('enable'));
  }

  protected function _getPath($key = 'path')
  {
    return $this->_checkPath(urldecode($this->_getParam($key, '')), $this->_basePath);
  }

  protected function _getRelPath($path, $basePath = null)
  {
    if( null === $basePath ) $basePath = $this->_basePath;
    $path = realpath($path);
    $basePath = realpath($basePath);
    $relPath = trim(str_replace($basePath, '', $path), '/\\');
    return $relPath;
  }

  protected function _checkPath($path, $basePath)
  {
    // Sanitize
    //$path = preg_replace('/^[a-z0-9_.-]/', '', $path);
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    // Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);
    // Check if this is a parent of the base path
    if( $basePath != $path && strpos($basePath, $path) !== false ) {
      return $this->_helper->redirector->gotoRoute(array());
    }

    return $path;
  }

}
