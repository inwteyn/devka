<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: video-content.php 2011-04-26 11:18:13 mirlan $
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
    'title' => 'Profile Videos',
    'description' => 'Displays a member\'s videos on their profile.',
    'category' => 'Videos',
    'type' => 'widget',
    'name' => 'touch.video-profile-videos',
    'isPaginated' => true,
    'requirements' => array(
      'subject' => 'user',
    ),
    'defaultParams' => array(
      'title' => 'Videos',
      'titleCount' => true,
    ),
  ),
) ?>