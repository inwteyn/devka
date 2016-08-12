<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  array(
    'title' => 'Badges',
    'description' => '',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'hebadge.page-badges',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_PAGE_BADGES',
      'titleCount' => true
    ),
    'requirements' => array(
      'subject' => 'page',
    ),
  ),

  array(
    'title' => 'Page Badges Icons',
    'description' => '',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'hebadge.page-badgeicons',
    'defaultParams' => array(
        'title' => 'Badges Icons',
    ),
    'requirements' => array(
      'subject' => 'page',
    ),
  )


) ?>