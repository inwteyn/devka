<?php return array(
  'package' =>
    array(
      'type' => 'module',
      'name' => 'avatarstyler',
      'version' => '4.8.9p4',
      'path' => 'application/modules/Avatarstyler',
      'repository' => 'hire-experts.com',
      'title' => 'HE - Avatar Styler',
      'description' => 'Hire-Experts Avatar Styler plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
      'meta' =>
        array(
          'title' => 'HE - Avatar Styler',
          'description' => 'Hire-Experts Avatar Styler',
          'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
        ),
      'dependencies' => array(
        array(
          'type' => 'module',
          'name' => 'core',
          'minVersion' => '4.1.8',
        ),
        array(
          'type' => 'module',
          'name' => 'hecore',
          'minVersion' => '4.2.2',
        ),
      ),
      'actions' =>
        array(
          0 => 'install',
          1 => 'upgrade',
          2 => 'refresh',
          3 => 'enable',
          4 => 'disable',
        ),
      'callback' => array(
        'path' => 'application/modules/Avatarstyler/settings/install.php',
        'class' => 'Avatarstyler_Installer',
      ),
      'directories' =>
        array(
          0 => 'application/modules/Avatarstyler',
        ),
      'files' =>
        array(
          0 => 'application/languages/en/avatarstyler.csv',
        ),
    ),

  'routes' => array(
    // User - General
    'avatarstyler' => array(
      'route' => 'avatarstyler/:controller/:action/',
      'defaults' => array(
        'module' => 'avatarstyler',
        'controller' => 'index',
        'action' => 'index'
      )
    ),
      'updateavatar' =>array(
          'route' => 'avatarstyler/index/updateavatar',
          'defaults'=>array(
              'module' => 'avatarstyler',
              'controller' => 'index',
              'action'=>'update-avatar'
          )
      ),
      'update' =>array(
          'route' => 'avatarstyler/index/update',
          'defaults'=>array(
              'module' => 'avatarstyler',
              'controller' => 'index',
              'action'=>'update'
          )
      )

  ),


); ?>