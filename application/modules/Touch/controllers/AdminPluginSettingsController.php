<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminPluginSettingsController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_AdminPluginSettingsController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    if(Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('rate')){
              $this->view->yesrate = true;
    } else{
      $this->view->yesrate = false;
      return;
    }

    $this->view->form = $form = new Touch_Form_Admin_Rate_Settings();

    $setting_api = Engine_Api::_()->getApi('settings', 'core');

    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('touch');

    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);

      return;
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())){

      $values = $form->getValues();
      $setting_api->setSetting('touch.blog.rate-browse', $values['touch_blog_rate_browse']);
      $setting_api->setSetting('touch.blog.rate-widget', $values['touch_blog_rate_widget']);
      $setting_api->setSetting('touch.member.rate-browse', $values['touch_member_rate_browse']);
      $setting_api->setSetting('touch.event.rate-browse', $values['touch_event_rate_browse']);
      $setting_api->setSetting('touch.group.rate-browse', $values['touch_group_rate_browse']);
      $setting_api->setSetting('touch.classified.rate-browse', $values['touch_classified_rate_browse']);

      $setting_api->setSetting('touch.album.rate-browse', $values['touch_album_rate_browse']);
      $setting_api->setSetting('touch.album.rate-widget', $values['touch_album_rate_widget']);

//-------------------= Saving Article Rating Widget Visibility Settings =----------------------------

      $setting_api->setSetting('touch.article.rate-browse', $values['touch_article_rate_browse']);
      $setting_api->setSetting('touch.article.rate-manage', $values['touch_article_rate_manage']);
      $setting_api->setSetting('touch.article.rate-widget', $values['touch_article_rate_widget']);

//-------------------= End Of Saving Article Rating Widget Visibility Settings =---------------------

      $form->addNotice('TOUCH_Your changes have been saved.');

    }
    
    $form->touch_blog_rate_browse
        ->setValue($setting_api->getSetting('touch.blog.rate-browse', 1));
    $form->touch_blog_rate_widget
        ->setValue($setting_api->getSetting('touch.blog.rate-widget', 1));
    $form->touch_member_rate_browse
        ->setValue($setting_api->getSetting('touch.member.rate-browse', 1));
    $form->touch_event_rate_browse
        ->setValue($setting_api->getSetting('touch.event.rate-browse', 1));
    $form->touch_group_rate_browse
        ->setValue($setting_api->getSetting('touch.group.rate-browse', 1));
    $form->touch_classified_rate_browse
        ->setValue($setting_api->getSetting('touch.classified.rate-browse', 1));


    $form->touch_album_rate_browse
        ->setValue($setting_api->getSetting('touch.album.rate-browse', 1));
      
    $form->touch_album_rate_widget
        ->setValue($setting_api->getSetting('touch.album.rate-widget', 1));

//-------------------= Article Rating Widget Visibility Settings Form Population =----------------------------
    $form->touch_article_rate_browse
        ->setValue($setting_api->getSetting('touch.article.rate-browse', 1));

    $form->touch_article_rate_manage
        ->setValue($setting_api->getSetting('touch.article.rate-manage', 1));

    $form->touch_article_rate_widget
        ->setValue($setting_api->getSetting('touch.article.rate-widget', 1));

//-------------------= End Of Article Rating Widget Visibility Settings Form Population ----------------------
  }
}