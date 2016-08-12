<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 14.12.11
 * Time: 17:57
 * To change this template use File | Settings | File Templates.
 */
 
class Touch_View_Helper_TouchCacheSettings extends Zend_View_Helper_Abstract{

  public function touchCacheSettings($is_json){
    $cache = Engine_Api::_()->getApi('settings', 'core');
    $pref = 'touch.admin.cache.';
    $settings = array();
    $enable =  $cache->getSetting($pref.'enable');
    $min_lifetime =  $cache->getSetting($pref.'min_lifetime');
    $max_lifetime =  $cache->getSetting($pref.'max_lifetime');
    $cache_feature =  $cache->getSetting($pref.'type');
    $settings['enabled'] = $enable;
    $settings['min_lifetime'] = $min_lifetime;
    $settings['max_lifetime'] = $max_lifetime;
    $settings['caching_feature'] = $cache_feature;
    if($is_json=='json' || $is_json=='json')
      $settings = Zend_Json::encode($settings);
    return $settings;
  }
}
