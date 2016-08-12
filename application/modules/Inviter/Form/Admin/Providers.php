<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-03-31 10:15 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Inviter_Form_Admin_Providers extends Engine_Form
{
  public function init()
  {
    $providers = Engine_Api::_()->inviter()->getProvidersSystem(true);
    $t = array();
    foreach($providers as $provider) {
      $t[$provider->provider_id] = $provider->provider_enabled;
    }

    $module_path = Engine_Api::_()->getModuleBootstrap('inviter')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $this
      ->setTitle('INVITER_Providers Settings')
      ->setDescription("INVITER_FORM_ADMIN_PROVIDERS_DESCRIPTION")
      ->setOptions(array('class' => 'he_inviter_settings'));

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $translate = $this->getTranslator();

    $tutorial = $translate->_('INVITER_See tutorial');
    // Facebook
    $this->addElement('Text', 'inviter_facebook_consumer_key', array(
      'label' => 'Facebook App ID',
      'value' => $settings->getSetting('inviter.facebook.consumer.key', ''),
    ));

    $this->addElement('Text', 'inviter_facebook_consumer_secret', array(
      'label' => 'Facebook App Secret',
      'value' => $settings->getSetting('inviter.facebook.consumer.secret', ''),
    ));

    $this->addElement('Dummy', 'facebook', array('content'=>'<div class="inviter-admin-loader inviter-admin-loader-animation"></div>'));
    $this->addElement('Button', 'save_fb',
      array(
        'label' => $translate->_('Save Credentials'),
        'class' => 'provider-action-button',
        'onclick' => "save_data('facebook', new Array('inviter_facebook_consumer_key', 'inviter_facebook_consumer_secret'), this)"
      ));

    $this->addElement('Button', 'clear_fb',
      array(
        'label' => $translate->_('INVITER_Clear Credentials'),
        'class' => 'provider-action-button provider-action-button-right',
        'onclick' => "save_data('facebook', new Array('inviter_facebook_consumer_key', 'inviter_facebook_consumer_secret'), this, 1)"
      )
    );


    $this->getElement('save_fb')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_fb')->setDecorators(array('ViewHelper'));

    $this->addElement('Checkbox', 'fb_enabled', array('label' => 'Enabled?', 'data-provider'=>'1', 'class'=>'provider_en', 'checked'=>(boolean)$t[1]));

    $content = <<<CNTNT
<div class="provider_tutorial" onclick="showTutorial('facebook');">
  <img src="application/modules/Inviter/externals/images/providers_big/facebook_logo.png" alt="Facebook">
  <p>{$tutorial}</p>
</div>
CNTNT;
    $this->addElement('Dummy', 'fb_image', array(
      'content' => $content,
    ));

    $this->addDisplayGroup(
      array('inviter_facebook_consumer_key', 'inviter_facebook_consumer_secret', 'fb_enabled', 'fb_visible',
        'save_fb', 'clear_fb', 'facebook', 'fb_image'),
      'facebook_settings',
      array('class' => 'he_setting_fieldset')
    );

    $this->getDisplayGroup('facebook_settings')->addDecorator(
      'ProviderDescription',
      array('label' => $translate->_('Facebook settings'), 'description' => $translate->_('INVITER_FACEBOOK_APP_DESC')));

    // Twitter
    $this->addElement('Text', 'inviter_twitter_consumer_key', array(
      'label' => 'Consumer Key',
      'value' => $settings->getSetting('inviter.twitter.consumer.key', ''),
    ));

    $this->addElement('Text', 'inviter_twitter_consumer_secret', array(
      'label' => 'Consumer Secret',
      'value' => $settings->getSetting('inviter.twitter.consumer.secret', ''),
    ));

    $content = <<<CNTNT
<div class="provider_tutorial" onclick="showTutorial('twitter');">
  <img src="application/modules/Inviter/externals/images/providers_big/twitter_logo.png" alt="Twitter">
  <p>{$tutorial}</p>
</div>
CNTNT;
    $this->addElement('Dummy', 'tw_image', array(
      'content' => $content,
    ));
    $this->addElement('Dummy', 'twitter', array('content'=>'<div class="inviter-admin-loader inviter-admin-loader-animation"></div>'));
    $this->addElement('Button', 'save_tw',
      array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button',
        'onclick' => "save_data('twitter', new Array('inviter_twitter_consumer_key', 'inviter_twitter_consumer_secret'), this)"
      ));
    $this->addElement('Button', 'clear_tw',
      array(
        'label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right',
        'onclick' => "save_data('twitter', new Array('inviter_twitter_consumer_key', 'inviter_twitter_consumer_secret'), this, 1)"
      )
    );

    $this->getElement('save_tw')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_tw')->setDecorators(array('ViewHelper'));
    $this->addElement('Checkbox', 'tw_enabled', array('label' => 'Enabled?', 'data-provider'=>'7', 'class'=>'provider_en', 'checked'=>(boolean)$t[7]));
    $this->addDisplayGroup(
      array('inviter_twitter_consumer_key', 'inviter_twitter_consumer_secret', 'tw_enabled', 'save_tw', 'clear_tw', 'twitter', 'tw_image'),
      'twitter_settings',
      array('class' => 'he_setting_fieldset')
    );

    $this->getDisplayGroup('twitter_settings')->addDecorator(
      'ProviderDescription',
      array('label' => $translate->_('Twitter settings'), 'description' => $translate->_('INVITER_TWITTER_APP_DESC')));

    // LinkedIn
    $this->addElement('Text', 'inviter_linkedin_consumer_key', array(
      'label' => 'Client ID',
      'value' => $settings->getSetting('inviter.linkedin.consumer.key', ''),
    ));

    $this->addElement('Text', 'inviter_linkedin_consumer_secret', array(
      'label' => 'Client Secret',
      'value' => $settings->getSetting('inviter.linkedin.consumer.secret', ''),
    ));

    $this->addElement('Text', 'inviter_linkedin_default_title', array(
      'label' => 'Default Share Title',
      'value' => $settings->getSetting('inviter.linkedin.default.title', ''),
    ));

    $this->addElement('Text', 'inviter_linkedin_default_descr', array(
      'label' => 'Default Share Description',
      'value' => $settings->getSetting('inviter.linkedin.default.descr', ''),
    ));

    $this->addElement('Dummy', 'linkedin', array('content'=>'<div class="inviter-admin-loader inviter-admin-loader-animation"></div>'));
    $this->addElement('Button', 'save_ld', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button',
      'onclick' => "save_data('linkedin', new Array('inviter_linkedin_consumer_key', 'inviter_linkedin_consumer_secret', 'inviter_linkedin_default_title',
      'inviter_linkedin_default_descr'), this)"));
    $this->addElement('Button', 'clear_ld', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right',
      'onclick' => "save_data('linkedin', new Array('inviter_linkedin_consumer_key', 'inviter_linkedin_consumer_secret', 'inviter_linkedin_default_title',
      'inviter_linkedin_default_descr'), this, 1)"));
    $this->getElement('save_ld')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_ld')->setDecorators(array('ViewHelper'));
    $this->addElement('Checkbox', 'ln_enabled', array('label' => 'Enabled?', 'data-provider'=>'5', 'class'=>'provider_en', 'checked'=>(boolean)$t[5]));

    $content = <<<CNTNT
