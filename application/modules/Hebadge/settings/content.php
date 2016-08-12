<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 02.04.12 09:12 michael $
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
    'title' => 'Popular Badges tab',
    'description' => 'Displays popular badges for members, please put this widget in Tab container on Badges Home page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.badges',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_BADGES',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'paginator_type',
          array(
            'label' => 'HEBADGE_PAGINATION_TYPE',
            'multiOptions' => array(
              'all' => 'HEBADGE_PAGINATION_TYPE_ALL',
              'mini' => 'HEBADGE_PAGINATION_TYPE_MINI',
              'hide' => 'HEBADGE_PAGINATION_TYPE_HIDE'
            ),
            'value' => 'all',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Friends Badges tab',
    'description' => 'Displays friends badges, please put this widget in Tab container on Badges Home page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.badges-friend',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_BADGES_FRIEND'
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'paginator_type',
          array(
            'label' => 'HEBADGE_PAGINATION_TYPE',
            'multiOptions' => array(
              'all' => 'HEBADGE_PAGINATION_TYPE_ALL',
              'mini' => 'HEBADGE_PAGINATION_TYPE_MINI',
              'hide' => 'HEBADGE_PAGINATION_TYPE_HIDE'
            ),
            'value' => 'all',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Recent Badges tab',
    'description' => 'Displays recent badges for members, please put this widget in Tab container on Badges Home page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.badges-recent',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_BADGES_RECENT'
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'paginator_type',
          array(
            'label' => 'HEBADGE_PAGINATION_TYPE',
            'multiOptions' => array(
              'all' => 'HEBADGE_PAGINATION_TYPE_ALL',
              'mini' => 'HEBADGE_PAGINATION_TYPE_MINI',
              'hide' => 'HEBADGE_PAGINATION_TYPE_HIDE'
            ),
            'value' => 'all',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'My Badges',
    'description' => 'Displays the member badges with options to enable and disable them. You can put it on Manage Badges page and on other pages.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.badges-manage',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_BADGES_MANAGE'
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'paginator_type',
          array(
            'label' => 'HEBADGE_PAGINATION_TYPE',
            'multiOptions' => array(
              'all' => 'HEBADGE_PAGINATION_TYPE_ALL',
              'mini' => 'HEBADGE_PAGINATION_TYPE_MINI',
              'hide' => 'HEBADGE_PAGINATION_TYPE_HIDE'
            ),
            'value' => 'all',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'My Next Badges',
    'description' => 'Displays a list of next badges which the member can get. Put this widget on Badges Home, Manage Badges and on other pages.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.badges-next',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_BADGES_NEXT'
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'paginator_type',
          array(
            'label' => 'HEBADGE_PAGINATION_TYPE',
            'multiOptions' => array(
              'all' => 'HEBADGE_PAGINATION_TYPE_ALL',
              'mini' => 'HEBADGE_PAGINATION_TYPE_MINI',
              'hide' => 'HEBADGE_PAGINATION_TYPE_HIDE'
            ),
            'value' => 'all',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Best Members',
    'description' => 'Displays members who have most number of badges. Put this widget on Badges Home and on other pages.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.best-members',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_BEST_MEMBERS'
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'paginator_type',
          array(
            'label' => 'HEBADGE_PAGINATION_TYPE',
            'multiOptions' => array(
              'all' => 'HEBADGE_PAGINATION_TYPE_ALL',
              'mini' => 'HEBADGE_PAGINATION_TYPE_MINI',
              'hide' => 'HEBADGE_PAGINATION_TYPE_HIDE'
            ),
            'value' => 'all',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Last Members',
    'description' => 'Displays members who received a badge recently. Put this widget on Badges Home and on other pages.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.last-members',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_BADGES_LAST_MEMBERS'
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'paginator_type',
          array(
            'label' => 'HEBADGE_PAGINATION_TYPE',
            'multiOptions' => array(
              'all' => 'HEBADGE_PAGINATION_TYPE_ALL',
              'mini' => 'HEBADGE_PAGINATION_TYPE_MINI',
              'hide' => 'HEBADGE_PAGINATION_TYPE_HIDE'
            ),
            'value' => 'all',
          )
        ),
      )
    ),
  ),

  array(
    'title' => 'About me',
    'description' => 'Displays the member statistics for various actions: posted photos count, comments count, etc. Put this widget on Badges Home and on other pages.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.info',
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_INFO'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Search Badges',
    'description' => 'Displays a search form in the Badges Home page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.browse-search',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Menu',
    'description' => 'Displays a menu in the Badges Home & Badges Manage pages.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),


  array(
    'title' => 'Badge Profile: Information',
    'description' => 'Displays the badge short information. Put this widget in sidebar on Badge Profile page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.profile-body',
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_PROFILE_BODY',
    ),
    'requirements' => array(
      'subject' => 'hebadge_badge',
    ),
  ),
  array(
    'title' => 'Badge Profile: Learn',
    'description' => 'Displays the badge detailed description. Put this widget in Tab container on Badge Profile page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.profile-info',
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_PROFILE_INFO',
    ),
    'requirements' => array(
      'subject' => 'hebadge_badge',
    ),
  ),
  array(
    'title' => 'Badge Profile: Members',
    'description' => 'Displays the badge members. Put this widget in Tab container on Badge Profile page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.profile-members',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_PROFILE_MEMBERS'
    ),
    'requirements' => array(
      'subject' => 'hebadge_badge',
    ),
  ),
  array(
    'title' => 'Badge Profile: Requirements',
    'description' => 'Displays the badge requirements. Put this widget in Tab container on Badge Profile page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.profile-require',
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_PROFILE_REQUIRE'
    ),
    'requirements' => array(
      'subject' => 'hebadge_badge',
    ),
  ),
  array(
    'title' => 'Badge Profile: Photo',
    'description' => 'Displays the badge photo. Put this widget in sidebar on Badge Profile page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.profile-status',
    'defaultParams' => array(
    ),
    'requirements' => array(
      'subject' => 'hebadge_badge',
    ),
  ),
  array(
    'title' => 'Badge Profile: Requirements Completeness',
    'description' => 'Displays the badge requirements completeness by the current member. Put this widget in sidebar on Badge Profile page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.profile-loader',
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_PROFILE_LOADER'
    ),
    'requirements' => array(
      'subject' => 'hebadge_badge',
    ),
  ),
  array(
    'title' => 'Badge Profile: Enable button',
    'description' => 'Displays the badge enable button for the current member. Put this widget in sidebar on Badge Profile page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.profile-approved',
    'defaultParams' => array(
    ),
    'requirements' => array(
      'subject' => 'hebadge_badge',
    ),
  ),

  array(
    'title' => 'Member Profile: Badges',
    'description' => 'Displays the member profile badges. Put this widget in Tab container on Member Profile page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.profile-badges',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_PROFILE_BADGES',
      'titleCount' => true
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),

  array(
    'title' => 'Member Profile: Badges Icons',
    'description' => 'Displays the member profile badges icons. Put this widget on the sidebar on Member Profile page.',
    'category' => 'Badges',
    'type' => 'widget',
    'name' => 'hebadge.profile-badgeicons',
    'defaultParams' => array(
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),

  array(
    'title' => 'Page: Badges Filter',
    'description' => 'Displays a filter which allows to filter pages by badges. Put this widget on Browse Pages page.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'hebadge.pages-badges',
    'defaultParams' => array(
    ),
    'requirements' => array(
    ),
  ),

  array(
    'title' => 'Ranks',
    'description' => 'Displays a list of available ranks for credits. Put this widget on Credit Ranks page.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'hebadge.credit-badges',
    'defaultParams' => array(
    ),
    'requirements' => array(
    ),
  ),
  array(
    'title' => 'My Ranks',
    'description' => 'Displays the member\'s rank and completeness loader to the next rank. Put this widget on Credit Ranks and other pages.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'hebadge.credit-loader',
    'defaultParams' => array(
      'title' => 'HEBADGE_WIDGET_TITLE_CREDIT_LOADER'
    ),
    'requirements' => array(
    ),
  )




) ?>