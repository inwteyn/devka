<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
return array(
	'title' => 'Blogs',
	'description' => 'Displays the page\'s blogs.',
	'category' => 'Tabs',
	'type' => 'widget',
	'name' => 'pageblog.profile-blog',
  'isPaginated' => true,
	'defaultParams' => array(
		'title' => 'Blogs',
		'titleCount' => true
	)
);