<div class="provider_tutorial" onclick="showTutorial('linkedin');">
  <img src="application/modules/Inviter/externals/images/providers_big/linkedin_logo.png" alt="LinkedIn">
  <p>{$tutorial}</p>
</div>
CNTNT;
    $this->addElement('Dummy', 'ln_image', array(
      'content' => $content,
    ));

    $this->addDisplayGroup(
      array('inviter_linkedin_consumer_key', 'inviter_linkedin_consumer_secret', 'inviter_linkedin_default_title',
        'inviter_linkedin_default_descr', 'ln_enabled', 'save_ld', 'clear_ld', 'linkedin', 'ln_image'),
      'linkedin_settings',
      array('class' => 'he_setting_fieldset')
    );

    $this->getDisplayGroup('linkedin_settings')->addDecorator(
      'ProviderDescription',
      array('label' => $translate->_('LinkedIn settings'), 'description' => $translate->_('INVITER_LINKEDIN_APP_DESC')));


    // GMail
    $this->addElement('Text', 'inviter_gmail_consumer_key', array(
      'label' => 'OAuth Client Id',
      'value' => $settings->getSetting('inviter.gmail.consumer.key', ''),
    ));

    $this->addElement('Text', 'inviter_gmail_consumer_secret', array(
      'label' => 'OAuth Client Secret',
      'value' => $settings->getSetting('inviter.gmail.consumer.secret', ''),
    ));

    $this->addElement('Dummy', 'gmail', array('content'=>'<div class="inviter-admin-loader inviter-admin-loader-animation"></div>'));
    $this->addElement('Button', 'save_gm', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button',
      'onclick' => "save_data('gmail', new Array('inviter_gmail_consumer_key', 'inviter_gmail_consumer_secret'), this)"));
    $this->addElement('Button', 'clear_gm', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right',
      'onclick' => "save_data('gmail', new Array('inviter_gmail_consumer_key', 'inviter_gmail_consumer_secret'), this, 1)"));
    $this->getElement('save_gm')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_gm')->setDecorators(array('ViewHelper'));
    $this->addElement('Checkbox', 'gm_enabled', array('label' => 'Enabled?', 'data-provider'=>'2', 'class'=>'provider_en', 'checked'=>(boolean)$t[2]));

    $content = <<<CNTNT
