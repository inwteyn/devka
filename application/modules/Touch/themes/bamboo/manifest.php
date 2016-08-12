<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'bamboo',
    'version' => '4.0.1',
    'revision' => '$Revision: 6973 $',
    'path' => 'application/modules/Touch/themes/bamboo',
    'repository' => 'socialengine.net',
    'title' => 'Bamboo Theme',
    'thumb' => 'bamboo.jpg',
    'author' => 'Webligo Developments',
    'changeLog' => array(
      '4.0.1' => array(
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Uses fixed relative URL support in Scaffold',
      ),
    ),
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'remove',
    ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array(
      'application/modules/Touch/themes/bamboo',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  )
) ?>