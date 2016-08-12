<style type="text/css">
  #submit_contacts-wrapper {
    clear: both;
  }

  #lnScreen {
    position: fixed;
    width: 100%;
    height: 100%;
    z-index: 999;
    background-color: #000;
    opacity: 0.5;
    display: none;
    left: 0;
    top: 0;
  }

  #lnPopUp {
    width: 300px;
    z-index: 9999;
    margin: 0 auto;
    position: fixed;
    left: 38%;
    top: 100px;
    padding: 10px;
    box-shadow: 0 1px 45px rgba(0, 0, 0, 0.35);
    background-color: #fff;
    padding-top: 0;
    display: none;
  }

  #lnPopUp div {
    margin: 0 auto;
    margin-top: 10px;
    text-align: center;
  }

  #lnPopUp div textarea,
  #lnPopUp div button,
  #lnPopUp div input {
    width: 100%;
  }
  #lnPopUp .ln_limit {
    font-size: 10px;
  }
</style>
<?php
if ($this->form && $this->form->_sign_up) {
  $this->providers = $this->form->_providers;
  $this->count = $this->form->_count;
  $this->fb_settings = $this->form->_fb_settings;
  $session = new Zend_Session_Namespace('inviter');
  $session->__set('user_referral_code', $this->form->_fb_settings['invite_code']);
}
?>
<script type='text/javascript'>

  en4.core.runonce.add(function () {
    provider.set_enabled('Facebook', <?php echo (isset($this->providers['facebook']) && $this->providers['facebook']) ? 1 : 0 ?>);
    provider.set_enabled('Twitter', <?php echo (isset($this->providers['twitter']) && $this->providers['twitter']) ? 1 : 0 ?>);
    provider.set_enabled('LinkedIn', <?php echo (isset($this->providers['linkedin']) && $this->providers['linkedin']) ? 1 : 0 ?>);
    provider.set_enabled('GMail', <?php echo (isset($this->providers['gmail']) && $this->providers['gmail']) ? 1 : 0 ?>);
    provider.set_enabled('Yahoo!', <?php echo (isset($this->providers['yahoo']) && $this->providers['yahoo']) ? 1 : 0 ?>);
    provider.set_enabled('Live/Hotmail', <?php echo (isset($this->providers['hotmail']) && $this->providers['hotmail']) ? 1 : 0 ?>);
    provider.set_enabled('MSN', <?php echo (isset($this->providers['hotmail']) && $this->providers['hotmail']) ? 1 : 0 ?>);
    provider.set_enabled('Last.fm', <?php echo (isset($this->providers['lastfm']) && $this->providers['lastfm']) ? 1 : 0 ?>);
    provider.set_enabled('Foursquare', <?php echo (isset($this->providers['foursquare']) && $this->providers['foursquare']) ? 1 : 0 ?>);
    provider.set_enabled('Mail.ru', <?php echo (isset($this->providers['mailru']) && $this->providers['mailru']) ? 1 : 0 ?>);
    provider.set_enabled('Orkut', <?php echo (isset($this->providers['gmail']) && $this->providers['gmail']) ? 1 : 0 ?>);

    provider.oauth_url = "<?php echo $this->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'format' => 'smoothbox'), 'default') ?>";
  });

  suggest.current_suggests = <?php echo Zend_Json::encode($this->current_suggests); ?>;

  en4.core.runonce.add(function () {
    if ($('separator2-label') != undefined) {
      $('separator2-label').getParent('li').destroy();
    }
    var cnt = '<?php echo 1;//$this->count; ?>';
    cnt *= 1;

    if (cnt == 0) {
      if ($('inviter-writer-form') != undefined) {
        $('inviter-writer-form').slide('hide').slide('out');
      }
    } else {
      if ($('inviter-uploader-form') != undefined) {
        $('inviter-uploader-form').slide('hide').slide('out');
      }

      if ($('inviter-writer-form') != undefined) {
        $('inviter-writer-form').slide('hide').slide('out');
      }
    }

  });

  function tab_slider($tab) {
    var cnt = '<?php echo $this->count; ?>';
    cnt *= 1;

    var select = $('provider_select');
    if (select) {
      var h = $('provider_select').getStyle('height');
      if (h != '0px') {
        $('provider_select').getElement('div').setStyle('height', '0px');
        $('provider_box-toggle_providers').toggleClass('hide_provider_btn');
      }
    }

    $$('.inviter-form-cont').removeClass('inviter-form-bg');
    $$('.inviter-form-cont').removeClass('inviter-form-hover');
    $$('.inviter-form').slide('hide').slide('out');
    $$('.inviter-tab-title').addClass('inviter-form-title');

    $('inviter-' + $tab + '-form').slide('hide').slide('in');
    $('inviter-' + $tab + '-title').removeClass('inviter-form-title');
    $('inviter-' + $tab + '-conteiner').addClass('inviter-form-bg');
  }

  function changeFields() {

  }

  function searchMembers() {
    $('field_search_criteria').submit();
  }

  function show_creation_description(id) {
    $('inviter-uploader-conteiner').getElements('div')[1].setStyle('height', '');

    if ($(id).hasClass('creation_item_hide')) {
      $(id).removeClass('creation_item_hide');
    } else {
      $(id).addClass('creation_item_hide');
    }
  }
