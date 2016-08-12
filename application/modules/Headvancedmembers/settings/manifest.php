<?php return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'headvancedmembers',
    'version' => '4.8.10',
    'path' => 'application/modules/Headvancedmembers',
    'title' => 'Advanced Members',
    'description' => 'Advanced Members',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'callback' => array(
      'path' => 'application/modules/Headvancedmembers/settings/install.php',
      'class' => 'Headvancedmembers_Installer',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.8',
      )
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'hecore',
        'minVersion' => '4.2.3',
      )
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
      0 => 'application/modules/Headvancedmembers',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/headvancedmembers.csv',
    ),
  ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        // Setting my location
        'headvancedmembers_settings' => array(
            'route' => 'headvancedmembers/settings/mylocation',
            'defaults' => array(
                'module' => 'headvancedmembers',
                'controller' => 'index',
                'action' => 'mylocation',
            ),

        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'markers',
    ),
); ?>