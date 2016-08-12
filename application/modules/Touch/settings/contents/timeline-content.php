<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2012-02-01 15:13:20 ulan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  array(
    'title' => 'Timeline Header',
    'description' => 'Displays following widgets with a special timeline design: Profile Cover, Profile Photo, Profile Status, Profile Info, Profile Options',
    'category' => 'Timeline',
    'type' => 'widget',
    'name' => 'touch.timeline-header',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Timeline Feed',
    'description' => 'Displays Timeline\'s main content (feed actions) ',
    'category' => 'Timeline',
    'type' => 'widget',
    'name' => 'touch.timeline-content',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
) ?>