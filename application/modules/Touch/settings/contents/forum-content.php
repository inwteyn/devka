<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 13.10.11
 * Time: 15:56
 * To change this template use File | Settings | File Templates.
 */
 return array(
  array(
    'title' => 'Profile Forum Posts',
    'description' => 'Displays a member\'s forum posts on their profile.',
    'category' => 'Forum',
    'type' => 'widget',
    'name' => 'touch.profile-forum-posts',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Forum Posts',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    )
  )
);
