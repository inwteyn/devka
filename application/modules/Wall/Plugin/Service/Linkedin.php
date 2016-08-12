<?php

class Wall_Plugin_Service_Linkedin extends Wall_Plugin_Service_Abstract
{

  public function check(Wall_Model_Token $token)
  {

    try {
      $redirect_uri = Engine_Api::_()->wall()->getUrl(array('format' => 'smoothbox'));
      $setting = Engine_Api::_()->getDbTable('settings', 'core');

      $config = array(
        'requestTokenUrl' => 'https://www.linkedin.com/uas/oauth2/requestToken',
        'accessTokenUrl' => 'https://www.linkedin.com/uas/oauth2/accessToken',
        'authorizeUrl' => 'https://www.linkedin.com/uas/oauth2/authorization?client_id='.$setting->getSetting('wall.service.linkedin.consumerkey'),
        'consumerKey' => $setting->getSetting('wall.service.linkedin.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.linkedin.consumersecret'),
        'callbackUrl' => $redirect_uri,
        'client_id' => $setting->getSetting('wall.service.linkedin.consumerkey')
      );
      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $url = 'https://api.linkedin.com/v1/people/~:(id,first-name,last-name)?format=json';
      $profile_info = $this->request($url,$token->oauth_token);
      $profile_info = json_decode($profile_info);

      $this->is_enabled = (bool)$profile_info;
      return $this->is_enabled;

    } catch (Exception $e) {

    }
    $this->is_enabled = false;
    return $this->is_enabled;
  }
  private function request($url = '', $token)
  {
    $headers = array('Content-Type:application/json', "Authorization: Bearer {$token}");

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_HTTPAUTH, true);
    //curl_setopt($curl, CURLOPT_POST, true);
    //curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    //curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    // curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    return $response;
  }

  public function postAction(Wall_Model_Token $token, Activity_Model_Action $action, User_Model_User $user)
  {
    try {


      $fields = Engine_Api::_()->wall()->getActionFields($action);


      $setting = Engine_Api::_()->getDbTable('settings', 'core');

      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.linkedin.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.linkedin.consumersecret'),
      );
      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $data = array(
        'comment' => $fields['message'],
        'content' => array(
          'title' => $fields['title'] ? $fields['title'] : ' ',
          'description' => $fields['description'] ? $fields['description'] : ' ',
          'submitted-url' => $fields['link'] ? $fields['link'] : ' ',
          'submitted-image-url' => $fields['picture'] ? $fields['picture'] : ' ',
        ),
        'visibility' => array(
        'code' => 'anyone'
      )
      );
      $url = 'https://api.linkedin.com/v1/people/~/shares?format=json';
      $profile_info = $this->requestPost($url,$token->oauth_token,$data);


      return $profile_info;

    } catch (Exception $e) { }

    return false;

  }


  private function requestPost($url = '', $token,$data)
  {
    $headers = array('Content-Type:application/json', "Authorization: Bearer {$token}");

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_HTTPAUTH, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    // curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    return $response;
  }
  public function addComment(Wall_Model_Token $token, $key, $message)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');


      $config = array(
        'requestTokenUrl' => 'https://api.linkedin.com/uas/oauth/requestToken',
        'accessTokenUrl' => 'https://api.linkedin.com/uas/oauth/accessToken',
        'authorizeUrl' => 'https://api.linkedin.com/uas/oauth/authorize',
        'consumerKey' => $setting->getSetting('wall.service.linkedin.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.linkedin.consumersecret'),
      );


      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);


      $client = $access->getHttpClient($config);
      $client->setUri('http://api.linkedin.com/v1/people/~/network/updates/key='.$key.'/update-comments');
      $client->setMethod(Zend_Http_Client::POST);
      $client->setHeaders('Content-Type', 'text/xml');
      $client->setHeaders('charset', 'UTF-8');

      $xml = "<?xml version='1.0' encoding='UTF-8'?>
<update-comment>
  <comment>".$message."</comment>
