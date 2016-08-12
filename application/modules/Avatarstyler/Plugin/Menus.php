<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Avatarstyler_Plugin_Menus
{
  public function onMenuInitialize_UserProfileAvatar($row)
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    if ($settings->getSetting('avatarstyler.usage', 'allow') != 'allow') {
      return false;
    }

    return array(
      'label' => $row->label,
      'icon' => 'application/modules/User/externals/images/edit.png',
      'route' => 'avatarstyler',
      'params' => array()
    );
  }

}