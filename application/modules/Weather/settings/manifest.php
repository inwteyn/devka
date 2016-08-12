<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'weather',
    'version' => '4.1.5p7',
    'path' => 'application/modules/Weather',
    'title' => 'Weather Plugin',
    'description' => 'Weather Plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'callback' => array(
      'path' => 'application/modules/Weather/settings/install.php',
      'class' => 'Weather_Installer',
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
      0 => 'application/modules/Weather',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/weather.csv',
    ),
  ),

    //Routes------------------------------------------------------------------------------
    'routes' => array(
        'edit_location' => array(
            'route' => 'edit_location/:action',
            'defaults' => array(
                'module' => 'weather',
                'controller' => 'index',
                'action' => 'edit-location',
            ),
        ),
    )

); ?>