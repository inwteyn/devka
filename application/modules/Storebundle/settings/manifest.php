<?php return array(
  'package' =>
    array(
      'type' => 'module',
      'name' => 'storebundle',
      'version' => '4.8.9p1',
      'path' => 'application/modules/Storebundle',
      'title' => 'Store Bundle Products',
      'description' => 'Store Bundle Products Plugin from Hire-Express LLC',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
      'meta' => array(
        'title' => 'Store Bundle Products',
        'description' => 'Store Bundle Products Plugin from Hire-Express LLC',
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
          'minVersion' => '4.2.4p1',
        ),
        array(
          'type' => 'module',
          'name' => 'store',
          'minVersion' => '4.3.2',
        ),
      ),
      'callback' => array(
        'path' => 'application/modules/Storebundle/settings/install.php',
        'class' => 'Storebundle_Installer',
      ),
      'actions' =>
        array(
          0 => 'install',
          1 => 'upgrade',
          2 => 'refresh',
          3 => 'enable',
          4 => 'disable',
        ),
      'directories' =>
        array(
          0 => 'application/modules/Storebundle',
        ),
      'files' =>
        array(
          0 => 'application/languages/en/storebundle.csv',
        ),
    ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'storebundle'
  ),
  'hooks' => array(
    array(
      'event' => 'onItemDeleteAfter',
      'resource' => 'Storebundle_Plugin_Core',
    ),
  ),
); ?>