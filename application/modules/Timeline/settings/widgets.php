<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 2010-08-31 16:05  $
 * @author
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
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
        'title' => 'Members likes page',
        'description' => 'Displays a page\'s members on its profile.',
        'category' => 'Timeline',
        'type' => 'widget',
        'name' => 'timeline.member-likes-page',
        'defaultParams' => array(
            'title' => 'Member likes this',
            'titleCount' => true
        ),
    ),


);