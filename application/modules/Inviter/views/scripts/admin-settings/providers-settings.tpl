<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>

<style type="text/css">
  #fb_image-wrapper, #facebook-wrapper,
  #twitter-wrapper, #tw_image-wrapper,
  #linkedin-wrapper, #ln_image-wrapper,
  #gmail-wrapper, #gm_image-wrapper,
  #yahoo-wrapper, #ya_image-wrapper,
  #hotmail-wrapper, #ht_image-wrapper,
  #lastfm-wrapper, #lf_image-wrapper,
  #foursquare-wrapper, #fs_image-wrapper,
  #mailru-wrapper, #mr_image-wrapper {
    padding: 0;
  }
</style>

<div id="inviter-screen"><div id="inviter-video-container"></div></div>

<script type="text/javascript">
  function showTutorial(provider) {
    $('inviter-screen').setStyle('display', 'block');
    if(provider == 'facebook') {
      $('inviter-video-container').set('html',
        '<iframe width="854" height="510" src="https://www.youtube.com/embed/LDxlOVnjz3I" frameborder="0" allowfullscreen></iframe>'
      );
    }
    if(provider == 'twitter') {
      $('inviter-video-container').set('html',
        '<iframe width="854" height="510" src="https://www.youtube.com/embed/cSMFbx9VBEE" frameborder="0" allowfullscreen></iframe>'
      );
    }
    if(provider == 'linkedin') {
      $('inviter-video-container').set('html',
        '<iframe width="854" height="510" src="https://www.youtube.com/embed/mOxZr6aTmbE" frameborder="0" allowfullscreen></iframe>'
      );
    }
    if(provider == 'gmail') {
      $('inviter-video-container').set('html',
        '<iframe width="854" height="510" src="https://www.youtube.com/embed/paZwgmP4Mss" frameborder="0" allowfullscreen></iframe>'
      );
    }
    if(provider == 'yahoo') {
      $('inviter-video-container').set('html',
        '<iframe width="854" height="510" src="https://www.youtube.com/embed/ocZS8upIjjU" frameborder="0" allowfullscreen></iframe>'
      );
    }
    if(provider == 'hotmail') {
      $('inviter-video-container').set('html',
        '<iframe width="854" height="510" src="https://www.youtube.com/embed/YwllZ3WkjHw" frameborder="0" allowfullscreen></iframe>'
      );
    }
    if(provider == 'lastfm') {
      $('inviter-video-container').set('html',
        '<iframe width="854" height="510" src="https://www.youtube.com/embed/9QO6R9YTKgE" frameborder="0" allowfullscreen></iframe>'
      );
    }
    if(provider == 'foursquare') {
      $('inviter-video-container').set('html',
        '<iframe width="854" height="510" src="https://www.youtube.com/embed/_XuoV3zVDRo" frameborder="0" allowfullscreen></iframe>'
      );
    }
    if(provider == 'mailru') {
      $('inviter-video-container').set('html',
        '<iframe width="854" height="510" src="https://www.youtube.com/embed/nBIEKAKPBwA" frameborder="0" allowfullscreen></iframe>'
      );
    }
  }

  function showLoader(el) {

    var parent = $(el).getParent('.inviter_provider_description');
    var inputs = $(parent).getElements('input');
    var buttons = $(parent).getElements('button');
    console.log(parent);

    inputs.each(function(el) {
      $(el).disabled = true;
    });
    buttons.each(function(el) {
      $(el).disabled = true;
    });

    var loader = parent.getElement('.inviter-admin-loader');
    loader.setStyle('display', 'inline-block');
  }
  function hideLoader(el) {
    var parent = $(el).getParent('.inviter_provider_description');
    var inputs = $(parent).getElements('input');
    var buttons = $(parent).getElements('button');

    inputs.each(function(el) {
      $(el).disabled = false;
    });
    buttons.each(function(el) {
      $(el).disabled = false;
    });

    var loader = parent.getElement('.inviter-admin-loader');
    loader.setStyle('display', 'none');
  }
  function save_data(provider, values, el, clear) {

      var params = {};
      for (i = 0; i < values.length; i++) {
        if(clear == 1) {
          params[values[i]] = $(values[i]).value = '';
        } else {
          params[values[i]] = $(values[i]).value;
        }

      }
      showLoader(el);
      var r = new Request.JSON({
        url:en4.core.baseUrl + 'admin/inviter/settings/providers-save',
        data:{
          format:'json',
          provider:provider,
          values:params
        },
        onSuccess:function (response) {
          if (response.error) {
          } else {
          }
        },
        onFailure:function () {hideLoader(el);},
        onCancel:function () {hideLoader(el);},
        onException:function () {hideLoader(el);},
        onComplete:function() { hideLoader(el); }
      });
      r.send();
  }

  en4.core.runonce.add(function () {

    $('inviter-screen').addEvent('click', function() {
      $(this).setStyle('display', 'none');
      $('inviter-video-container').set('html', '');
    });

    $$('.provider_en').addEvent('click', function() {
      var self = this;
      var provider = $(this).get('data-provider');

      en4.core.request.send(new Request.JSON({
        url:'<?php echo $this->url(array('module' => 'inviter', 'controller' => 'settings', 'action' => 'enable-provider'), 'admin_default'); ?>',
        data:{
          format:'json',
          provider_id:provider
        },
        onRequest:function() {
          showLoader(self);
        },
        onSuccess:function (response) {
          if (response.status) {

          } else {

          }
        },
        onCancel:function() { hideLoader(self); },
        onComplete:function() { hideLoader(self); },
        onFailure:function(err) { hideLoader(self); }
      }));
    });

  });
</script>

<?php
$this->headLink()->appendStylesheet($this->baseUrl() . '/application/css.php?request=application/modules/Inviter/externals/styles/main.css');
?>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
    <div class='inviter-settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>