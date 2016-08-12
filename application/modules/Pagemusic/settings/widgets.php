<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
return array(
	'title' => 'Music',
	'description' => 'Displays a page\'s music on it\'s profile.',
	'category' => 'Tabs',
	'type' => 'widget',
	'name' => 'pagemusic.profile-music',
  'isPaginated' => true,
	'defaultParams' => array(
		'title' => 'Music',
		'titleCount' => true
	)
);