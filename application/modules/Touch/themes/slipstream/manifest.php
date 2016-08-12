<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'slipstream',
    'version' => NULL,
    'revision' => '$Revision: 7306 $',
    'path' => 'application/modules/Touch/themes/slipstream',
    'repository' => 'socialengine.net',
    'title' => 'Slipstream',
    'thumb' => 'slipstream.jpg',
    'author' => 'Webligo Developments',
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
      0 => 'application/modules/Touch/themes/default',
    ),
    'description' => '',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
  'nophoto' => 
  array (
    'user' => 
    array (
      'thumb_icon' => 'application/modules/Touch/themes/slipstream/images/nophoto_user_thumb_icon.png',
      'thumb_profile' => 'application/modules/Touch/themes/slipstream/images/nophoto_user_thumb_profile.png',
    ),
    'group' => 
    array (
      'thumb_normal' => 'application/modules/Touch/themes/slipstream/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/modules/Touch/themes/slipstream/images/nophoto_event_thumb_profile.jpg',
    ),
    'event' => 
    array (
      'thumb_normal' => 'application/modules/Touch/themes/slipstream/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/modules/Touch/themes/slipstream/images/nophoto_event_thumb_profile.jpg',
    ),
  ),
); ?>