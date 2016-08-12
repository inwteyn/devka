<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Register
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2012-12-04 16:05 teajay $
 * @author     TJ
 */

/**
 * @category   Application_Extensions
 * @package    Register
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array (
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'register',
    'version' => '4.2.1',
    'path' => 'application/modules/Register',
    'title' => 'Register',
    'description' => 'Register Plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => array(
      'title' => 'Register',
      'description' => 'Register Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'callback' => array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'directories' => array(
      'application/modules/Register',
    ),
    'files' => array(
      'application/languages/en/register.csv',
    ),
  ),

  'routes' => array(
    // Public
    'register_url' => array(
      'route' => 'register/:action/*',
      'defaults' => array(
        'module' => 'register',
        'controller' => 'index',
        'action' => 'index'
      )
    ),
  ),
  
); ?>