<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Plugin_Menus
{

  public function canViewBadges()
  {
    return true;
  }

  public function canManageBadges()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()){
      return false;
    }

    return true;
  }


  public function onMenuInitialize_HebadgeMainManage($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()){
      return false;
    }

    return $row;
  }

  public function onMenuInitialize_HebadgeMainCredit($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()){
      return false;
    }

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('credit')){
      return false;
    }

    return $row;
  }


}