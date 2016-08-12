<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'highlights',
    'version' => '4.5.1',
    'path' => 'application/modules/Highlights',
    'title' => 'Highlights',
    'description' => 'Profile highlights',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' =>
    array (
      'title' => 'Highlight Profile Plugin',
      'description' => 'Hire-Experts Highlight Profile Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'callback' => array(
      'path' => 'application/modules/Highlights/settings/install.php',
      'class' => 'Highlights_Installer',
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
      0 => 'application/modules/Highlights',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/highlights.csv',
    ),
  ),
  'items' => array('highlight'),
  'routes' => array(




    // Public
    'highlight_general' => array(
      'route' => 'highlights/:controller/:action/*',
      'defaults' => array(
        'module' => 'highlights',
        'controller' => 'index',
        'action' => 'index',
      ),
    ),

      'highlight_extends' => array(
          'route' => 'highlights/:controller/:action/*',
          'defaults' => array(
              'module' => 'highlights',
              'controller' => 'credit',
              'action' => 'profile'
          ),
          'reqs' => array(
              'controller' => '\D+',
              'action' => '\D+',
          )
      ),

  )
); ?>