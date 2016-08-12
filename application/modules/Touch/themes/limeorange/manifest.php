<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'limeorange',
    'version' => NULL,
    'revision' => '$Revision: 6973 $',
    'path' => 'application/modules/Touch/themes/limeorange',
    'repository' => 'socialengine.net',
    'title' => 'Limeorange for Touch',
    'thumb' => 'limeorange.jpg',
    'author' => 'Hire-Experts LLC',
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Touch/themes/snowbot',
    ),
    'description' => '',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>