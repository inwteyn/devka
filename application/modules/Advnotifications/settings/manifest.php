<?php return array(
  'package' =>
    array(
      'type' => 'module',
      'name' => 'advnotifications',
      'version' => '4.8.9p2',
      'path' => 'application/modules/Advnotifications',
      'title' => 'Advanced Notifications',
      'description' => 'Advanced Notifications Plugin from Hire-Express LLC',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
      'meta' => array(
        'title' => 'Advanced Notifications',
        'description' => 'Advanced Notifications Plugin from Hire-Express LLC',
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
          'minVersion' => '4.2.0p1',
        )
      ),
      'actions' => array(
        'install',
        'upgrade',
        'refresh',
        'enable',
        'disable',
      ),
      'callback' => array(
        'path' => 'application/modules/Advnotifications/settings/install.php',
        'class' => 'Advnotifications_Installer',
      ),
      'directories' =>
        array(
          0 => 'application/modules/Advnotifications',
        ),
      'files' =>
        array(
          0 => 'application/languages/en/advnotifications.csv',
        ),
    ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Advnotifications_Plugin_Core'
    )
  )
); ?>