<div class="provider_tutorial" onclick="showTutorial('gmail');">
  <img src="application/modules/Inviter/externals/images/providers_big/gmail_logo.png" alt="Gmail">
  <p>{$tutorial}</p>
</div>
CNTNT;
    $this->addElement('Dummy', 'gm_image', array(
      'content' => $content,
    ));

    $this->addDisplayGroup(
      array('inviter_gmail_consumer_key', 'inviter_gmail_consumer_secret', 'gm_enabled', 'save_gm', 'clear_gm', 'gmail', 'gm_image'),
      'gmail_settings',
      array('class' => 'he_setting_fieldset')
    );

    $this->getDisplayGroup('gmail_settings')->addDecorator(
      'ProviderDescription',
      array('label' => $translate->_('GMail settings'), 'description' => $translate->_('INVITER_GMAIL_APP_DESC')));

    // Yahoo
    $this->addElement('Text', 'inviter_yahoo_consumer_key', array(
      'label' => 'Consumer Key',
      'value' => $settings->getSetting('inviter.yahoo.consumer.key', ''),
    ));

    $this->addElement('Text', 'inviter_yahoo_consumer_secret', array(
      'label' => 'Consumer Secret',
      'value' => $settings->getSetting('inviter.yahoo.consumer.secret', ''),
    ));

    $this->addElement('Dummy', 'yahoo', array('content'=>'<div class="inviter-admin-loader inviter-admin-loader-animation"></div>'));
    $this->addElement('Button', 'save_ya', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button',
      'onclick' => "save_data('yahoo', new Array('inviter_yahoo_consumer_key', 'inviter_yahoo_consumer_secret'), this)"));
    $this->addElement('Button', 'clear_ya', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right',
      'onclick' => "save_data('yahoo', new Array('inviter_yahoo_consumer_key', 'inviter_yahoo_consumer_secret'), this, 1)"));
    $this->getElement('save_ya')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_ya')->setDecorators(array('ViewHelper'));
    $this->addElement('Checkbox', 'ya_enabled', array('label' => 'Enabled?', 'data-provider'=>'8', 'class'=>'provider_en', 'checked'=>(boolean)$t[8]));

    $content = <<<CNTNT
<div class="provider_tutorial" onclick="showTutorial('yahoo');">
  <img src="application/modules/Inviter/externals/images/providers_big/yahoo_logo.png" alt="Yahoo">
  <p>{$tutorial}</p>
