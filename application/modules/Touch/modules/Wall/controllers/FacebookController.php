<?php

class Wall_FacebookController extends Touch_Controller_Action_Standard
{

  public function indexAction()
  {
    $this->view->status = false;

    $viewer = Engine_Api::_()->user()->getViewer();
    $session = new Zend_Session_Namespace("wall_service_facebook");
    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $config = array(
      'client_id' => $setting->getSetting('wall.service.facebook.clientid'),
      'client_secret' => $setting->getSetting('wall.service.facebook.clientsecret'),
    );

    $redirect_uri = Engine_Api::_()->wall()->getUrl(array());
    $redirect_uri = urlencode($redirect_uri);

    $code = $this->_getParam('code');

    if (empty($code)){

      $session->state = md5(uniqid(rand(), TRUE));

      $url = "http://www.facebook.com/dialog/oauth?client_id=". $config['client_id'] . "&redirect_uri=" . $redirect_uri . "&state=" . $session->state . "&scope=publish_stream,read_stream";
      header("Location: $url");
      exit(1);


    } else if ($session->state == $this->_getParam('state')){

      $url = "https://graph.facebook.com/oauth/access_token?client_id=" . $config['client_id'] . "&redirect_uri=" . $redirect_uri . "&client_secret=" . $config['client_secret'] . "&code=" . $code;
      $response = Engine_Api::_()->wall()->getUrlContent($url, null, true);

      $access_token = null;
      if ($response){
        $params = null;
        parse_str($response, $params);
        if ($params && isset($params['access_token'])) {
          $access_token = $params['access_token'];
        }
      }

      if (!$access_token){
        return $this->_forward('error', 'twitter', 'wall', array('format' => 'smoothbox'));
      }

      $url = 'https://graph.facebook.com/me?access_token=' . $access_token;
      $content = Engine_Api::_()->wall()->getUrlContent($url, null, true);

      if (!$content){
        return $this->_forward('error', 'twitter', 'wall', array('format' => 'smoothbox'));
      }
      $profile_info = Zend_Json::decode($content);

      if (!$profile_info || isset($profile_info['error'])){
        return $this->_forward('error', 'twitter', 'wall', array('format' => 'smoothbox'));
      }


      $table = Engine_Api::_()->getDbTable('tokens', 'wall');

      $user_id = $viewer->getIdentity();
      $object_id = (isset($profile_info['id'])) ? $profile_info['id'] : null;
      $object_name = (isset($profile_info['name'])) ? $profile_info['name'] : null;
      $provider = 'facebook';

      $select = $table->select()
        ->where('user_id = ?', $viewer->getIdentity())
        ->where('provider = ?', $provider)
        ->where('object_id = ?', $object_id);

      $tokenRow = $table->fetchRow($select);

      if (!$tokenRow){
        $tokenRow = $table->createRow();
      }

      $this->view->task = $task = $this->_getParam('task');


      $tokenRow->user_id = $user_id;
      $tokenRow->object_id = $object_id;
      $tokenRow->object_name = $object_name;
      $tokenRow->provider = $provider;
      $tokenRow->oauth_token = $access_token;
      $tokenRow->oauth_token_secret = 0;
      $tokenRow->creation_date = date('Y-m-d H:i:s');
      $tokenRow->save();

      $this->view->tokenRow = $tokenRow;

    }


  }


  public function streamAction()
  {
    $this->view->enabled = false;

    $provider = 'facebook';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token){
      return ;
    }

    if (!$service->check($token)){
      return ;
    }

    $this->view->enabled = true;

    $this->view->limit = $limit = 10;

    $params = array(
      'limit' => $limit,
    );


    if ($this->_getParam('previous')){
      $params['since'] = $this->_getParam('previous');
    } else if ($this->_getParam('next')){
      $params['until'] = $this->_getParam('next');
    }

    $this->view->viewall = $this->_getParam('viewall', false);
    $this->view->stream = $stream = $service->stream($token, $params);



    $temp_data = null;
    $next = null;
    $previous = null;

    $paging = $this->getPaging($stream);
    $this->view->next = $paging['until'];

    if ($this->_getParam('format') == 'json'){
      $this->view->html = $this->view->render('facebook/items.tpl');
      return ;
    }


  }

  public function postAction()
  {
    $this->view->enabled = false;

    $provider = 'facebook';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token){
      return ;
    }
    if (!$service->check($token)){
      return ;
    }
    $this->view->enabled = true;
    $service->postStatus($token, $this->_getParam('body'));

  }

  protected function getPaging($stream)
  {
    $until = null;
    $since = null;

    if (!empty($stream['paging'])){
      $matches = null;

      if (!empty($stream['paging']['previous'])){
        preg_match('/since=([0-9]*)/i', $stream['paging']['previous'], $matches);
        $since = (isset($matches[1])) ? $matches[1] : null;
      }
      if (!empty($stream['paging']['next'])){
        preg_match('/until=([0-9]*)/i', $stream['paging']['next'], $matches);
        $until = (isset($matches[1])) ? $matches[1] : null;
      }
    }
    return array(
      'until' => $until,
      'since' => $since
    );
  }

  public function errorAction()
  {
  }

}