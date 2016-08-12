<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
    array(
        'title' => 'Timeline Header',
        'description' => 'Displays following widgets with a special timeline design: Profile Cover, Profile Photo, Profile Status, Profile Info, Profile Options. ' .
            'This widget can be used ONLY on TIMELINE profile page',
        'category' => 'Timeline',
        'type' => 'widget',
        'name' => 'timeline.header',
        'requirements' => array(
            'subject' => 'user',
        ),
    ),

    array(
        'title' => 'Timeline Feed',
        'description' => 'Displays Timeline\'s main content (feed actions). ' .
            'This widget can be used ONLY on TIMELINE profile page',
        'category' => 'Timeline',
        'type' => 'widget',
        'name' => 'timeline.content',
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(

        'title' => 'Profile Cover Widget',
        'description' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
        'category' => 'Timeline',
        'type' => 'widget',
        'name' => 'timeline.new-cover',
        'special' => 1,
        'defaultParams' => array(
            'max' => 6
        ),
        'canHaveChildren' => true,
        'childAreaDescription' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    ),
    array(
        'title' => 'Profile Feed Widget',
        'description' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
        'category' => 'Timeline',
        'type' => 'widget',
        'name' => 'timeline.new-feed',
        'displayName' => 'Timeline',
        'special' => 1,
        'defaultParams' => array(
            'max' => 6
        ),
        'canHaveChildren' => true,
        'childAreaDescription' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    ),
   array(
        'title' => 'Friends Quik view',
        'description' => 'Show member\'s friends in left side. ',
        'category' => 'Timeline',
        'type' => 'widget',
        'name' => 'timeline.tile-friends',
        'defaultParams' => array(
            'title' => 'Friends',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Group Profile Members',
        'description' => 'Displays a group\'s members on its profile.',
        'category' => 'Timeline',
        'type' => 'widget',
        'name' => 'timeline.tile-members-groups',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Members',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),

) ?>