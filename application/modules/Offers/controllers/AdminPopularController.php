<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminManageController.php 2012-06-07 11:40 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Offers_AdminPopularController extends Core_Controller_Action_Admin
{
  protected $offer_id;
  protected $_subject;

  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('offer_admin_main', array(), 'offer_admin_main_popular');


  }

  public function indexAction()
  {
    $setting = Engine_Api::_()->getDbTable("settings", "core");
    $this->view->popular = $popular = new Offers_Form_Popular();
    $this->view->setting_data = $setting->getSetting("offers_popular_count", 3);
    $popular->popular_count->setValue($setting->getSetting('offers_popular_count', 3));
    if (!$this->getRequest()->isPost()) {
      return false;
    }
      $post = $this->_getParam('popular_count');

    if($post>0 && preg_match("|^[\d]*$|", $post)) {
      $setting->setSetting("offers_popular_count", $post);
      $popular->addNotice("Your changes have been saved.");
      $popular->popular_count->setValue($setting->getSetting('offers_popular_count', 3));
    }else{
      $popular->addError("Failed. Please make sure that you have typed correct values.");
    }

  }
}