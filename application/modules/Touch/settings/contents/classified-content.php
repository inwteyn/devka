<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: classified-content.php 2011-04-26 11:18:13 mirlan $
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
    'title' => 'Profile Classifieds',
    'description' => 'Displays a member\'s classifieds on their profile. Please put on Member Profile page.',
    'category' => 'Classifieds',
    'type' => 'widget',
    'name' => 'touch.classified-profile-classifieds',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Classifieds',
      'titleCount' => true,
    ),
  ),
) ?>