</update-comment>";

      $client->setRawData($xml);

      $client->request()->getBody();

      return true;

    } catch (Exception $e) {}

    return ;
  }

  public function getAction(Wall_Model_Token $token, $key)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');


      $config = array(
        'requestTokenUrl' => 'https://api.linkedin.com/uas/oauth/requestToken',
        'accessTokenUrl' => 'https://api.linkedin.com/uas/oauth/accessToken',
        'authorizeUrl' => 'https://api.linkedin.com/uas/oauth/authorize',
        'consumerKey' => $setting->getSetting('wall.service.linkedin.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.linkedin.consumersecret'),
      );


      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);


      $client = $access->getHttpClient($config);
      $client->setUri('http://api.linkedin.com/v1/people/~/network/updates/key='.$key.'');
      $client->setParameterGet('format', 'json');
      $client->setMethod(Zend_Http_Client::GET);

      $body = $client->request()->getBody();

      if (!$body){
        return ;
      }
      $body = Zend_Json::decode($body);
      if (!$body){
        return ;
      }

      return $body;

    } catch (Exception $e) {}

    return ;
  }

  public function like(Wall_Model_Token $token, $key)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');


      $config = array(
        'requestTokenUrl' => 'https://api.linkedin.com/uas/oauth/requestToken',
        'accessTokenUrl' => 'https://api.linkedin.com/uas/oauth/accessToken',
        'authorizeUrl' => 'https://api.linkedin.com/uas/oauth/authorize',
        'consumerKey' => $setting->getSetting('wall.service.linkedin.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.linkedin.consumersecret'),
      );


      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);


      $client = $access->getHttpClient($config);
      $client->setUri('http://api.linkedin.com/v1/people/~/network/updates/key='.$key.'/is-liked');
      $client->setMethod(Zend_Http_Client::PUT);
      $client->setHeaders('Content-Type', 'text/xml');
      $client->setHeaders('charset', 'UTF-8');

      $xml = "<?xml version='1.0' encoding='UTF-8'?><is-liked>true</is-liked>";

      $client->setRawData($xml);

      $body = $client->request()->getBody();

      return true;

    } catch (Exception $e) {}

    return ;
  }


  public function unlike(Wall_Model_Token $token, $key)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');


      $config = array(
        'requestTokenUrl' => 'https://api.linkedin.com/uas/oauth/requestToken',
        'accessTokenUrl' => 'https://api.linkedin.com/uas/oauth/accessToken',
        'authorizeUrl' => 'https://api.linkedin.com/uas/oauth/authorize',
        'consumerKey' => $setting->getSetting('wall.service.linkedin.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.linkedin.consumersecret'),
      );


      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);


      $client = $access->getHttpClient($config);
      $client->setUri('http://api.linkedin.com/v1/people/~/network/updates/key='.$key.'/is-liked');
      $client->setMethod(Zend_Http_Client::PUT);
      $client->setHeaders('Content-Type', 'text/xml');
      $client->setHeaders('charset', 'UTF-8');

      $xml = "<?xml version='1.0' encoding='UTF-8'?><is-liked>false</is-liked>";

      $client->setRawData($xml);

      $body = $client->request()->getBody();

      return true;

    } catch (Exception $e) {}

    return ;
  }




  public function stream(Wall_Model_Token $token, $params = array())
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');


      $config = array(
        'requestTokenUrl' => 'https://api.linkedin.com/uas/oauth/requestToken',
        'accessTokenUrl' => 'https://api.linkedin.com/uas/oauth/accessToken',
        'authorizeUrl' => 'https://api.linkedin.com/uas/oauth/authorize',
        'consumerKey' => $setting->getSetting('wall.service.linkedin.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.linkedin.consumersecret'),
      );


      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);


      $limit = (!empty($params['limit'])) ? $params['limit'] : 10;


      $client = $access->getHttpClient($config);
      $client->setUri('http://api.linkedin.com/v1/people/~/network/updates');
      $client->setParameterGet('format', 'json');
      //$client->setParameterGet('type', array('ANSW', 'APPS', 'CMPY', 'CONN', 'JGRP', 'PICT', 'PRFX', 'RECU', 'PRFU', 'SHAR', 'STAT'));
      $client->setMethod(Zend_Http_Client::GET);

      foreach ($params as $key => $value){
        $client->setParameterGet($key, $value);
      }
      $client->setParameterGet('count', 10);
      $updates = $this->decode($client->request()->getBody());



      $client = $access->getHttpClient($config);
      $client->setUri('http://api.linkedin.com/v1/people/~/network/updates');
      $client->setParameterGet('format', 'json');
      $client->setParameterGet('scope', 'self');
      //$client->setParameterGet('type', array('ANSW', 'APPS', 'CMPY', 'CONN', 'JGRP', 'PICT', 'PRFX', 'RECU', 'PRFU', 'SHAR', 'STAT'));
      $client->setMethod(Zend_Http_Client::GET);

      foreach ($params as $key => $value){
        $client->setParameterGet($key, $value);
      }

      $client->setParameterGet('count', 10);
      $user_updates = $this->decode($client->request()->getBody());





      $total = 0;
      $values = array();

      if (!empty($updates['values'])){
        foreach ($updates['values'] as $item){
          $values[] = $item;
          $total++;
        }
      }
      if (!empty($user_updates['values'])){
        foreach ($user_updates['values'] as $item){
          $values[] = $item;
          $total++;
        }
      }

      usort($values, array($this, "sortActivity"));


      $data = array(
        '_total' => $total,
        'values' => array()
      );

      $counter = 0;
      foreach ($values as $item){

        $data['values'][] = $item;

        if ($counter == $limit){
          break ;
        }

        $counter++;

      }

      return $data;

    } catch (Exception $e) {}

    return ;
  }


  protected  function sortActivity($a, $b)
  {
    return $a['timestamp'] < $b['timestamp'];
  }


  protected function decode($body)
  {
    if (empty($body)){
      return ;
    }
    $body = Zend_Json::decode($body);
    if (empty($body)){
      return ;
    }
    return $body;
  }

  public function postStatus(Wall_Model_Token $token, $message)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');

      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.linkedin.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.linkedin.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);



      $client = $access->getHttpClient($config);
      $client->setUri('http://api.linkedin.com/v1/people/~/shares');
      $client->setMethod(Zend_Http_Client::POST);
      $client->setHeaders('Content-Type', 'text/xml');
      $client->setHeaders('charset', 'UTF-8');


      $xml = '<?xml version="1.0" encoding="UTF-8"?>
<share>
  <comment>'.$message.'</comment>
  <visibility>
	 <code>anyone</code>
  </visibility>
</share>';

      $client->setRawData($xml);



    Engine_Hooks_Dispatcher::getInstance()
      ->callEvent('onWallPostStatus', array(
       'name' => $this->getName(),
       'token' => $token
     ));



      return $client->request()->getBody();

    } catch (Exception $e) { }

    return false;

  }


}