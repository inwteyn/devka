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
    'name' => 'midnight',
    'version' => '4.0.0',
    'revision' => '$Revision: 6973 $',
    'path' => 'application/modules/Touch/themes/midnight',
    'repository' => 'socialengine.net',
    'title' => 'Midnight Theme',
    'thumb' => 'midnight.jpg',
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
      'application/modules/Touch/themes/midnight',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  ),
  'nophoto' => array(
    'user' => array(
      'thumb_icon' => 'application/modules/Touch/themes/midnight/images/nophoto_user_thumb_icon.png',
      'thumb_profile' => 'application/modules/Touch/themes/midnight/images/nophoto_user_thumb_profile.png',
    ),
    'group' => array(
      'thumb_normal' => 'application/modules/Touch/themes/midnight/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/modules/Touch/themes/midnight/images/nophoto_event_thumb_profile.jpg',
    ),
    'event' => array(
      'thumb_normal' => 'application/modules/Touch/themes/midnight/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/modules/Touch/themes/midnight/images/nophoto_event_thumb_profile.jpg',
    ),
  ),
) ?>