</div>
CNTNT;
    $this->addElement('Dummy', 'ya_image', array(
      'content' => $content,
    ));

    $this->addDisplayGroup(
      array('inviter_yahoo_consumer_key', 'inviter_yahoo_consumer_secret', 'ya_enabled', 'save_ya', 'clear_ya', 'yahoo', 'ya_image'),
      'yahoo_settings',
      array('class' => 'he_setting_fieldset')
    );

    $this->getDisplayGroup('yahoo_settings')->addDecorator(
      'ProviderDescription',
      array('label' => $translate->_('Yahoo settings'), 'description' => $translate->_('INVITER_YAHOO_APP_DESC')));

    // hotmail
    $this->addElement('Text', 'inviter_hotmail_consumer_key', array(
      'label' => 'Client ID',
      'value' => $settings->getSetting('inviter.hotmail.consumer.key', ''),
    ));

    $this->addElement('Text', 'inviter_hotmail_consumer_secret', array(
      'label' => 'Client secret',
      'value' => $settings->getSetting('inviter.hotmail.consumer.secret', ''),
    ));

    $this->addElement('Dummy', 'hotmail', array('content'=>'<div class="inviter-admin-loader inviter-admin-loader-animation"></div>'));
    $this->addElement('Button', 'save_ms', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button',
      'onclick' => "save_data('hotmail', new Array('inviter_hotmail_consumer_key', 'inviter_hotmail_consumer_secret'), this)"));
    $this->addElement('Button', 'clear_ms', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right',
      'onclick' => "save_data('hotmail', new Array('inviter_hotmail_consumer_key', 'inviter_hotmail_consumer_secret'), this, 1)"));
    $this->getElement('save_ms')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_ms')->setDecorators(array('ViewHelper'));
    $this->addElement('Checkbox', 'ht_enabled', array('label' => 'Enabled?', 'data-provider'=>'3', 'class'=>'provider_en', 'checked'=>(boolean)$t[3]));

    $content = <<<CNTNT
<div class="provider_tutorial" onclick="showTutorial('hotmail');">
  <img src="application/modules/Inviter/externals/images/providers_big/hotmail_logo.png" alt="MSN/Live/Hotmail">
  <p>{$tutorial}</p>
</div>
CNTNT;
    $this->addElement('Dummy', 'ht_image', array(
      'content' => $content,
    ));

    $this->addDisplayGroup(
      array('inviter_hotmail_consumer_key', 'inviter_hotmail_consumer_secret', 'ht_enabled', 'save_ms', 'clear_ms', 'hotmail', 'ht_image'),
      'hotmail_settings',
      array('class' => 'he_setting_fieldset')
    );

    $this->getDisplayGroup('hotmail_settings')->addDecorator(
      'ProviderDescription',
      array('label' => $translate->_('Live/Hotmail/MSN settings'), 'description' => $translate->_('INVITER_HOTMAIL_APP_DESC')));


    // last.fm
    $this->addElement('Dummy', 'lastfm', array('content'=>'<div class="inviter-admin-loader inviter-admin-loader-animation"></div>'));
    $this->addElement('Text', 'inviter_lastfm_api_key', array(
      'label' => 'Api key',
      'value' => $settings->getSetting('inviter.lastfm.api.key', ''),
    ));

    $this->addElement('Text', 'inviter_lastfm_secret', array(
      'label' => 'Secret',
      'value' => $settings->getSetting('inviter.lastfm.secret', ''),
    ));

    $this->addElement('Button', 'save_lf', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button',
      'onclick' => "save_data('lastfm', new Array('inviter_lastfm_api_key', 'inviter_lastfm_secret'), this)"));
    $this->addElement('Button', 'clear_lf', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right',
      'onclick' => "save_data('lastfm', new Array('inviter_lastfm_api_key', 'inviter_lastfm_secret'), this, 1)"));
    $this->getElement('save_lf')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_lf')->setDecorators(array('ViewHelper'));
    $this->addElement('Checkbox', 'lf_enabled', array('label' => 'Enabled?', 'data-provider'=>'4', 'class'=>'provider_en', 'checked'=>(boolean)$t[4]));

    $content = <<<CNTNT
<div class="provider_tutorial" onclick="showTutorial('lastfm');">
  <img src="application/modules/Inviter/externals/images/providers_big/lastfm_logo.png" alt="Last.FM">
  <p>{$tutorial}</p>
