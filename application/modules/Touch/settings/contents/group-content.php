<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: group-content.php 2011-04-26 11:18:13 mirlan $
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
    'title' => 'Profile Groups',
    'description' => 'Displays a member\'s groups on their profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'touch.group-profile-groups',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Groups',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Group Profile Info',
    'description' => 'Displays a group\'s info (creation date, member count, leader, officers, etc) on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'touch.group-profile-info',
  ),
  array(
    'title' => 'Group Profile Members',
    'description' => 'Displays a group\'s members on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'touch.group-profile-members',
    'isPaginated' => true,
  ),
  array(
    'title' => 'Group Profile Options',
    'description' => 'Displays a menu of actions (edit, report, join, invite, etc) that can be performed on a group on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'touch.group-profile-options',
  ),
  array(
    'title' => 'Group Profile Photo',
    'description' => 'Displays a group\'s photo on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'touch.group-profile-photo',
  ),
  array(
    'title' => 'Group Profile Photos',
    'description' => 'Displays a group\'s photos on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'touch.group-profile-photos',
    'isPaginated' => true,
  ),
  array(
    'title' => 'Group Profile Status',
    'description' => 'Displays a group\'s title on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'touch.group-profile-status',
  ),
  array(
    'title'=> 'Group Profile Events',
    'description' => 'Displays a group\'s events on its profile',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'touch.group-profile-events',
    'isPaginated' => true,
  )
) ?>