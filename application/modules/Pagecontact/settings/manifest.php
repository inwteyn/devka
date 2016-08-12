<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2011-07-13 16:01 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array (
  'package' => array (
    'type' => 'module',
    'name' => 'pagecontact',
    'version' => '4.1.3p2',
    'path' => 'application/modules/Pagecontact',
    'title' => 'Page Contact',
    'description' => 'Page Contact',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'callback' => array(
          'path' => 'application/modules/Pagecontact/settings/install.php',
          'class' => 'Pagecontact_Installer',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'page',
        'minVersion' => '4.1.9',
      ),
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
      0 => 'application/modules/Pagecontact',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/pagecontact.csv',
    ),
  )
);
?>