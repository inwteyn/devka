<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'pageinstagram',
    'version' => '4.8.9p1',
    'path' => 'application/modules/Pageinstagram',
    'title' => 'Page Instagram',
    'description' => 'Hire-Experts Page Instagram Plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',

      'callback' => array(
          'path' => 'application/modules/Pageinstagram/settings/install.php',
          'class' => 'Pageinstagram_Installer',
      ),


      'dependencies' => array(
      array(
          'type' => 'module',
          'name' => 'hecore',
          'minVersion' => '4.2.1',
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
      0 => 'application/modules/Pageinstagram',
      1 => 'application/libraries/Experts/Service/Instagram',
    ),

  'files' =>
    array (
      0 => 'application/languages/en/pageinstagram.csv',
    ),

  ),

    'items' => array(
        'instagrams'
    ),


  'routes' => array(

    'instagram' => array(
        'route' => 'page-instagram/*',
        'defaults' => array(
            'module' => 'pageinstagram',
            'controller' => 'index',
            'action' => 'index'
        ),
    ),

    'page-instagram-more-photo-for-user' => array(
        'route' => 'page-instagram-more-photo-for-user/*',
        'defaults' => array(
            'module' => 'pageinstagram',
            'controller' => 'index',
            'action' => 'more'
        ),
    ),

    'instagram_save' => array(
        'route' => 'page-instagram-save/*',
        'defaults' => array(
            'module' => 'pageinstagram',
            'controller' => 'index',
            'action' => 'save'
        ),
    ),


    'instagram_edit' => array(
        'route' => 'page-instagram-edit/*',
        'defaults' => array(
            'module' => 'pageinstagram',
            'controller' => 'index',
            'action' => 'edit'
        ),
    ),

    'instagram_delete' => array(
        'route' => 'page-instagram-delete/*',
        'defaults' => array(
            'module' => 'pageinstagram',
            'controller' => 'index',
            'action' => 'delete'
        ),
    ),
  )

); ?>