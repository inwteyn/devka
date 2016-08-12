<?php

return array(
  array(
    'title' => 'Browse Albums',
    'description' => 'Displays albums, published in Pages and common Albums sections.',
    'category' => 'Page Albums',
    'type' => 'widget',
    'name' => 'pagealbum.albums',
  ),
  array(
    'title' => 'Albums Browse Search',
    'description' => 'Display an Albums Search Form.',
    'category' => 'Page Albums',
    'type' => 'widget',
    'name' => 'pagealbum.browse-search',
  ),

  array(
    'title' => 'Albums Browse Menu',
    'description' => 'Displays a menu in All Albums Browse page.',
    'category' => 'Page Albums',
    'type' => 'widget',
    'name' => 'pagealbum.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Profile Albums',
    'description' => 'Displays a member\'s albums on their profile, which are published in Pages and common Albums sections.',
    'category' => 'Page Albums',
    'type' => 'widget',
    'name' => 'pagealbum.profile-albums',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Albums',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),

  array(
    'title' => 'Popular Albums',
    'description' => 'Display a list of the most popular albums, which are published in Pages and common Albums sections.',
    'category' => 'Page Albums',
    'type' => 'widget',
    'name' => 'pagealbum.popular-albums',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Albums',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Recent Albums',
    'description' => 'Display a list of the most recent created albums, which are published in Pages and common Albums sections',
    'category' => 'Page Albums',
    'type' => 'widget',
    'name' => 'pagealbum.recent-albums',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Albums',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
);