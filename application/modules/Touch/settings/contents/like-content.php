<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: like-content.php 2011-04-26 11:18:13 mirlan $
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
    'title' => 'Likes',
    'description' => 'Displays things that current member liked, please put it on Member Profile page.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'touch.like-profile-likes',
		'defaultParams' => array(
      'title' => 'like_Likes',
      'titleCount' => true
    )
  ),

  array(
    'title' => 'Members Like This',
    'description' => 'Displays members who liked this(Profile, Page, Event...). Please put it on Member Profile, Event Profile, Group Profile pages',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'touch.like-box',
		'defaultParams' => array(
      'title' => 'like_Like Club',
      'titleCount' => true
    )
  )
);