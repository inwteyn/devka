<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pageinstagram_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_addons');
  }

  public function indexAction()
  {
    $form = new Pageinstagram_Form_Admin_Global();
    $form->getDecorator('description')->setOption('escape', false);
    $this->view->form = $form;
      

      if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
      {
          $settings = Engine_Api::_()->getApi('settings', 'core');
          $value = $form->getValue('page_item_on_page');
          $settings->setSetting('page.count.item.on.page', $value);
          $form->page_item_on_page->setValue($value);


          $value = $form->getValue('page_instagram_option');
          $settings->setSetting('page.instagram.option', $value);
          $form->page_instagram_option->setValue($value);


          $form->addNotice('Your changes have been saved.');
      }
  }


}