</script>

<div id="fb-root"></div>
<script type="text/javascript">
  en4.core.runonce.add(function () {
    $('lnScreen').addEvent('click', function () {
      inviter.hideLnPopUp();
    });

    $('share_with_ln').addEvent('click', function () {
      inviter.shareWithLn();
    });


    FB.init({
      appId: '<?php echo $this->providers['facebook']; ?>',
      status: true,
      cookie: true,
      xfbml: true
    });
  });
</script>
<input name="fb-invite-code" id="fb-invite-code" type="hidden" value="<?php echo $this->fb_settings['invite_code']; ?>">
<input name="fb-redirect-url" id="fb-redirect-url" type="hidden"
       value="<?php echo $this->fb_settings['redirect_url']; ?>">
<input name="fb-invitation-url" id="fb-invitation-url" type="hidden"
       value="<?php echo $this->fb_settings['invitation_url']; ?>">
<input name="fb-host" id="fb-host" type="hidden"
       value="<?php if (isset($this->fb_settings['host'])) echo $this->fb_settings['host']; ?>">
<input name="fb-picture" id="fb-picture" type="hidden"
       value="<?php if (isset($this->fb_settings['picture'])) echo $this->fb_settings['picture']; ?>">
<input name="fb-caption" id="fb-caption" type="hidden"
       value="<?php if (isset($this->fb_settings['caption'])) echo $this->translate($this->fb_settings['caption']); ?>">
<input name="fb-message" id="fb-message" type="hidden"
       value="<?php if (isset($this->fb_settings['message'])) echo $this->translate($this->fb_settings['message']); ?>">
<input name="fb-signup" id="fb-signup" type="hidden"
       value="<?php if (isset($this->fb_settings['signup'])) echo $this->fb_settings['signup']; ?>">
<input name="fb-page-id" id="fb-page-id" type="hidden"
       value="<?php if (isset($this->fb_settings['page_id'])) echo $this->fb_settings['page_id']; ?>">
<input name="fb-fail-message" id="fb-fail-message" type="hidden"
       value="<?php echo $this->translate('INVITER_Invitations not sent'); ?>">

<input name="ln-fail-message" id="ln-fail-message" type="hidden"
       value="<?php echo $this->translate('INVITER_Ln_share_fail'); ?>">
<input name="ln-success-message" id="ln-success-message" type="hidden"
       value="<?php echo $this->translate('INVITER_Ln_share_ok'); ?>">

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
<div id="lnScreen"></div>
<div id="lnPopUp">
  <div>
    <input type="text" id="ln_title" name="ln_title"
           placeholder="<?php echo $this->translate('Inviter_Ln_Share_Title'); ?>"
           value="<?php echo $settings->getSetting('inviter.linkedin.default.title', ''); ?>">
    <p class="ln_limit"><?php echo $this->translate('Inviter_ln_max_title'); ?></p>
  </div>

  <div>
    <textarea id="ln_description" placeholder="<?php echo $this->translate('Inviter_Ln_Share_Description'); ?>"><?php echo $settings->getSetting('inviter.linkedin.default.descr', ''); ?></textarea>
    <p class="ln_limit"><?php echo $this->translate('Inviter_ln_max_descr'); ?></p>
  </div>

  <div>
    <input disabled="true" type="text" id="ln_link" name="ln_link"
           placeholder="<?php echo $this->translate('Inviter_Ln_Share_Link'); ?>"
           value="<?php echo $this->referralLink; ?>">
  </div>
  <div>
    <button id="share_with_ln"><?php echo $this->translate('Inviter_Share_with_LinkedIn'); ?></button>
  </div>
</div>