<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
	'title' => 'Discussions',
	'description' => 'Displays the page\'s discussions.',
	'category' => 'Tabs',
	'type' => 'widget',
	'name' => 'pagediscussion.profile-discussion',
  'isPaginated' => true,
	'defaultParams' => array(
		'title' => 'Discussions',
		'titleCount' => true
	)
);