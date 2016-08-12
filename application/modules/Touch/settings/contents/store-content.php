<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: video-content.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  array(
    'title' => 'Navigation Tabs',
    'description' => 'Displays the Navigation tabs for stores having links of pages Store Home, Browse Products, Stores and Basket. This widget should be placed at the top of Store Home, Browse Products and Stores pages.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'touch.store-menu',
  ),
  array(
    'title' => 'Browse Products',
    'description' => 'Displays a list of products according to selected filters and search terms. Please put this widget on Store Home and Browse Products page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'touch.store-product-browse',
  ),
  array(
    'title' => 'Product Of The Day',
    'description' => 'Displays most viewed product for current day. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'touch.store-product-of-the-day',
		'defaultParams' => array(
      'title' => 'STORE_Product Of The Day',
    )
  ),
  array(
    'title' => 'Product Status',
    'description' => 'Displays a product\'s name and featured/sponsored status on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'touch.store-product-status',
  ),
  array(
    'title' => 'Product Information',
    'description' => 'Displays a product\'s detailed information on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'touch.store-product-info',
    'defaultParams' => array(
      'title' => 'Product Information',
    )
  ),

  array(
    'title' => 'Product Photos',
    'description' => 'Displays a product\'s photos on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'touch.store-product-photos',
    'defaultParams' => array(
      'title' => 'Photos'
    )
  ),
  array(
    'title' => 'Product Audios',
    'description' => 'Displays a product\'s audios on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'touch.store-product-audios',
    'defaultParams' => array(
      'title' => 'Audios'
    )
  ),
  array(
    'title' => 'Product Video',
    'description' => 'Displays a product\'s video on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'touch.store-product-video',
    'defaultParams' => array(
      'title' => 'Video'
    )
  )
) ?>