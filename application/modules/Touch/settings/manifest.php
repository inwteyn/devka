<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
    // Package -------------------------------------------------------------------
    'package' => array(
        'type' => 'module',
        'name' => 'touch',
        'version' => '4.2.3p4',
        'path' => 'application/modules/Touch',
        'repository' => 'hire-experts.com',
        'title' => 'Touch-Mobile',
        'description' => 'Touch-Mobile',
        'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
        'meta' =>
        array(
            'title' => 'Touch-Mobile',
            'description' => 'Touch-Mobile',
            'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
        ),
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
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
                'minVersion' => '4.2.0',
            ),
        ),
        'callback' => array(
            'path' => 'application/modules/Touch/settings/install.php',
            'class' => 'Touch_Installer',
        ),
        'directories' => array(
            'application/modules/Touch',
        ),
        'files' => array(
            'application/languages/en/touch.csv',
        ),
    ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserCreateAfter',
      'resource' => 'Touch_Plugin_User',
    ),
  ),

    // Items ---------------------------------------------------------------------
  'items' => array(
  ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'touch_dashboard' => array(
            'route' => '/touch-dashboard',
            'defaults' => array(
                'module' => 'touch',
                'controller' => 'index',
                'action' => 'index'
            ),
        ),
        // Public
        'getRateContainer' => array(
            'route' => 'rate/getratecontainer',
            'defaults' => array(
                'module' => 'rate',
                'controller' => 'index',
                'action' => 'getratecontainer',
            )
        ),

        'copy_touch_themes' => array(
            'routes' => 'touch/:action/*'
        ),
        'touch_mode_switch' => array(
            'route' => '/touch-mode-switch/:mode',
            'defaults' => array(
                'module' => 'touch',
                'controller' => 'index',
                'action' => 'touch-mode-switch',
                'mode' => 'standard',
            )
        ),

        'touch_messages_delete' => array(
            'route' => 'messages/touch-delete/:message_id',
            'defaults' => array(
                'module' => 'messages',
                'controller' => 'messages',
                'action' => 'delete',
                'message_id' => '',
            )
        ),

        'touch_recent_activity' => array(
            'route' => 'activity/notifications/:action/*',
            'defaults' => array(
                'module' => 'activity',
                'controller' => 'notifications',
                'action' => 'index'
            )
        ),
        'touch_classified_entry_view' => array(
            'route' => 'classifieds/:user_id/:classified_id/:slug/*',
            'defaults' => array(
                'module' => 'classified',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'classified_id' => '\d+'
            )
        ),

        'pagevideo_create' => array(
            'route' => 'browse-pages/:page_id/pagevideo/create/:video_id',
            'defaults' => array(
                'module' => 'pagevideo',
                'controller' => 'index',
                'action' => 'create'
            )
        ),
        'pagevideo_edit' => array(
            'route' => 'pagevideo/edit/:video_id',
            'defaults' => array(
                'module' => 'pagevideo',
                'controller' => 'index',
                'action' => 'edit'
            )
        ),
        'pagevideo_delete' => array(
            'route' => 'pagevideo/delete/:video_id',
            'defaults' => array(
                'module' => 'pagevideo',
                'controller' => 'index',
                'action' => 'delete'
            )
        ),
        'pagealbum_specific' => array(
            'route' => 'pagealbums/:action/:page_id/:pagealbum_id/*',
            'defaults' => array(
                'module' => 'pagealbum',
                'controller' => 'index',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(delete|edit|upload|view|order)',
            ),
        ),
        'upload_profile_photo' => array(
          'route' => 'touch/uploadprofilephoto',
          'defaults' => array(
            'module' => 'touch',
            'controller' =>'utility',
            'action' => 'uploadprofilephoto'
          )
        ),
        'refresh_captcha' => array(
          'route' => 'touch/refresh-captcha',
          'defaults' => array(
            'module' => 'touch',
            'controller' =>'utility',
            'action' => 'refresh-captcha'
          )
        )
    ),

    'wall_touch_composer' => array(
      'photo' => array(
        'script' => array('compose/photo.tpl', 'touch'),
        'plugin' => 'Wall_Plugin_Composer_Album',
        'auth' => array('album', 'create'),
        'module' => 'album',
        'type'=>'photo'
      ),
      'link' => array(
        'script' => array('compose/link.tpl', 'touch'),
        'plugin' => 'Wall_Plugin_Composer_Core',
        'auth' => array('core_link', 'create'),
        'module' => 'core',
        'type'=>'link'
      ),
      'video' => array(
        'script' => array('compose/video.tpl', 'touch'),
        'plugin' => 'Wall_Plugin_Composer_Video',
        'auth' => array('video', 'create'),
        'module' => 'video',
        'type'=>'video'
      ),
//      array(
//        'script' => array('compose/date.tpl', 'timeline'),
//        'composer' => true,
//        'plugin' => 'Timeline_Plugin_Composer_Date',
//        'module' => 'timeline',
//        'type' => 'date'
//      ),
      array(
        'script' => array('compose/checkin.tpl', 'touch'),
        'plugin' => 'Checkin_Plugin_Composer_Core',
        'module' => 'checkin',
        'type' => 'checkin',
        'composer' => TRUE
      ),
      array(
         'script' => array('compose/avp.tpl', 'touch'),
         'plugin' => 'Avp_Plugin_Composer',
   	    'module' => 'avp',
         'type' => 'avp'
       ),
    ),


    // end routes
);