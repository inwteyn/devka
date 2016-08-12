<?php
/**
 * Created by Hire-Experts LLC.
 * Author: Ulan
 * Date: 17.04.12
 * Time: 18:18
 */

return array(
  'touch.user-profile-friends' => array(
    'module' => 'user',
    'title' => 'Friends',
    'items' => array(),
    'add-link' => array(
      'route' => 'user_general',
    ),
  ),
  'touch.page-profile-pages' => array(
    'module' => 'page',
    'title' => 'Pages',
    'items' => array(),
    'add-link' => array(
      'route' => 'page_create',
    )
  ),
  'touch.album-profile-albums' => array(
    'module' => 'album',
    'title' => 'Photos',
    'items' => array(),
    'add-link' => array(
      'route' => 'album_general',
      'action' => 'upload'
    )
  ),
  'touch.video-profile-videos' => array(
    'module' => 'video',
    'title' => 'Videos',
    'items' => array(),
    'add-link' => array(
      'route' => 'video_general',
      'action' => 'create'
    )
  ),
  'touch.event-profile-events' => array(
    'module' => 'touch',
    'title' => 'Events',
    'items' => array(),
    'add-link' => array(
      'route' => 'event_general',
      'action' => 'create'
    )
  ),
  'touch.group-profile-groups' => array(
    'module' => 'touch',
    'title' => 'Groups',
    'items' => array(),
    'add-link' => array(
      'route' => 'group_general',
      'action' => 'create'
    ),
  ),
  'touch.classified-profile-classifieds' => array(
    'module' => 'touch',
    'title' => 'Classifieds',
    'items' => array(),
    'add-link' => array(
      'route' => 'classified_general',
      'action' => 'create'
    ),
  ),
  'touch.like-profile-likes' => array(
    'module' => 'touch',
    'title' => 'Likes',
    'items' => array(),
  ),

  //Just supported
  'touch.blog-profile-blogs' => array(
    'module' => 'touch',
    'title' => 'Blogs',
    'render' => false,
    'add-link' => array(
      'route' => 'blog_general',
      'action' => 'create'
    ),
  ),
  'touch.profile-forum-posts' => array(
    'module' => 'touch',
    'title' => 'Forum Posts',
    'render' => false,
    'add-link' => array(
      'route' => 'forum_general',
    ),
  ),
//  'forum-profile-forum-topics' => array(
//    'module' => 'forum',
//    'title' => 'Forum Topics',
//    'render' => false,
//    'add-link' => array(
//      'route' => 'forum_general',
//    ),
//  ),
  'touch.profile-checkins' => array(
    'module' => 'touch',
    'title' => 'Check-Ins',
    'render' => false,
  ),
);