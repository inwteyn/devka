<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: touch-content.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
	// Touch widgets
	array(
		'title' => 'Mode Switcher',
		'description' => 'Shows switch links for Standard/Touch modes. Recommended to put it in Site Footer.',
		'category' => 'Touch',
		'type' => 'widget',
		'name' => 'touch.mode-switcher',
    'defaultParams' => array(
      'standard' => 'Standard Site',
			'touch' => 'TOUCH_MODE',
    ),
		'adminForm' => array(
      'elements' => array(
  			array(
          'Text',
          'standard',
          array(
            'label' => 'Standard Site Link Label',
            'default' => 'Standard Site',
          )
        ),
  			array(
          'Text',
          'touch',
          array(
            'label' => 'Touch Site Link Label',
            'default' => 'TOUCH_MODE',
          )
        ),
  			array(
          'Text',
          'mobile',
          array(
            'label' => 'Mobile Site Link Label',
            'default' => 'Mobile',
          )
        ),
      ),
  	),
	),
  array(
    'title' => 'Footer Menu',
    'description' => 'Shows the site-wide footer menu in touch mode. You can edit its contents in your menu editor.',
    'category' => 'Touch',
    'type' => 'widget',
    'name' => 'touch.menu-footer',
  ),
  array(
    'title' => 'Main Menu',
    'description' => 'Shows the site-wide main menu in touch mode. You can edit its contents in your menu editor.',
    'category' => 'Touch',
    'type' => 'widget',
    'name' => 'touch.menu-main',
    'defaultParams' => array(
      'count' => 3
    ),
		'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Text',
          'count',
          array(
            'label' => 'Display Number',
						'Description' => 'Other menus will be inserted into more menu links.',
          )
        ),
      ),
  	),
	),
  array(
    'title' => 'Dashboard',
    'description' => 'Shows the dashboard. You can edit its contents in your menu editor.',
    'category' => 'Touch',
    'type' => 'widget',
    'name' => 'touch.dashboard',
    'defaultParams' => array(
      'title' => 'Dashboard',
    ),
		'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
      ),
  	),
	),
  array(
    'title' => 'Site Logo',
    'description' => 'Shows your site-wide main logo or title in touch mode.  Images are uploaded via the <a href="admin/files" target="_parent">File Media Manager</a>.',
    'category' => 'Touch',
    'type' => 'widget',
    'name' => 'touch.menu-logo',
    'adminForm' => 'Core_Form_Admin_Widget_Logo',
  ),
	array(
		'title' => 'Main Header',
		'description' => 'Shows your site-wide main logo and site-wide mini menu',
		'category' => 'Touch',
		'type' => 'widget',
		'name' => 'touch.main-header',
        'adminForm' => 'Core_Form_Admin_Widget_Logo'
		),
  array(
    'title' => 'Tab Container',
    'description' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    'category' => 'Touch',
    'type' => 'widget',
    'name' => 'touch.container-tabs',
    'defaultParams' => array(
      'max' => 6
    ),
    'canHaveChildren' => true,
    'childAreaDescription' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    //'special' => 1,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Select',
          'max',
          array(
            'label' => 'Max Tab Count',
            'description' => 'Show sub menu at x containers.',
            'default' => 4,
            'multiOptions' => array(
              0 => 0,
              1 => 1,
              2 => 2,
              3 => 3,
              4 => 4,
            )
          )
        ),
      )
    ),
  ),

  array(
    'title' => 'Column Container',
    'description' => 'Adds a container with a separated columns. Any other blocks you drop inside it will be inserted into column.',
    'category' => 'Touch',
    'type' => 'widget',
    'name' => 'touch.container-columns',
    'canHaveChildren' => true,
    'childAreaDescription' => 'Adds a container with a separated columns. Any other blocks you drop inside it will be inserted into column.',
  ),

  array(
    'type' => 'widget',
    'name' => 'touch.rss',
    'title' => 'RSS',
    'description' => 'Displays an RSS feed.',
    'category' => 'Touch',
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title'
          )
        ),
        array(
          'Text',
          'url',
          array(
            'label' => 'URL'
          )
        ),
      ),
    ),
  ),

	array(
    'title' => 'Touch Profile Status',
    'description' => 'Displays Profile status ( with like options if Hire-Expert\'s Like Plugin installed ).',
    'category' => 'Touch',
    'type' => 'widget',
    'name' => 'touch.profile-status',
		'defaultParams' => array(
      'showlikebutton' => 1
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title'
          )
        ),
        array(
          'Select',
          'showlikebutton',
          array(
            'Label'=>'Like Button',
						'multiOptions' => array(
							'1'=>'Enabled',
							'0'=>'Disabled'
						)
          )
        ),
      )
    ),
  ),
	array(
    'title' => 'HTML Box',
    'description' => 'Allows you insert any HTML of your choice.',
    'category' => 'Touch',
    'type' => 'widget',
    'name' => 'touch.html-box',
    'special' => 1,
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title'
          )
        ),
        array(
          'Textarea',
          'data',
          array(
            'label' => 'HTML'
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Ad Campaign',
    'description' => 'Shows one of your ad banners. Requires that you have at least one active ad campaign.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'touch.ad-campaign',
   // 'special' => 1,
    'autoEdit' => true,
    'adminForm' => 'Core_Form_Admin_Widget_Ads',
  ),
/*  array(
    'type' => 'widget',
    'name' => 'touch.clock',
    'title' => 'Clock',
    'description' => 'Displays a clock.',
    'category' => 'Touch',
  ),

  array(
    'type' => 'widget',
    'name' => 'touch.weather',
    'title' => 'Weather',
    'description' => 'Displays the weather.',
    'category' => 'Touch',
  ),*/
)
?>