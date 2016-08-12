<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: user-content.php 2011-04-26 11:18:13 mirlan $
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
    'title' => 'User Photo',
    'description' => 'Displays the logged-in member\'s photo.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-home-photo',
  ),
  array(
    'title' => 'Online Users',
    'description' => 'Displays a list of online members.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-list-online',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => '%d Members Online',
    ),
  ),
  array(
    'title' => 'Popular Members',
    'description' => 'Displays the list of most popular members.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-list-popular',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Members',
    ),
  ),
  array(
    'title' => 'Recent Signups',
    'description' => 'Displays the list of most recent signups.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-list-signups',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Signups',
    ),
  ),
  array(
    'title' => 'Login or Signup',
    'description' => 'Displays a login form and a signup link for members that are not logged in.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-login-or-signup',
  ),
  array(
    'title' => 'Profile Photo',
    'description' => 'Displays a member\'s photo on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-profile-photo',
  ),
  array(
    'title' => 'User Profile Status',
    'description' => 'Displays a member\'s name and most recent status on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-profile-status',
  ),
	array(
    'title' => 'Profile Fields',
    'description' => 'Displays a member\'s profile field data on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-profile-fields',
    'defaultParams' => array(
      'title' => 'Info'
    ),
  ),
  array(
    'title' => 'Profile Friends',
    'description' => 'Displays a member\'s friends on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-profile-friends',
    'defaultParams' => array(
      'title' => 'Friends',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Profile Info',
    'description' => 'Displays a member\'s info (signup date, friend count, etc) on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-profile-info',
  ),
  array(
    'title' => 'Profile Options',
    'description' => 'Displays a list of actions that can be performed on a member on their profile (report, add as friend, etc).',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'touch.user-profile-options',
  ),
) ?>