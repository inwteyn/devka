<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'hetools',
    'version' => '4.0.0',
    'path' => 'application/modules/Hetools',
    'title' => 'Hetools',
    'description' => 'Hetools',
    'author' => 'Michael',
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
      0 => 'application/modules/Hetools',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/hetools.csv',
    ),
  ),
); ?>