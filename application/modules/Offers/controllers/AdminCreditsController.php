<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminCreditsController.php 06.09.12 17:59 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_AdminCreditsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('credit')) {
      $this->_redirectCustom($this->view->url(array(),'offer_admin_manage', true));
    }

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('offer_admin_main', array(), 'offer_admin_main_credits');
  }

  public function indexAction()
  {
    /**
     * @var $settings Core_Model_DbTable_Settings
     * @var $hecoreModulesTbl Hecore_Model_DbTable_Modules
     */

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $hecoreModulesTbl = Engine_Api::_()->getDbTable('modules', 'hecore');

    $select = $hecoreModulesTbl->select()
      ->where('name = ?', 'credit');

    $credit = $hecoreModulesTbl->fetchRow($select);
    $this->view->error = 0;
    if (version_compare($credit->version, '4.2.5') < 0) {
      $this->view->error = 1;
      $settings->setSetting('offers.credit.enabled', 0);
      return ;
    }

    $this->view->credit_enabled = $credit_enabled = $settings->getSetting('offers.credit.enabled', 0);
    $this->view->page_enabled = $page_enabled = $hecoreModulesTbl->isModuleEnabled('page');

    if ($credit_enabled && $page_enabled) {
      $this->view->form = $form = new Offers_Form_Admin_Credits_Settings();

      $values = array();
      $values['credits_on_pages'] = $settings->getSetting('offers.credit.pages', 0);

      $form->populate($values);

      if (!$this->getRequest()->isPost()) {
        return ;
      }
      // Check license
      $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
      $product_result = $hecoreApi->checkProduct('offers');

      if (isset($product_result['result']) && !$product_result['result']) {
        $form->addError($product_result['message']);
        $this->view->headScript()->appendScript($product_result['script']);

        return;
      }

      if (!$form->isValid($this->getRequest()->getPost())) {
        return ;
      }

      $values = array_merge($values, $form->getValues());
      $settings->setSetting('offers.credit.pages', $values['credits_on_pages']);
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function enableAction()
  {
    /**
     * @var $settings Core_Model_DbTable_Settings
     */

    $switcher = $this->_getParam('switcher', 0);

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $settings->setSetting('offers.credit.enabled', $switcher);
  }
}
