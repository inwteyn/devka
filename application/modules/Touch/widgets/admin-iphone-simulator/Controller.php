<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 15.02.12
 * Time: 17:23
 * To change this template use File | Settings | File Templates.
 */
class Touch_Widget_AdminIphoneSimulatorController extends Engine_Content_Widget_Abstract
{
  private $defaults = array(
      'touch.ips.pos.x' => 60,
      'touch.ips.pos.y' => 150,
      'touch.ips.scale' => 1,
      'touch.ips.loc.last' => 'home',
      'touch.ips.showing' => true,
      'touch.ips.change.time' => 0
      );
  private $setting_pref ='touch.ips.';
  public function indexAction()
  {
    // IE doesn't deserve it :)))
    if(preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']))
      return $this->setNoRender();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if(!$viewer || $viewer->level_id != 1){
      return $this->setNoRender();
    }
    $this->view->setting_pref = $this->setting_pref;
    $this->view->settings = $this->getSettings();
  }
  private function getSettings(){
    $core_settings = Engine_Api::_()->getDbTable('settings', 'core');
    $settings = array();
    $this->view->update_settings = $update_settings = $_COOKIE['touch_ips_change_time'] && ($_COOKIE['touch_ips_change_time']) > ($core_settings->getSetting('touch.ips.change.time', 0));
    foreach($this->defaults as $key => $default){
      $c_key = str_replace('.', '_', $key);
      if($update_settings && isset($_COOKIE[$c_key])){
        $settings[$key] = $_COOKIE[$c_key];
        $core_settings->setSetting($key, $_COOKIE[$c_key]);
        continue;
      }
      $settings[$key] = $core_settings->getSetting($key, $default);
    }
    return $settings;
  }
}
