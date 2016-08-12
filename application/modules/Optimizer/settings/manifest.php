<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'optimizer',
    'version' => '4.0.1',
    'path' => 'application/modules/Optimizer',
    'title' => 'Optimizer',
    'description' => 'Optimizer',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
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
      0 => 'application/modules/Optimizer',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/optimizer.csv',
    ),
  ),

  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Optimizer_Api_Core',
    ),
  ),
); ?>