<?php

return array(
  array(
    'title' => 'Navigation Tabs',
    'description' => 'Displays the Navigation tabs for offers having links of offers Browse Offers, My Offers. This widget should be placed only at the top of Browse Offers',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.navigation-tabs',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Browse Offers',
    'description' => 'Displays offers listing on Browse Offers page',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.browse-offers'
  ),
  array(
    'title' => 'Profile Offers',
    'description' => 'Displays offers',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.profile-offers',
    'defaultParams' => array(
      'title' => 'OFFERS_Offers',
      'titleCount' => true
    )
  ),
  array(
    'title' => 'Offer Details',
    'description' => 'Displaya the offer details on its Offer Profile Page',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.offer-details',
    'defaultParams' => array(
      'title' => 'Offers_Details',
      'titleCount' => false
    )
  ),
  array(
    'title' => 'Offer Photo',
    'description' => 'Displays the offer photo on its Offer Profile Page',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.offer-photo'
  ),
  array(
    'title' => 'Offer Menu',
    'description' => 'Displays the offer menu options on its Offer Profile Page',
    'type' => 'widget',
    'category' => 'Offers',
    'name' => 'offers.offer-menu'
  ),
  array(
    'title' => 'Search',
    'description' => 'Displays search offers form on Browse Offers page',
    'type' => 'widget',
    'category' => 'Offers',
    'name' => 'offers.offer-search'
  ),
  array(
    'title' => 'Categories',
    'description' => 'Displays offers categories on Browse Offers page',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.offer-categories',
    'defaultParams' => array(
      'title' => 'OFFERS_Categories',
    )
  ),
  array(
    'title' => 'Most Popular Offers',
    'description' => 'Displays most popular offers',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.popular-offers',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'OFFERS_Popular',
    )
  ),
  array(
    'title' => 'Recent Offers',
    'description' => 'Displays recently created offers',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.recent-offers',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'OFFERS_Recent',
    )
  ),
  array(
    'title' => 'Hot Offers',
    'description' => 'Displays hot offers which expires very soon',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.hot-offers',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'OFFERS_Hot',
    )
  ),

  array(
    'title' => 'Contacts Offer',
    'description' => 'Displays contact details of the offer on its Offer Profile Page',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.offer-contacts',
    'defaultParams' => array(
      'title' => 'OFFERS_Contacts'
    )
  ),

  array(
    'title' => 'Participants Offer',
    'description' => 'Displays the offer participants on its Offer Profile Page',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.offer-participants',
    'defaultParams' => array(
      'title' => 'OFFERS_Participants',
      'titleCount' => true
    )
  ),

  array(
    'title' => 'Profile Status',
    'description' => 'Displays the offer title on its Offer Profile Page',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.profile-status',
  ),

  array(
    'title' => 'Subscribers',
    'description' => 'Display the offer subscribers',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.offer-subscribers',
    'defaultParams' => array(
      'title' => 'OFFERS_Subscribers'
    )
  ),

  array(
    'title' => 'Featured Offer',
    'description' => 'Displays featured offer',
    'category' => 'Offers',
    'type' => 'widget',
    'name' => 'offers.featured-offer',
    'defaultParams' => array(
      'title' => 'OFFERS_Featured_offer'
    )
  )
);