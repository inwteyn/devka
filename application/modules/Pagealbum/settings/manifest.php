<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array (
  'package' => array (
    'type' => 'module',
    'name' => 'pagealbum',
    'version' => '4.2.1',
    'path' => 'application/modules/Pagealbum',
      'title' => 'Page Album',
      'description' => 'Page Album',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'page',
        'minVersion' => '4.2.3',
      ),
    ),

    'callback' => array(
      'path' => 'application/modules/Pagealbum/settings/install.php',
      'class' => 'Pagealbum_Installer',
    ),

    'actions' => array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),

    'directories' => array (
      0 => 'application/modules/Pagealbum',
    ),

    'files' => array (
      0 => 'application/languages/en/pagealbum.csv',
    ),

  ),

  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'removePage',
      'resource' => 'Pagealbum_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Pagealbum_Plugin_Core'
    )
  ),

  'items' => array(
    'pagealbum',
    'pagealbumphoto'
  ),
  // Routes---------------------------------------------------------------------
  'routes' => array(
    'page_album' => array(
      'route' => 'page-album/:action/*',
      'defaults' => array(
        'module' => 'pagealbum',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'action' => '(upload|index|mine|manage-photo|delete|edit|view|view-photo|upload-photo|remove-photo|load-comments|edit-photo|save)',
      ),
    ),
    'page_albums' => array(
      'route' => 'page-album/albums/:action/*',
      'defaults' => array(
        'module' => 'pagealbum',
        'controller' => 'albums',
        'action' => 'browse'
      ),
      'reqs' => array(
        'action' => '(browse|manage|delete)',
      )
    ),
  ),
); ?>
