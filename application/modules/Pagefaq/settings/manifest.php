<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2011-09-28 15:18 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array (
  'package' => array (
    'type' => 'module',
    'name' => 'pagefaq',
    'version' => '4.1.2p5',
    'path' => 'application/modules/Pagefaq',
    'title' => 'Page FAQ',
    'description' => 'Page FAQ',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'callback' => array (
      'path' => 'application/modules/Pagefaq/settings/install.php',
      'class' => 'Pagefaq_Installer',
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
      0 => 'application/modules/Pagefaq',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/pagefaq.csv',
    ),
  ),
); ?>