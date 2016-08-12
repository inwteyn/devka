<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
return array(
  'title' => 'Documents',
  'description' => 'Displays the page\'s documents.',
  'category' => 'Tabs',
  'type' => 'widget',
  'name' => 'pagedocument.profile-document',
  'isPaginated' => true,
	'defaultParams' => array(
    'title' => 'Documents',
    'titleCount' => true
  )
);