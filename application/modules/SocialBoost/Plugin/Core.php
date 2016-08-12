<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Core.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class SocialBoost_Plugin_Core
{
  public function onRenderLayoutDefault($event)
  {
    $view = $event->getPayload();
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $fbAppId = $settings->getSetting('socialboost.facebook.app.id', false);
    $fbPage = $settings->getSetting('socialboost.admin.facebook', false);
    $twitterPage = $settings->getSetting('socialboost.admin.twitter', false);
    $gpPage = $settings->getSetting('socialboost.admin.google', false);
    $flag = $settings->getSetting('socialboost.user.like.' . $viewer->getIdentity(), 0);

    if (
      (!$fbPage && !$twitterPage && !$gpPage) ||
      (!$viewer || !$viewer->getIdentity()) ||
      $flag
    ) {
      return;
    }

    $content = addslashes($view->action('index', 'index', 'social-boost', array('from' => 'SocialBoost_Plugin_Core')));
    $content = preg_replace(array('/\r/', '/\n/'), '', $content);

    $subscribeUrl = $view->url(array('module' => 'social-boost', 'controller' => 'index', 'action' => 'subscribe'), 'default');
    $likeUrl = $view->url(array('module' => 'social-boost', 'controller' => 'index', 'action' => 'like'), 'default');
    $maxDays = $settings->getSetting('socialboost.admin.days', 90);
    $popupType = Engine_Api::_()->getApi('core', 'socialBoost')->getPopupType();


    // Core js
    $script = <<<EOF
  SocialBoost.content = Elements.from('$content', false);
  SocialBoost.content.addClass('socialboost_fade socialboost_hide socialpopup');
  SocialBoost.subscribeUrl = '$subscribeUrl';
  SocialBoost.likeUrl = '$likeUrl';
  SocialBoost.maxDays = $maxDays;
  SocialBoost.popupType = '$popupType';

  SocialBoost.modalView = new Element('div', {
    'class': 'socialboost_modal_backdrop socialboost_fade',
    events: {
        click: function(){
            SocialBoost.hidePopup();
        }
    }
});
  window.addEvent('domready', function(){
    SocialBoost.init();
    SocialBoost.showPopup();
  });
EOF;

    $view->headScript()
      ->appendFile($view->layout()->staticBaseUrl . 'application/modules/SocialBoost/externals/scripts/core.js')
      ->appendScript($script);

    ///// ---------------Twitter----------------------------

    $twttr = <<<EOF
    Asset.javascript('https://platform.twitter.com/widgets.js', {
      onLoad: function(){
        twttr.events.bind('follow', function(event) {
          SocialBoost.like('twitter');
        });
      }
    });
EOF;

    $view->headScript()
      ->appendScript($twttr);


    ///// ---------------Google----------------------------

    $google = <<<EOF
    var plusClick = function(data)
    {
      if(data.state=="on"){
        SocialBoost.like('google');
      }else if(data.state=="off"){

      }
    }
EOF;

    $view->headScript()
      ->appendFile("https://apis.google.com/js/plusone.js")
      ->appendScript($google);


    ///// ---------------Facebook----------------------------

    if ($fbAppId) {
      $fb = <<<EOF
      Asset.javascript('https://connect.facebook.net/en_EN/all.js#xfbml=1&appId=$fbAppId', {
  onLoad: function(){
    FB.Event.subscribe('edge.create',
      function(href, widget) {
        SocialBoost.like('facebook');
      }
    );
  }
});
EOF;
      $view->headScript()
        ->appendScript($fb);
    }


  }
}