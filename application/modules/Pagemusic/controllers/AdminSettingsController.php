<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pagemusic_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_addons');
  }

  public function indexAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $form = new Pagemusic_Form_Admin_Global();
    $form->ipp->setValue($settings->getSetting('pagemusic.page', 10));
    $form->allow->setValue($settings->getSetting('page.browse.pagemusic', 0));
    $this->view->form = $form;

    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      Engine_Api::_()->getApi('settings', 'core')->setSetting('pagemusic.page', $values['ipp']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('page.browse.pagemusic', $values['allow']);

      $form->addNotice('Your changes have been saved.');
    }
  }
}