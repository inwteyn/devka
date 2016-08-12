<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: event-content.php 2011-04-26 11:18:13 mirlan $
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
    'title' => 'Upcoming Events',
    'description' => 'Displays the logged-in member\'s upcoming events.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'touch.event-home-upcoming',
    'isPaginated' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title'
          )
        ),
        array(
          'Radio',
          'type',
          array(
            'label' => 'Show',
            'multiOptions' => array(
              '1' => 'Any upcoming events.',
              '2' => 'Current member\'s upcoming events.',
              '0' => 'Any upcoming events when member is logged out, that member\'s events when logged in.',
            ),
            'value' => '0',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Profile Events',
    'description' => 'Displays a member\'s events on their profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'touch.event-profile-events',
    'defaultParams' => array(
      'title' => 'Events',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Event Profile Info',
    'description' => 'Displays a event\'s info (creation date, member count, etc) on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'touch.event-profile-info',
  ),
  array(
    'title' => 'Event Profile Members',
    'description' => 'Displays a event\'s members on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'touch.event-profile-members',
  ),
  array(
    'title' => 'Event Profile Options',
    'description' => 'Displays a menu of actions (edit, report, join, invite, etc) that can be performed on a event on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'touch.event-profile-options',
  ),
  array(
    'title' => 'Event Profile Photo',
    'description' => 'Displays a event\'s photo on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'touch.event-profile-photo',
  ),
  array(
    'title' => 'Event Profile Photos',
    'description' => 'Displays a event\'s photos on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'touch.event-profile-photos',
  ),
  array(
    'title' => 'Event Profile RSVP',
    'description' => 'Displays options for RSVP\'ing to an event on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'touch.event-profile-rsvp',
  ),
  array(
    'title' => 'Event Profile Status',
    'description' => 'Displays a event\'s title on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'touch.event-profile-status',
  ),
) ?>