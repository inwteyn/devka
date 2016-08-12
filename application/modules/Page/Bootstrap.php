<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();
    $pTable = Engine_Api::_()->getDbTable('settings', 'core');
    $setting = $pTable->fetchRow($pTable->select()->where('name=?', 'page.short.url'));

    if ((!Engine_Api::_()->hasModuleBootstrap('apptouch') || !Engine_Api::_()->apptouch()->isApptouchMode()) && $setting && $setting->value == 1) {
      if (_ENGINE_R_BASE == '/') {
        $directory = '';
      } else {
        $directory = _ENGINE_R_BASE;
      }
      define('SDIRECTORY', $directory);
      $tmp3 = substr(str_replace($directory, '', $_SERVER['REQUEST_URI']), 1);
      $tmp3 = explode('/', $tmp3);
      $tmp3 = reset($tmp3);
      $tmp = explode('?', $tmp3);
      $reset = reset($tmp);


      if ($reset == 'page') {
        $tmp2 = substr(str_replace($directory, '', $_SERVER['REQUEST_URI']), 6);
        $tmp2 = reset(explode('/page/', $tmp2));
        $tmp2 = explode('?', $tmp2);
        $name = reset($tmp2);
        $baseUrl = rtrim(constant('_ENGINE_R_BASE'), '/') . '/' . $name;
        header("Location:" . rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']) . $baseUrl, 0);
        die;
      }
      $db = Engine_Db_Table::getDefaultAdapter();
      $reset = reset($tmp);
      $pageee = $db->fetchRow($db->select()->from('engine4_page_pages')->where('name = ?', $reset));

      if ($pageee) {

        $route = array(
          'page_view' => array(
            'route' => ':page_id/*',
            'defaults' => array(
              'module' => 'page',
              'controller' => 'index',
              'action' => 'view',
              'page_id' => 0
            )
          )
        );

        Zend_Registry::get('Zend_Controller_Front')->getRouter()->addConfig(new Zend_Config($route));

      } else {
        $route = array(
          'page_view' => array(
            'route' => 'page/:page_id/*',
            'defaults' => array(
              'module' => 'page',
              'controller' => 'index',
              'action' => 'view',
              'page_id' => 0
            )
          )
        );
        Zend_Registry::get('Zend_Controller_Front')->getRouter()->addConfig(new Zend_Config($route));
      }
    } else {
      $route = array(
        'page_view' => array(
          'route' => 'page/:page_id/*',
          'defaults' => array(
            'module' => 'page',
            'controller' => 'index',
            'action' => 'view',
            'page_id' => 0
          )
        )
      );
      Zend_Registry::get('Zend_Controller_Front')->getRouter()->addConfig(new Zend_Config($route));
    }
  }
}