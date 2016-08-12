<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
return array (
  // Package -------------------------------------------------------------------
  'package' =>
  array (
    'type' => 'module',
    'name' => 'updates',
    'version' => '4.2.6p1',
    'path' => 'application/modules/Updates',
    'repository' => 'hire-experts.com',
    'title' => 'Newsletter Updates',
    'description' => 'Newsletter Updates Plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => array(
      'title' => 'Newsletter Updates',
      'description' => 'Newsletter Updates Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'actions' => array (
      'preinstall',
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
        'minVersion' => '4.1.7',
      ),
    ),
    'callback' => array(
      'path' => 'application/modules/Updates/settings/install.php',
      'class' => 'Updates_Installer',
    ),
    'directories' =>
    array (
      'application/modules/Updates',
    ),
    'files' => 
    array (
      'application/languages/en/updates.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'update',
    'campaign',
    'updates_template',
    ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Updates_Plugin_Core',
    ),
    array(
      'event' => 'onUserCreateAfter',
      'resource' => 'Updates_Plugin_Core',
    ),
    array(
      'event' => 'onUserUpdateAfter',
      'resource' => 'Updates_Plugin_Core',
    ),
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'upadtes' => array(
      'route' => 'updates/:controller/:action',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'settings',
        'action' => 'index'
      ),
    ),
    'updates_vieved' => array(
      'route' => 'updates/ajax/image/:type/:id',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'ajax',
        'action' => 'image',
        'type' => 'updates',
        'id' => 0, 
      )
    ),
    'updates_referred' => array(
      'route' => 'updates/ajax/referred/:type/:id/:url',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'ajax',
        'action' => 'referred',
        'type'=>'updates',
        'id' => 0,
        'url' => '', 
      )
    ),
    'updates_unsubscribe' => array(
      'route' => 'updates/ajax/unsubscribe/:email',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'ajax',
        'action' => 'unsubscribe',
        'email' => '', 
      )
    ),
    'updates_main' => array(
      'route' => 'admin/updates',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'admin-index',
        'action' => 'index',
        'page' => 1, 
      )
    ),
    'updates_stats' => array(
      'route' => 'admin/updates/stats/index/:page',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'admin-stats',
        'action' => 'index',
        'page' => 1, 
      )
    ),
    'campaign_stats' => array(
      'route' => 'admin/updates/stats/campaign/:page',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'admin-stats',
        'action' => 'campaign',
        'page' => 1,
      )
    ),

    'campaign_instant_sent' => array(
      'route' => 'admin/updates/campaign/index/:campaign_id',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'admin-campaign',
        'action' => 'index',
        'campaign_id'=>0,
      ),
    ),
    'campaign_edit' => array(
      'route' => 'admin/updates/campaign/edit/:campaign_id',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'admin-campaign',
        'action' => 'edit',
      ),
    ),
    'campaign_index' => array(
      'route' => 'admin/updates/campaign/campaigns/:page',
      'defaults' => array(
        'module' => 'updates',
        'controller' => 'admin-campaign',
        'action' => 'campaigns',
        'page' => 1
      ),
    )
  ),
);