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
     'title' => 'Profile Music',
     'description' => 'Displays a member\'s music on their profile.',
     'category' => 'Music',
     'type' => 'widget',
     'name' => 'touch.profile-music',
     'isPaginated' => true,
     'defaultParams' => array(
       'title' => 'Music',
       'titleCount' => true,
     ),
     'requirements' => array(
       'subject' => 'user',
     ),
   )
);

