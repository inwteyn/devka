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
    'title' => 'Browse Musics',
    'description' => 'Displays musics, published in Pages and common Music sections.',
    'category' => 'Page Musics',
    'type' => 'widget',
    'name' => 'pagemusic.musics',
  ),
  array(
    'title' => 'Musics Browse Search',
    'description' => 'Displays a Musics Search Form.',
    'category' => 'Page Musics',
    'type' => 'widget',
    'name' => 'pagemusic.browse-search',
  ),

  array(
    'title' => 'Page Music Browse Menu',
    'description' => 'Displays a menu in All Music Browse page.',
    'category' => 'Page Musics',
    'type' => 'widget',
    'name' => 'pagemusic.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Profile Music',
    'description' => 'Displays a member\'s music on their profile, which are published in Pages and common Music sections.',
    'category' => 'Page Musics',
    'type' => 'widget',
    'name' => 'pagemusic.profile-musics',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Music',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),

  array(
    'title' => 'Popular Playlists',
    'description' => 'Displays a list of popular playlists, which are published in Pages and common Music sections.',
    'category' => 'Page Musics',
    'type' => 'widget',
    'name' => 'pagemusic.popular-playlists',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Playlists',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Recent Playlists',
    'description' => 'Displays a list of recent playlists, which are published in Pages and common Music sections.',
    'category' => 'Page Musics',
    'type' => 'widget',
    'name' => 'pagemusic.recent-playlists',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Playlists',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
);
