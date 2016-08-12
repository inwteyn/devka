<?php

return array(
  'package' => array(
    'type' => 'module',
    'name' => 'usernotes',
    'version' => '4.1.3p3',
    'path' => 'application/modules/Usernotes',
    'title' => 'User Notes',
    'description' => 'Profile User Notes',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => array(
      'title' => 'User Notes',
      'description' => 'Profile User Notes',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'callback' => array(
      'path' => 'application/modules/Usernotes/settings/install.php',
      'class' => 'Usernotes_Installer',
    ),
    'actions' => array(
       'preinstall',
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable'
     ),
    'directories' => array(
      'application/modules/Usernotes',
    ),
    'files' => array(
      'application/languages/en/usernotes.csv',
    ),
  ),
  // Content -------------------------------------------------------------------
  'content'=> array(
    'usernotes_profile_usernotes' => array(
      'type' => 'action',
      'title' => 'User Notes',
      'route' => array(
        'module' => 'usernotes',
        'controller' => 'widget',
        'action' => 'profile-usernotes',
      ),
    )
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'usernote'
  ),

  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Usernotes_Plugin_Menus',
    ),
  ),

  // Routes --------------------------------------------------------------------
  'routes' => array(
    'usernote_save' => array(
      'route' => 'usernotes/save',
      'defaults' => array(
        'module' => 'usernotes',
        'controller' => 'index',
        'action' => 'save'
      )
    ),

    'usernote_delete' => array(
      'route' => 'usernotes/delete',
      'defaults' => array(
        'module' => 'usernotes',
        'controller' => 'index',
        'action' => 'delete'
      )
    ),

    'usernote_index' => array(
      'route' => 'usernotes/:page',
      'defaults' => array(
        'module' => 'usernotes',
        'controller' => 'index',
        'action' => 'index'
      )
    ),

    'usernotes_admin_level' => array(
      'route' => 'admin/usernotes/level/:id',
      'defaults' => array(
        'module' => 'usernotes',
        'controller' => 'admin-level',
        'action' => 'index'
      )
    )
  )
);

?>