</div>
CNTNT;
    $this->addElement('Dummy', 'lf_image', array(
      'content' => $content,
    ));

    $this->addDisplayGroup(
      array('inviter_lastfm_api_key', 'inviter_lastfm_secret', 'lf_enabled', 'save_lf', 'clear_lf', 'lastfm', 'lf_image'),
      'lastfm_settings',
      array('class' => 'he_setting_fieldset')
    );

    $this->getDisplayGroup('lastfm_settings')->addDecorator(
      'ProviderDescription',
      array('label' => $translate->_('Last.fm settings'), 'description' => $translate->_('INVITER_LASTFM_APP_DESC')));

    // foursquare

    $this->addElement('Text', 'inviter_foursquare_consumer_key', array(
      'label' => 'Client ID',
      'value' => $settings->getSetting('inviter.foursquare.consumer.key', ''),
    ));

    $this->addElement('Text', 'inviter_foursquare_consumer_secret', array(
      'label' => 'Client Secret',
      'value' => $settings->getSetting('inviter.foursquare.consumer.secret', ''),
    ));

    $this->addElement('Dummy', 'foursquare', array('content'=>'<div class="inviter-admin-loader inviter-admin-loader-animation"></div>'));
    $this->addElement('Button', 'save_16', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button',
      'onclick' => "save_data('foursquare', new Array('inviter_foursquare_consumer_key', 'inviter_foursquare_consumer_secret'), this)"));
    $this->addElement('Button', 'clear_16', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right',
      'onclick' => "save_data('foursquare', new Array('inviter_foursquare_consumer_key', 'inviter_foursquare_consumer_secret'), this, 1)"));
    $this->getElement('save_16')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_16')->setDecorators(array('ViewHelper'));
    $this->addElement('Checkbox', 'fs_enabled', array('label' => 'Enabled?', 'data-provider'=>'9', 'class'=>'provider_en', 'checked'=>(boolean)$t[9]));
    $content = <<<CNTNT
<div class="provider_tutorial" onclick="showTutorial('foursquare');">
  <img src="application/modules/Inviter/externals/images/providers_big/foursquare_logo.png" alt="Foursquare">
  <p>{$tutorial}</p>
</div>
CNTNT;
    $this->addElement('Dummy', 'fs_image', array(
      'content' => $content,
    ));

    $this->addDisplayGroup(
      array('inviter_foursquare_consumer_key', 'inviter_foursquare_consumer_secret', 'fs_enabled', 'save_16', 'clear_16', 'foursquare', 'fs_image'),
      'foursquare_settings',
      array('class' => 'he_setting_fieldset')
    );

    $this->getDisplayGroup('foursquare_settings')->addDecorator(
      'ProviderDescription',
      array('label' => $translate->_('Foursquare settings'), 'description' => $translate->_('INVITER_FOUR_APP_DESC')));


    // mail.ru

    $this->addElement('Text', 'inviter_mailru_id', array(
      'label' => 'ID',
      'value' => $settings->getSetting('inviter.mailru.id', ''),
    ));

    $this->addElement('Text', 'inviter_mailru_private_key', array(
      'label' => 'Private Key',
      'value' => $settings->getSetting('inviter.mailru.private.key', ''),
    ));
    $this->addElement('Text', 'inviter_mailru_secret_key', array(
      'label' => 'Secret Key',
      'value' => $settings->getSetting('inviter.mailru.secret.key', ''),
    ));

    $this->addElement('Dummy', 'mailru', array('content'=>'<div class="inviter-admin-loader inviter-admin-loader-animation"></div>'));
    $this->addElement('Button', 'save_mr', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button',
      'onclick' => "save_data('mailru', new Array('inviter_mailru_id', 'inviter_mailru_private_key', 'inviter_mailru_secret_key'), this)"));
    $this->addElement('Button', 'clear_mr', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right',
      'onclick' => "save_data('mailru', new Array('inviter_mailru_id', 'inviter_mailru_private_key', 'inviter_mailru_secret_key'), this, 1)"));
    $this->getElement('save_mr')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_mr')->setDecorators(array('ViewHelper'));
    $this->addElement('Checkbox', 'mr_enabled', array('label' => 'Enabled?', 'data-provider'=>'6', 'class'=>'provider_en', 'checked'=>(boolean)$t[6]));
    $content = <<<CNTNT
<div class="provider_tutorial" onclick="showTutorial('mailru');">
  <img src="application/modules/Inviter/externals/images/providers_big/mailru_logo.png" alt="Mail.ru">
  <p>{$tutorial}</p>
</div>
CNTNT;
    $this->addElement('Dummy', 'mr_image', array(
      'content' => $content,
    ));

    $this->addDisplayGroup(
      array('inviter_mailru_id', 'inviter_mailru_private_key', 'inviter_mailru_secret_key', 'mr_enabled', 'save_mr', 'clear_mr', 'mailru', 'mr_image'),
      'mailru_settings',
      array('class' => 'he_setting_fieldset')
    );

    $this->getDisplayGroup('mailru_settings')->addDecorator(
      'ProviderDescription',
      array('label' => $translate->_('Mail.ru settings'), 'description' => $translate->_('INVITER_MAILRU_APP_DESC')));

  }
}