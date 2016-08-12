<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
return array(
  'package' => array(
    'type' => 'module',
    'name' => 'pagedocument',
    'version' => '4.1.7',
    'path' => 'application/modules/Pagedocument',
    'title' => 'Page Document',
    'description' => 'Displays the page document.',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => array(
      'title' => 'Page Document',
      'description' => 'Displays the page document.',
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
      'application/modules/Pagedocument',
    ),
    'files' => array(
      'application/languages/en/pagedocument.csv',
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Pagedocument/settings/install.php',
      'class' => 'Pagedocument_Installer',
    ),
  ),
   
  'hooks' => array(
    array(
      'event' => 'removePage',
      'resource' => 'Pagedocument_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Pagedocument_Plugin_Core'
    )
  ),
  
  'items' => array(
    'pagedocument',
    'pagedocumentcategories',
		'search',
		'rating'
  ),
  
  'routes' => array(
    'page_document' => array(
      'route' => 'page-document/:action/*',
      'defaults' => array(
        'module' => 'pagedocument',
        'controller' => 'index',
        'action' => 'index'
      )
    )
  ),
);