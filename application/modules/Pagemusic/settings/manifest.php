<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'pagemusic',
    'version' => '4.1.9p1',
    'path' => 'application/modules/Pagemusic',
      'title' => 'Page Music',
      'description' => 'Page Music',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',

    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'page',
        'minVersion' => '4.2.3',
      ),
    ),

    'callback' => array(
      'path' => 'application/modules/Pagemusic/settings/install.php',
      'class' => 'Pagemusic_Installer',
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
      0 => 'application/modules/Pagemusic',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/pagemusic.csv',
    ),
  ),
  'hooks' => array(
    array(
      'event' => 'removePage',
      'resource' => 'Pagemusic_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Pagemusic_Plugin_Core'
    ),
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Pagemusic_Plugin_Core',
    )
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'playlist',
    'song',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'page_music' => array(
      'route' => 'page-music/:action/*',
      'defaults' => array(
        'module' => 'pagemusic',
        'controller' => 'index',
        'action' => 'index'
      ),
    ),
    'page_musics' => array(
      'route' => 'page-music/musics/:action/*',
      'defaults' => array(
        'module' => 'pagemusic',
        'controller' => 'musics',
        'action' => 'browse'
      ),
      'reqs' => array(
        'action' => '(browse|manage|delete)'
      ),
    )
  ),
);
