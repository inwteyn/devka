<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'autologin',
    'version' => '4.0.0p1',
    'path' => 'application/modules/Autologin',
    'title' => 'Auto login',
    'description' => 'Auto login in your demo site',
    'author' => 'Hire-Experts',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Autologin',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/autologin.csv',
    ),
  ),
   //Hooks -----------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutAdmin',
      'resource' => 'Autologin_Plugin_Core'
    )
   ),
  'routes' => array(
    'autologin_general' => array(
      'route' => 'autologin/*',
      'defaults' => array(
        'module' => 'autologin',
        'controller' => 'index',
        'action' => 'index'
      )
    )
  )
); ?>