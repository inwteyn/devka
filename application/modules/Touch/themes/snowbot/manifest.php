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
    'name' => 'snowbot',
    'version' => '4.0.0',
    'revision' => '$Revision: 6973 $',
    'path' => 'application/modules/Touch/themes/snowbot',
    'repository' => 'socialengine.net',
    'title' => 'Snowbot Theme',
    'thumb' => 'snowbot.jpg',
    'author' => 'Webligo Developments',
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
      'application/modules/Touch/themes/snowbot',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  )
) ?>