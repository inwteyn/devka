<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Installer extends Engine_Package_Installer_Module
{
  public function onPreInstall()
  {
    parent::onPreInstall();

    $db = $this->getDb();
    $translate = Zend_Registry::get('Zend_Translate');

    $select = $db->select()
      ->from('engine4_core_modules')
      ->where('name = ?', 'hecore')
      ->where('enabled = ?', 1);

    $hecore = $db->fetchRow($select);

    if (!$hecore) {
      $error_message = $translate->_('Error! This plugin requires Hire-Experts Core module. It is free module and can be downloaded from Hire-Experts.com');
      return $this->_error($error_message);
    }

    if (version_compare($hecore['version'], '4.2.0p1') < 0) {
      $error_message = $translate->_('This plugin requires Hire-Experts Core Module. We found that you has old version of Core module, please download latest version of Hire-Experts Core Module and install. Note: Core module is free.');
      return $this->_error($error_message);
    }

    $select = $db->select()
      ->from('engine4_core_modules')
      ->where('name = ?', 'inviter')
      ->where('enabled = ?', 1);

    $inviter = $db->fetchRow($select);

    if($inviter) {
      if (version_compare($inviter['version'], '4.1.8') < 0) {
        $error_message = $translate->_('You should first update your Inviter module.');
        return $this->_error($error_message);
      }
    }

    if (!$this->checkModule('like')) {
      return $this->_error('You should first install Like Module.');
    }

    $operation = $this->_databaseOperationType;
    $module_name = 'pages';

    $select = $db->select()
      ->from('engine4_hecore_modules')
      ->where('name = ?', $module_name);

    $module = $db->fetchRow($select);

    if ($module && isset($module['installed']) && $module['installed']
      && isset($module['version']) && $module['version'] == $this->_targetVersion
      && isset($module['modified_stamp']) && ($module['modified_stamp'] + 1000) > time()
    ) {
      return;
    }

    if ($operation == 'install') {

      if ($module && $module['installed']) {
        return;
      }

      $url_params = array(
        'module' => 'hecore',
        'controller' => 'module',
        'action' => 'license',
        'name' => $module_name,
        'version' => $this->_targetVersion,
        'format' => 'smoothbox'
      );

      $route = Zend_Controller_Front::getInstance()->getRouter();
      $register_url = $route->assemble($url_params, 'default', true);
      $register_url = str_replace('/install', '', $register_url);

      $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to install this module - <a class="smoothbox" href="%s">Click Here</a>');
      $error_message = sprintf($error_message, $register_url);

      return $this->_error($error_message);
    }
    else { //$operation = upgrade|refresh

      $url_params = array(
        'module' => 'hecore',
        'controller' => 'module',
        'action' => 'upgrade',
        'name' => $module_name,
        'version' => $this->_currentVersion,
        'target_version' => $this->_targetVersion,
        'operation' => $operation,
        'format' => 'smoothbox'
      );

      $route = Zend_Controller_Front::getInstance()->getRouter();
      $register_url = $route->assemble($url_params, 'default', true);
      $register_url = str_replace('/install', '', $register_url);

      $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to install this module - <a class="smoothbox" href="%s">Click Here</a>');
      $error_message = sprintf($error_message, $register_url);

      return $this->_error($error_message);
    }
  }

  function onInstall()
  {
    parent::onInstall();

    $db = $this->getDb();
    $db->beginTransaction();

    try{

      $select = new Zend_Db_Select($db);

      // profile page
      $select
        ->from('engine4_core_pages')
        ->where('name = ?', 'user_profile_index')
        ->limit(1);

      $page_id = $select->query()->fetchObject()->page_id;

      // page.profile-pages

      // Check if it's already been placed
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', 'page.profile-pages')
      ;
      $info = $select->query()->fetch();

      if( empty($info) ) {

        // container_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $page_id)
          ->where('type = ?', 'container')
          ->limit(1);
        $container_id = $select->query()->fetchObject()->content_id;

        // middle_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
          ->from('engine4_core_content')
          ->where('parent_content_id = ?', $container_id)
          ->where('type = ?', 'container')
          ->where('name = ?', 'middle')
          ->limit(1);
        $middle_id = $select->query()->fetchObject()->content_id;

        // tab_id (tab container) may not always be there
        $select
          ->reset('where')
          ->where('type = ?', 'widget')
          ->where('name = ?', 'core.container-tabs')
          ->where('page_id = ?', $page_id)
          ->limit(1);
        $tab_id = $select->query()->fetchObject();

        if( $tab_id && @$tab_id->content_id ) {
          $tab_id = $tab_id->content_id;
        } else {
          $tab_id = null;
        }

        // tab on profile
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type'    => 'widget',
          'name'    => 'page.profile-pages',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order'   => 131,
          'params'  => '{"title":"Pages","titleCount":true}',
        ));
      }

      $db->commit();
    }
    catch (Exception $e){
      $db->rollBack();
      throw $e;
    }

    // rate integration
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'rate')
      ->where('version >= ?', '4.0.1')
      ->limit(1);

    $rate_module = $select->query()->fetchObject();

    if ($rate_module && $rate_module->name) {
      $db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`) "
        . "VALUES ('rate', 'rate.page-review', 16, '{\"title\":\"RATE_REVIEW_TABITEM\", \"titleCount\":true}');");

      $db->query("INSERT IGNORE INTO `engine4_rate_types` (`category_id`, `label`, `order`) "
        . "SELECT `option_id` AS `category_id`, 'Rate' AS `label`, 1 AS `order` "
        . "FROM `engine4_page_fields_options` "
        . "WHERE `field_id` = 1;");
    }

    // weather integration
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'weather')
      ->limit(1);

    $weather_module = $select->query()->fetchObject();

    if ($weather_module && $weather_module->name) {
      $db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`) VALUES "
        . "('weather', 'weather.weather', 21, '{\"title\":\"Weather\", \"titleCount\":false}');");
    }
  }

  protected function checkModule($module)
  {
    $db = $this->getDb();

    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_modules')
      ->where('name = ?', $module)
      ->where('enabled = 1')
      ->limit(1);

    $info = $select->query()->fetch();

    return (!empty($info));
  }
}