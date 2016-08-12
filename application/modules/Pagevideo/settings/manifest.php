<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'pagevideo',
    'version' => '4.2.1p1',
    'path' => 'application/modules/Pagevideo',
      'title' => 'Page Video',
      'description' => 'Page Video Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',

    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'page',
        'minVersion' => '4.2.3',
      ),
    ),

    'callback' =>
    array (
      'path' => 'application/modules/Pagevideo/settings/install.php',
      'class' => 'Pagevideo_Installer',
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
      0 => 'application/modules/Pagevideo',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/pagevideo.csv',
    ),
  ),

  'hooks' => array(
    array(
      'event' => 'removePage',
      'resource' => 'Pagevideo_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Pagevideo_Plugin_Core'
    )
  ),

  'items' => array(
    'pagevideo'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'page_video' => array(
      'route' => 'page-video/:action/*',
      'defaults' => array(
        'module' => 'pagevideo',
        'controller' => 'index',
        'action' => 'index'
      )
    ),
    'page_videos' => array(
      'route' => 'page-video/videos/:action/*',
      'defaults' => array(
        'module' => 'pagevideo',
        'controller' => 'videos',
        'action' => 'browse',
      ),
      'reqs' => array(
        'action' => '(browse|manage|delete)'
      ),
    ),
  ),

); ?>
