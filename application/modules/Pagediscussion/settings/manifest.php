<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  'package' => array(
    'type' => 'module',
    'name' => 'pagediscussion',
    'version' => '4.1.9',
    'path' => 'application/modules/Pagediscussion',
    'title' => 'Page Discussion',
    'description' => 'Displays the Page Discussion.',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => array(
      'title' => 'Page Discussion',
      'description' => 'Displays the Page Discussion.',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),

    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'page',
        'minVersion' => '4.2.3',
      ),
    ),

    'directories' => array(
      'application/modules/Pagediscussion',
    ),
    'files' => array(
      'application/languages/en/pagediscussion.csv',
    ),
    'actions' => array(
     'install',
     'upgrade',
     'refresh',
     'enable',
     'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Pagediscussion/settings/install.php',
      'class' => 'Pagediscussion_Installer',
    ),
  ),
  'items' => array(
    'pagediscussion_pagetopic',
    'pagediscussion_pagepost'
  ),
  'hooks' => array(
    array(
      'event' => 'removePage',
      'resource' => 'Pagediscussion_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Pagediscussion_Plugin_Core'
    )
  ),
  'routes' => array(
    'page_discussion' => array(
      'route' => 'page-discussion/:action/*',
      'defaults' => array(
        'module' => 'pagediscussion',
        'controller' => 'index',
        'action' => 'index'
      )
    )
  ),
);