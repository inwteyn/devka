<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_addons');
  }
  public function indexAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $form = new Pagediscussion_Form_Admin_Settings;
    $form->populate(array(
      'perpage_list' => $settings->getSetting('pagediscussion.perpage.list', 10),
      'perpage_post' => $settings->getSetting('pagediscussion.perpage.post', 10),
    ));

    $this->view->form = $form;

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $settings->setSetting('pagediscussion.perpage.list', $form->getValue('perpage_list'));
      $settings->setSetting('pagediscussion.perpage.post', $form->getValue('perpage_post'));

      $form->addNotice('Your changes have been saved.');
    }
  }

}