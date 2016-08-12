<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
return array(
  array(
    'title' => 'Albums',
    'description' => 'Displays the page\'s albums.',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'pagealbum.profile-album',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Albums',
      'titleCount' => true
    )
  ),

  array(
    'title' => 'Showcase Photos',
    'description' => 'Displays random photos from your page albums. It is recommended to put this widget under `Page Like Status`.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'pagealbum.profile-random'
  )
);