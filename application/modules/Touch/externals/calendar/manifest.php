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
    'type' => 'external',
    'name' => 'calendar',
    'version' => '4.0.1',
    'revision' => '$Revision: 7593 $',
    'path' => 'externals/calendar',
    'repository' => 'socialengine.net',
    'title' => 'Calendar',
    'author' => 'Webligo Developments',
    'changeLog' => array(
      '4.0.1' => array(
        'manifest.php' => 'Incremented version',
        'styles.css' => 'Improved localization and RTL support',
      ),
    ),
    'directories' => array(
      'externals/calendar',
    )
  )
) ?>