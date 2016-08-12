<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-07-02 19:25 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Survey_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('survey_admin_main', array(), 'survey_admin_main_settings');
    
    $this->view->form = $form = new Survey_Form_Admin_Global();

    // Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('survey');

    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);

      return;
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();

      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
    }
  }
}