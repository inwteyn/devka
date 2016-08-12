<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  'package' => array(
    'type' => 'module',
    'name' => 'pageevent',
    'version' => '4.2.3',
    'path' => 'application/modules/Pageevent',
    'title' => 'Page Event',
    'description' => 'Displays the Page Event.',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',

    'directories' => array(
      'application/modules/Pageevent',
    ),

    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'page',
        'minVersion' => '4.2.3',
      ),
    ),

    'files' => array(
      'application/languages/en/pageevent.csv',
    ),
    'actions' => array(
     'install',
     'upgrade',
     'refresh',
     'enable',
     'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Pageevent/settings/install.php',
      'class' => 'Pageevent_Installer',
    ),
  ),
  'items' => array(
    'pageevent'
  ),
 /* 'wall_composer' => array(
    array(
      'script' => array('_composePageevent.tpl', 'pageevent'),
      'plugin' => 'Pageevent_Plugin_Composer',
      'auth' => array('event', 'create'),
      'subjects' => array('user','page'),
      'resource' => 'Pageevent_Model_Pageevent',
      'module' => 'pageevent',
      'type' => 'pageevent',
      'can_disable' => true,
      'composer' => true
    )
  ),*/
  'hooks' => array(
    array(
      'event' => 'removePage',
      'resource' => 'Pageevent_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Pageevent_Plugin_Core'
    ),
    array(
      'event' => 'page_onPageEditPrivacy',
      'resource' => 'Pageevent_Plugin_Core'
    )
  ),
  'routes' => array(

    'page_event' => array(
      'route' => 'page-event/:action/*',
      'defaults' => array(
        'module' => 'pageevent',
        'controller' => 'index',
        'action' => 'index',
      )
    ),

    'pageevent_manage' => array(
      'route' => 'page-event/events/manage/*',
      'defaults' => array(
        'module' => 'pageevent',
        'controller' => 'events',
        'action' => 'manage',
        'filter' => 'my',
      ),
    ),

    'pageevent_upcoming' => array(
      'route' => 'page-event/events/upcoming/*',
      'defaults' => array(
        'module' => 'pageevent',
        'controller' => 'events',
        'action' => 'browse',
        'filter' => 'future',
      ),
    ),

    'pageevent_past' => array(
      'route' => 'page-event/events/past/*',
      'defaults' => array(
        'module' => 'pageevent',
        'controller' => 'events',
        'action' => 'browse',
        'filter' => 'past',
      ),
    ),

    'page_events' => array(
      'route' => 'page-events/:action/*',
      'defaults' => array(
        'module' => 'pageevent',
        'controller' => 'events',
      ),
      'reqs' => array(
        'action' => '(delete|leave)'
      ),
    ),

  ),
);
