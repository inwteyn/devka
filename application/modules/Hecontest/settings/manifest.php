<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
return array(
  'package' =>
    array(
      'type' => 'module',
      'name' => 'hecontest',
      'version' => '4.5.2',
      'path' => 'application/modules/Hecontest',
      'title' => 'HE - Contest',
      'description' => 'Contest Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
      'callback' =>
        array(
          'path' => 'application/modules/Hecontest/settings/install.php',
          'class' => 'Hecontest_Installer',
        ),
      'dependencies' => array(
        array(
          'type' => 'module',
          'name' => 'hecore',
          'minVersion' => '4.2.1',
        ),
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
          0 => 'application/modules/Hecontest',
        ),
      'files' =>
        array(
          0 => 'application/languages/en/hecontest.csv',
        ),
    ),
  'items' => array(
    'hecontest',
    'hecontest_prize_photo',
    'hecontest_photo'
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Hecontest_Plugin_Core',
    ),
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'hecontest_general' => array(
      'route' => 'contest/:action/*',
      'defaults' => array(
        'module' => 'hecontest',
        'controller' => 'index',
        'action' => 'index',
        'id' => 0
      )
    ),
    'hecontest_general_view' => array(
      'route' => 'contest/contestview/:contest_id/*',
      'defaults' => array(
        'module' => 'hecontest',
        'controller' => 'index',
        'action' => 'contestview',
        'contest_id' => 0
      ),
      'reqs' => array(
        'action' => '(contestview)',
        'contest_id' => '\d+',
      )
    )
  )
); ?>
