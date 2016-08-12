<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Widgets.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  array(
    'title' => 'Events',
    'description' => 'Displays the page\'s events.',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'pageevent.profile-event',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Events',
      'titleCount' => true
    )
  ),
  array(
    'title' => 'Event Calendar',
    'description' => 'Displays the page\'s event calendar.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'pageevent.profile-calendar',
    'defaultParams' => array(
      'title' => 'Event Calendar',
      'titleCount' => true
    )
  ),
);