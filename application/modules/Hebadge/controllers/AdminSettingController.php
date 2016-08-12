<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingController.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_AdminSettingController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hebadge_admin_main', array(), 'hebadge_setting');

    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $this->view->form = $form = new Hebadge_Form_Admin_Setting();

    $form->populate(array(
    ));

    if (!$this->getRequest()->isPost()){
      return ;
    }
    if (!$form->isValid($this->getRequest()->getPost())){
      return ;
    }
    // Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('badges');


    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);
      return;
    }
    $values = $form->getValues();
    
    $setting->setSetting('hebadge.user_approved', $values['user_approved']);
    $setting->setSetting('hebadge.showuserbadge', $values['showuserbadge']);
    $setting->setSetting('habadge.allow_public_view',$values['allow_public_view']);

    $form->addNotice('Your changes have been saved.');

  }





}