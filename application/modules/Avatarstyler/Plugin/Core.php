<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Core.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Avatarstyler_Plugin_Core
{
  public function onRenderLayoutDefault($event)
  {
    $view = $event->getPayload();
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer || !$viewer->getIdentity() ) {
      return;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $content = addslashes($view->action('index', 'index', 'social-boost', array('from' => 'Avatarstyler_Plugin_Core')));
    $content = preg_replace(array('/\r/', '/\n/'), '', $content);

    $subscribeUrl = $view->url(array('module' => 'social-boost', 'controller' => 'index', 'action' => 'subscribe'), 'default');
    $likeUrl = $view->url(array('module' => 'social-boost', 'controller' => 'index', 'action' => 'like'), 'default');
    $maxDays = $settings->getSetting('socialboost.admin.days', 90);
    $popupType = Engine_Api::_()->getApi('core', 'socialBoost')->getPopupType();


    // Core js
    $script = <<<EOF
  Avatarstyler.content = Elements.from('$content', false);
  Avatarstyler.content.addClass('socialboost_fade socialboost_hide socialpopup');
  Avatarstyler.subscribeUrl = '$subscribeUrl';
  Avatarstyler.likeUrl = '$likeUrl';
  Avatarstyler.maxDays = $maxDays;
  Avatarstyler.popupType = '$popupType';

  Avatarstyler.modalView = new Element('div', {
    'class': 'socialboost_modal_backdrop socialboost_fade',
    events: {
        click: function(){
            Avatarstyler.hidePopup();
        }
    }
});
  window.addEvent('domready', function(){
    Avatarstyler.init();
    Avatarstyler.showPopup();
  });
EOF;

    $view->headScript()
      ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Avatarstyler/externals/scripts/core.js')
      ->appendScript($script);

    ///// ---------------Twitter----------------------------

    $twttr = <<<EOF
    Asset.javascript('https://platform.twitter.com/widgets.js', {
      onLoad: function(){
        twttr.events.bind('follow', function(event) {
          console.log(event);
          Avatarstyler.like('twitter');
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
      console.log(data);
      if(data.state=="on"){
        Avatarstyler.like('google');
      }else if(data.state=="off"){

      }
    }
EOF;

    $view->headScript()
      ->appendFile("https://apis.google.com/js/plusone.js")
      ->appendScript($google);


    ///// ---------------Facebook----------------------------
    $fbAppId = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('socialboost.facebook.app.id', false);

    if($fbAppId) {
      $fb = <<<EOF
      Asset.javascript('https://connect.facebook.net/en_EN/all.js#xfbml=1&appId=$fbAppId', {
  onLoad: function(){
    FB.Event.subscribe('edge.create',
      function(href, widget) {
        Avatarstyler.like('facebook');
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