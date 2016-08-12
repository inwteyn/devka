<?php

return array(
  array(
    'title' => 'Browse Events',
    'description' => 'Displays events, published in Pages and common Events sections.',
    'category' => 'Page Events',
    'type' => 'widget',
    'name' => 'pageevent.events',
    'isPaginated' => true,
  ),

  array(
    'title' => 'Profile Events',
    'description' => 'Displays a member\'s events on their profile, which are published in Pages and common Events sections.',
    'category' => 'Page Events',
    'type' => 'widget',
    'name' => 'pageevent.profile-events',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Events',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),

  array(
    'title' => 'Events Browse Search',
    'description' => 'Displays an Events Search Form.',
    'category' => 'Page Events',
    'type' => 'widget',
    'name' => 'pageevent.browse-search',
  ),

  array(
    'title' => 'Event Browse Menu',
    'description' => 'Displays a menu in All Events Browse page.',
    'category' => 'Page Events',
    'type' => 'widget',
    'name' => 'pageevent.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Upcoming Events',
    'description' => 'Displays the logged-in member\'s upcoming events, which are published in Pages and common Events sections.',
    'category' => 'Page Events',
    'type' => 'widget',
    'name' => 'pageevent.upcoming',
    'isPaginated' => true,
    'requirements' => array(
      'viewer',
      'no-subject',
    ),
  ),

  array(
    'title' => 'Popular Events',
    'description' => 'Displays popular events, which are published in Pages and common Events sections.',
    'category' => 'Page Events',
    'type' => 'widget',
    'name' => 'pageevent.popular-events',
    'isPaginated' => true,
    'requirements' => array(
      'viewer',
      'no-subject',
    ),
  ),

  array(
    'title' => 'Recent Events',
    'description' => 'Displays recent events, which are published in Pages and common Events sections.',
    'category' => 'Page Events',
    'type' => 'widget',
    'name' => 'pageevent.recent-events',
    'isPaginated' => true,
    'requirements' => array(
      'viewer',
      'no-subject',
    ),
  ),
);
