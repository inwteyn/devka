<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'apptouch-cometchat',
    'version' => '4.8.9p1',
    'path' => 'application/modules/ApptouchCometchat',
    'title' => 'Cometchat for New Touch-Mobile',
    'description' => '',
    'author' => 'Hire-Experts',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'dependencies' => array(
        array(
            'type' => 'module',
            'name' => 'apptouch',
            'minVersion' => '4.3.0p4',
        ),
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
      'application/modules/ApptouchCometchat',
      'images'

    ),
    'files' => 
    array (
      'application/languages/en/apptouch-cometchat.csv',
      'lock.png',
      'icon.png',
    ),
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
       // Public
       'apptouch_cometchat_general' => array(
           'route' => 'chat/:action/*',
           'defaults' => array(
               'module' => 'apptouchcometchat',
               'controller' => 'index',
               'action' => 'index',
           ),
       ),
   ),
); ?>