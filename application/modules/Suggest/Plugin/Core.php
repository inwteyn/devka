<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Suggest_Plugin_Core
{
  public function addActivity($event)
  {
    $payload = $event->getPayload();
    $actionType = $payload['type'];
    $api = Engine_Api::_()->getApi('core', 'suggest');
    $actionTypesPairs = $api->getActionTypes();
    $actionTypes = array_values($actionTypesPairs);
    $session = new Zend_Session_Namespace();

    if (in_array($actionType, $actionTypes) || $actionType == 'quiz_take') {
      $object = $payload['object'];

      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

      $select = $actionTable->select()
        ->where('type = ?', $actionType)
        ->where('object_type = ?', $object->getType())
        ->where('object_id = ?', $object->getIdentity())
        ->where('date > ?', date('Y-m-d H:i:s', time() - 30));

      $action = $actionTable->fetchRow($select);

      $viewer = Engine_Api::_()->user()->getViewer();

      if (!$action || !$viewer->membership()->getMemberCount(true)) {
        return;
      }

      $session->suggest_type = $actionType;
      $session->object_type = $object->getType();
      $session->object_id = $object->getIdentity();
      $session->show_popup = (bool)$api->isAllowed($actionType);
    }
  }

  public function onItemDeleteAfter($event)
  {
    $payload = $event->getPayload();
    $type = $payload['type'];
    $id = $payload['identity'];

    $itemTypes = array_keys(Engine_Api::_()->suggest()->getItemTypes());
    if (in_array($type, $itemTypes)) {
      $table = Engine_Api::_()->getDbTable('suggests', 'suggest');
      $suggests = $table->fetchAll($table->getSelect(array(
        'object_type' => $type,
        'object_id' => $id
      )));

      foreach ($suggests as $suggest) {
        $suggest->delete();
      }
    }
  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    $user_id = $payload['user_id'];
    if ($user_id) {
      $table = Engine_Api::_()->getDbTable('suggests', 'suggest');
      $select = $table
        ->select()
        ->where(new Zend_Db_Expr('object_type = "user" AND object_id = ' . (int)$user_id))
        ->orWhere('from_id = ?', $user_id)
        ->orWhere('to_id = ?', $user_id);

      $suggests = $table->fetchAll($select);
      foreach ($suggests as $suggest) {
        $suggest->delete();
      }
    }
  }

  public function onRenderLayoutDefault($event)
  {
    $front = Zend_Controller_Front::getInstance();
    $view = $event->getPayload();
    $session = new Zend_Session_Namespace();

    if ($view instanceof Zend_View) {
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR
        . "modules" . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";

      $options = array(
        'm' => 'suggest',
        'c' => 'HESuggest.suggest',
        'l' => 'getSuggestItems',
        'nli' => 0,
        'ipp' => 30
      );

      if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
        $script = <<<EOL

          var internalTips = null;
          var initLikeHintTips = function(elements){
            var options = {
              url: "{$view->url(array('action' => 'show-user'), 'like_default')}",
              delay: 300,
              onShow: function(tip, element) {
                var miniTipsOptions = {
                  'htmlElement': '.he-hint-text',
                  'delay': 1,
                  'className': 'he-tip-mini',
                  'id': 'he-mini-tool-tip-id',
                  'ajax': false,
                  'visibleOnHover': false
                };

                var internals = \$(tip).getElements('.he-hint-tip-links');
                internalTips = new HETips(internals, miniTipsOptions);
                Smoothbox.bind();
              }
            };
            var thumbs = [];
            if (!elements) {
              thumbs = \$\$('.he_tip_link');
            } else {
              thumbs = elements;
            }
            var mosts_hints = new HETips(thumbs, options);
          }

          en4.core.runonce.add(function(){
            initLikeHintTips();
          });
EOL;
        $view->headScript()->appendScript($script);
      }

      $subject = null;
      $isAppendable = false;

      if (Engine_Api::_()->core()->hasSubject()) {
        if ($front->getRequest()->getParam('content') && $front->getRequest()->getParam('content_id')) {
          switch ($front->getRequest()->getParam('content')) {
            case 'blog':
              $subject_type = 'pageblog';
              break;
            case 'discussion':
              $subject_type = 'pagediscussion_pagepost';
              break;
            case 'video':
              $subject_type = 'pagevideo';
              break;
            case 'page_event':
              $subject_type = 'pageevent';
              break;
            case 'document':
              $subject_type = 'pagedocument';
              break;
            case 'review':
              $subject_type = 'pagereview';
              break;
            default:
              $subject_type = $front->getRequest()->getParam('content');
              break;
          }

          $subject = Engine_Api::_()->core()->getSubject();
          $subject_id = (int)$front->getRequest()->getParam('content_id');
          $subject_title = Engine_Api::_()->getItem($subject_type, $subject_id)->getTitle();
        } else {
          $subject = Engine_Api::_()->core()->getSubject();
          $subject_type = $subject->getType();
          $subject_id = $subject->getIdentity();
          $subject_title = $subject->getTitle();
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $type = str_replace('_', '.', $subject_type);
        $type = ($type == 'album.photo') ? 'photo' : $type;
        $options['t'] = $view->translate('suggest_popup_title_' . $subject_type . ' %s', $subject_title);
        if ($settings->getSetting('suggest.link.' . $type)) {
          $options['params'] = array(
            'suggest_type' => 'link_' . $subject_type,
            'object_type' => $subject_type,
            'object_id' => (int)$subject_id,
            'scriptpath' => $path,
            'potential' => (int)($subject_type == 'user')
          );
          $script = $view->partial('popup/init.tpl', 'suggest', array(
            'options' => $options
          ));
//          $isAppendable = true;
          $view->headScript()->appendScript($script);
        }
      }

      $list_modules = array('ynmusic', 'avp', 'advancedarticles', 'list', 'document', 'job');
      $module = $front->getRequest()->getModuleName();
      $action = $front->getRequest()->getActionName();
      $controller = $front->getRequest()->getControllerName();
      $validItem = $front->getRequest()->getModuleName() != 'suggest';
      $viewer = Engine_Api::_()->user()->getViewer();
      if ($validItem && ($action == 'view' || $action == 'timeline-view' || ($controller == 'profile' && $action == 'index') || ($controller == 'product' && $action == 'index') || in_array($module, $list_modules)) && !$viewer->isSelf($subject)) {
        $this->appendShareBox();
      }


      $view->headScript()
        ->appendFile('application/modules/Suggest/externals/scripts/core.js');

      if (isset($session->show_popup) && $session->show_popup) {
        $object = Engine_Api::_()->getItem($session->object_type, $session->object_id);

        if ($object) {
          $options['t'] = $view->translate('suggest_popup_title_' . $object->getType() . ' %s', $object->getTitle());
          $options['params'] = array(
            'suggest_type' => $session->suggest_type,
            'object_type' => $session->object_type,
            'object_id' => (int)$session->object_id,
            'scriptpath' => $path,
            'potential' => (int)($session->object_type == 'user')
          );

          if ($session->suggest_type == 'fr_sent' || $session->suggest_type == 'fr_confirm') {
            $options['timeout'] = 1000;
          } else {
            $options['timeout'] = 1000;
          }

          $script = $view->partial('popup/js.tpl', 'suggest', array(
            'options' => $options
          ));

          $view->headScript()->appendScript($script);
        }
        Engine_Api::_()->suggest()->clearSession();
      }
    }
  }

  protected function appendShareBox()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::get('Zend_View');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if (!$settings->getSetting('suggest.link.' . $subject->getType())) return;
    $app_id = $settings->getSetting('suggest.facebook.app.id', false);
    if ($app_id && $subject->getType() != 'page') {
      $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
      if (strpos($subject->getPhotoUrl(), 'http://') === 0 || strpos($subject->getPhotoUrl(), 'https://') === 0) {
        $host_url = '';
      }

      try {
        $og_app_id = $app_id;
        $og_title = $subject->getTitle() ? $subject->getTitle() : false;
        $og_type = 'website';
        $og_url = $subject->getHref() ? $host_url . $subject->getHref() : false;
        $og_image = $subject->getPhotoUrl() ? $host_url . $subject->getPhotoUrl() : false;
      } catch (Exception $e) {
      }

      echo ($og_app_id) ? '<meta property="fb:app_id" content="' . $og_app_id . '"/>' . "\n" : '';
      echo ($og_title) ? '<meta property="og:title" content="' . $og_title . '"/>' . "\n" : '';
      echo ($og_type) ? '<meta property="og:type" content="' . $og_type . '"/>' . "\n" : '';
      echo ($og_url) ? '<meta property="og:url" content="' . $og_url . '"/>' . "\n" : '';
      echo ($og_image) ? '<meta property="og:image" content="' . $og_image . '"/>' . "\n" : '';
    }

    $share = $view->partial('share/box.tpl', 'suggest', array('subject' => $subject, 'app_id' => $app_id, 'socials' => $this->getSocials()));
    $view->headScript()->appendScript($share);
  }

  private function getSocials()
  {

    $socials = array();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $url = urlencode('http://' . $request->getServer('HTTP_HOST') . $request->getRequestUri());
    $sites = array(
      'facebook' => array(
        'url' => 'http://graph.facebook.com/?id=' . $url,
        'count' => 'shares',
        'share_url' => 'https://www.facebook.com/sharer.php?u=' . $url
      ),
      'twitter' => array(
        'url' => 'http://cdn.api.twitter.com/1/urls/count.json?url=' . $url,
        'count' => 'count',
        'share_url' => 'http://twitter.com/share?&url=' . $url
      ),
      'linkedin' => array(
        'url' => 'http://www.linkedin.com/countserv/count/share?url=' . $url . '&format=json',
        'count' => 'count',
        'share_url' => 'http://www.linkedin.com/shareArticle?mini=true&amp;url=' . $url,
      )
    );
    $tmp = array();
    foreach ($sites as $key => $value) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $value['url']);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $result = (array)json_decode(curl_exec($ch));
      curl_close($ch);

      if ($key == 'facebook' && !array_key_exists('shares', $result)) {
        $result['shares'] = 0;
      }

      $tmp['count'] = (isset($result[$value['count']]) ? $result[$value['count']] : 0);
      $tmp['share_url'] = array_key_exists('share_url', $value) ? $value['share_url'] : '';

      $socials[$key] = $tmp;
    }

    $tmp['count'] = $this->get_plusones($url);
    $tmp['share_url'] = 'https://plus.google.com/share?url=' . $url;
    $socials['google-plus'] = $tmp;

    return $socials;
  }

  function get_plusones($url)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode($url) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    $curl_results = curl_exec($curl);
    curl_close($curl);
    $json = json_decode($curl_results, true);
    return isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval($json[0]['result']['metadata']['globalCounts']['count']) : 0;
  }
}