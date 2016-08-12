<?php

return array(
  array(
    'title' => 'Browse Blogs',
    'description' => 'Displays blogs, published in Pages and common Blog sections.',
    'category' => 'Page Blogs',
    'type' => 'widget',
    'name' => 'pageblog.blogs',
  ),
  array(
    'title' => 'Blogs Browse Search',
    'description' => 'Displays a Blogs Search Form',
    'category' => 'Page Blogs',
    'type' => 'widget',
    'name' => 'pageblog.browse-search',
  ),

  array(
    'title' => 'Blog Browse Menu',
    'description' => 'Displays a menu in All Blogs Browse page.',
    'category' => 'Page Blogs',
    'type' => 'widget',
    'name' => 'pageblog.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Profile Blogs',
    'description' => 'Displays a member\'s blogs on their profile, which are published in Pages and common Blog sections.',
    'category' => 'Page Blogs',
    'type' => 'widget',
    'name' => 'pageblog.profile-blogs',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Blogs',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),

  array(
    'title' => 'Popular Blog Entries',
    'description' => 'Displays a list of most viewed blog entries, which are published in Pages and common Blog sections.',
    'category' => 'Page Blogs',
    'type' => 'widget',
    'name' => 'pageblog.popular-blogs',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Blog Entries',
    ),
  ),

  array(
    'title' => 'Recent Blog Entries',
    'description' => 'Displays a list of recent blog entries, which are published in Pages and common Blog sections.',
    'category' => 'Page Blogs',
    'type' => 'widget',
    'name' => 'pageblog.recent-blogs',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Blog Entries',
    ),
  ),
);
