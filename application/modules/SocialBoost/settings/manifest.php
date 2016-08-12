<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'social-boost',
    'version' => '4.5.0',
    'path' => 'application/modules/SocialBoost',
    'repository' => 'hire-experts.com',
    'title' => 'HE - Social Boost',
    'description' => 'Hire-Experts Social Boost plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' =>
      array (
        'title' => 'HE - Social Boost',
        'description' => 'Hire-Experts Social Boost Plugin',
        'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
      ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.8',
      ),
    ),
    'actions' =>
      array (
        0 => 'install',
        1 => 'upgrade',
        2 => 'refresh',
        3 => 'enable',
        4 => 'disable',
      ),
    'callback' => array(
      'path' => 'application/modules/SocialBoost/settings/install.php',
      'class' => 'SocialBoost_Installer',
    ),
    'directories' => 
    array (
      0 => 'application/modules/SocialBoost',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/social-boost.csv',
    ),
  ),

  // Hooks ---------------------------------------------------------------------

  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'SocialBoost_Plugin_Core',
    ),
  ),
); ?>