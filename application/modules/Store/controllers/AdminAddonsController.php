<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_AdminAddonsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->activeAddonMenu = 'store_admin_main_bundle';
    $this->view->activeMenu = 'store_admin_main_addons';
  }

  public function indexAction()
  {
    $addons = Engine_Api::_()->getDbTable('addons', 'store');
    $addon = $addons->getAvailableAddon();

    if(!$addon) {
      $this->view->noAddons = true;
    } else {
      $this->redirect(
        $this->view->url(array(
          'module' => $addon->name
        ), 'admin_default', 1)
      );
      return;
    }
  }

}