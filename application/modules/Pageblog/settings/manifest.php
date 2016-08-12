<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  'package' => array(
    'type' => 'module',
    'name' => 'pageblog',
    'version' => '4.1.9',
    'path' => 'application/modules/Pageblog',
      'title' => 'Page Blog',
      'description' => 'Displays the page blog.',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',

    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'page',
        'minVersion' => '4.2.3',
      ),
    ),

    'directories' => array(
      'application/modules/Pageblog',
    ),
    'files' => array(
      'application/languages/en/pageblog.csv',
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Pageblog/settings/install.php',
      'class' => 'Pageblog_Installer',
    ),
  ),

  'hooks' => array(
    array(
      'event' => 'removePage',
      'resource' => 'Pageblog_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Pageblog_Plugin_Core'
    )
  ),

  'items' => array(
    'pageblog'
  ),

  'routes' => array(
    'page_blog' => array(
      'route' => 'page-blog/:action/*',
      'defaults' => array(
        'module' => 'pageblog',
        'controller' => 'index',
        'action' => 'index'
      )
    ),
    'page_blogs' => array(
      'route' => 'page-blog/blogs/:action/*',
      'defaults' => array(
        'module' => 'pageblog',
        'controller' => 'blogs',
        'action' => 'browse'
      ),
      'reqs' => array(
        'action' => '(browse|manage|delete)',
      ),
    ),
    'pageblog' => array(
      'route' => 'pageblog/:action/*',
      'defaults' => array(
        'module' => 'pageblog',
        'controller' => 'pageblog',
        'action' => 'index'
      )
    ),
  ),
);
