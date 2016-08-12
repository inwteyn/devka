<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
    array(
        'title' => 'Contest Info',
        'description' => 'Displays a contest sponsor.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.contest-info',
        'requirements' => array(
            'no-subject'
        ),
    ),
    array(
        'title' => 'Contest Participants',
        'description' => 'Displays a contest participants.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.contest-participants',
        'requirements' => array(
            'no-subject'
        ),
    ),
    array(
        'title' => 'Contest Partners',
        'description' => 'Displays a contest participants.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.contest-partners',
        'requirements' => array(
            'no-subject'
        ),
    ),
    array(
        'title' => 'Contest Winner',
        'description' => 'Displays a recent contest winner.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.contest-winner',
        'requirements' => array(
            'no-subject'
        ),
    ),
    array(
        'title' => 'View Contest',
        'description' => 'Displays an active contest.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.view-contest',
        'requirements' => array(
            'no-subject'
        ),
    ),
    array(
        'title' => 'Browse Menu',
        'description' => 'Displays a menu in the contest pages.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Top Photos',
        'description' => 'Displays top photos for the current contest.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.contest-top-photos',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Recent Photos',
        'description' => 'Displays recently posted photos to the current contest.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.contest-recent-photos',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Random Photos',
        'description' => 'Displays recently random photos to the current contest.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.contest-random-photos',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Countdown Timer',
        'description' => 'Displays countdown timer to the current contest\'s expiration date.',
        'category' => 'HE - Contest',
        'type' => 'widget',
        'name' => 'hecontest.contest-countdown'
    )
) ?>