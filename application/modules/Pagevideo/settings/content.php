<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 11.02.12
 * Time: 11:29
 * To change this template use File | Settings | File Templates.
 */

return array(
  array(
    'title' => 'Browse Videos',
    'description' => 'Displays videos, published in Pages and common Videos sections.',
    'category' => 'Page Videos',
    'type' => 'widget',
    'name' => 'pagevideo.videos',
  ),
  array(
    'title' => 'Videos Browse Search',
    'description' => 'Displays a Videos Search Form.',
    'category' => 'Page Videos',
    'type' => 'widget',
    'name' => 'pagevideo.browse-search',
  ),

  array(
    'title' => 'Page Video Browse Menu',
    'description' => 'Displays a menu in All Videos Browse page.',
    'category' => 'Page Videos',
    'type' => 'widget',
    'name' => 'pagevideo.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Profile Videos',
    'description' => 'Displays a member\'s videos on their profile, which published in Pages and common Videos sections.',
    'category' => 'Page Videos',
    'type' => 'widget',
    'name' => 'pagevideo.profile-videos',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Videos',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),

  array(
    'title' => 'Popular Videos',
    'description' => 'Displays a list of most viewed videos, which published in Pages and common Videos sections.',
    'category' => 'Page Videos',
    'type' => 'widget',
    'name' => 'pagevideo.popular-videos',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Videos',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Recent Videos',
    'description' => 'Displays a list of recent videos, which published in Pages and common Videos sections.',
    'category' => 'Page Videos',
    'type' => 'widget',
    'name' => 'pagevideo.recent-videos',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Videos',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
);
