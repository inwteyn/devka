<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Settings.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class SocialBoost_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    $this->setTitle('SOCIALBOOST_SETTINGS_FORM_TITLE');
    $this->setDescription('SOCIALBOOST_SETTINGS_FORM_DESCRIPTION');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $modules = Engine_Api::_()->getDbTable('modules', 'core');

    $this->addElement('Text', 'facebook_app_id', array(
      'label' => 'SOCIALBOOST_FACEBOOK_APP_ID_TITLE',
      'description' => 'SOCIALBOOST_FACEBOOK_APP_ID_DESC',
      'value' => $settings->getSetting('socialboost.facebook.app.id', ''),
      'escape' => false
    ));
    $desc_decorator = $this->getElement('facebook_app_id')->getDecorator('description');
    if ($desc_decorator) {
      $desc_decorator->setEscape(false);
    }

    $this->addElement('Text', 'facebook', array(
      'label' => 'Facebook Page',
      'placeholder' => 'https://www.facebook.com/YourPage',
      'value' => $settings->getSetting('socialboost.admin.facebook', ''),
    ));


    $this->addElement('Text', 'twitter', array(
      'label' => 'Twitter Page',
      'placeholder' => 'https://twitter.com/YourPage',
      'value' => $settings->getSetting('socialboost.admin.twitter', ''),
    ));


    $this->addElement('Text', 'google', array(
      'label' => 'Google plus Page',
      'placeholder' => 'https://plus.google.com/communities/YourCommunity',
      'value' => $settings->getSetting('socialboost.admin.google', ''),
    ));

    $this->addElement('Checkbox', 'newsletter', array(
      'label' => '',
      'description' => 'Enable subscription to digest newsletters',
      'checkedValue' => 1,
      'uncheckedValue' => 0,
      'value' => $settings->getSetting('socialboost.admin.newsletter', 0)
    ));
    if( !$modules->isModuleEnabled('updates') ) {
      $this->newsletter->setLabel("This option requires <a href='http://www.hire-experts.com/social-engine/newsletter-updates-plugin' target='_blank'>Newsletter Updates plugin</a>");
      $this->newsletter->setAttrib('disabled', true);
      $this->newsletter->getDecorator('label')->setOption('escape', false);
      $this->newsletter->setValue(0);
    }

    $this->addElement('Text', 'days', array(
      'label' => 'Max Days',
      'description' => 'Days number when Social Boost popup re-appears again',
      'value' => $settings->getSetting('socialboost.admin.days', 90),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
    ));
  }
}