<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2011-04-26 11:18:13 mirlan $
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
		'title' => 'Touch Mode Switcher',
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
            'default' => 'Standard',
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
)
?>