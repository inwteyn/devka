<?php

class Inviter_Plugin_Hotmail
{
  private $clientId = '';
  private $clientSecret = '';

  private $accessTokenUrl = 'https://login.live.com/oauth20_token.srf';
  private $signoutUrl = 'https://login.live.com/oauth20_logout.srf';

  private $authUrl = 'https://login.live.com/oauth20_authorize.srf';
  private $restUrl = 'https://apis.live.net/v5.0/';

  private $accessToken = null;
  private $authenticationToken = null;

  private $scope = null;

  public function init()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->clientId = $settings->getSetting('inviter.hotmail.consumer.key');
    $this->clientSecret = $settings->getSetting('inviter.hotmail.consumer.secret');

    $this->scope = "wl.signin wl.basic wl.emails";
    $this->scope = rawurlencode($this->scope);
  }

  public function getAuthUrl($redirectUrl = null)
  {
    $redirectUrl = strtolower(rawurlencode($redirectUrl));
    $url = $this->authUrl . "?client_id={$this->clientId}&scope={$this->scope}&response_type=code&redirect_uri={$redirectUrl}";
    return $url;
  }

  public function getAccessToken($code = null, $redirectUrl = null)
  {
    if (!$code)
      return false;
    $url = $this->accessTokenUrl;

    $params = array(
      'client_id' => $this->clientId,
      'redirect_uri' => strtolower(rawurlencode($redirectUrl)),
      'client_secret' => $this->clientSecret,
      'code' => $code,
      'grant_type' => 'authorization_code'
    );

    $params = $this->prepareParams($params);

    $response = $this->request($url, 'post', $params);

    $this->accessToken = $response->access_token;
    $this->authenticationToken = $response->authentication_token;
    return $this->accessToken;
  }

  public function getUserInfo($token = null)
  {
    if (!$token || $token != $this->accessToken)
      return array();

    $url = $this->restUrl . 'me' . "?access_token={$token}";
    $response = $this->request($url);

    $info['oauth_token'] = $this->accessToken;
    $info['oauth_token_secret'] = $this->authenticationToken;
    $info['object_id'] = $response->id;
    $info['object_name'] = $response->name;
    $info['expiration_date'] = $sent_date = new Zend_Db_Expr('NOW() + 3600');

    return $info;
  }

  public function getContacts($token = null)
  {
    $contacts = array();

    $this->accessToken = $accessToken = $token->oauth_token;
    $url = "https://apis.live.net/v5.0/" . "me/contacts?access_token={$accessToken}";

    $response = $this->request($url);
    $contacts = $response->data;
    return $contacts;
  }

  public function getUserEmail($user = null) {
    if(!$user)
      return false;

    $emails = $user->emails;

    if(!$emails)
      return false;

    if($emails->preferred) {
      return $emails->preferred;
    } else if($emails->account) {
      return $emails->account;
    } elseif($emails->personal) {
      return $emails->personal;
    } elseif($emails->business) {
      return $emails->business;
    } elseif($emails->other) {
      return $emails->other;
    } else {
      return false;
    }
  }

  public function getUser($token = null, $userId = null)
  {
    if (!$token || !$userId)
      return false;

    if(!$this->accessToken) {
      $this->accessToken = $token->oauth_token;
    }

    $url = $this->restUrl . "{$userId}?access_token={$this->accessToken}";

    $response = $this->request($url);
    return $response;

  }

  public function logout($redirectUri = null)
  {
    $url = $this->signoutUrl . "?client_id={$this->clientId}&redirect_uri={$redirectUri}";
    return $url;
  }


  private function request($url = null, $method = 'get', $params = null)
  {
    $ch = curl_init();
    $options = array();
    $options[CURLOPT_URL] = $url;
    $options[CURLOPT_SSL_VERIFYPEER] = false;
    $options[CURLOPT_RETURNTRANSFER] = true;

    if ($method == 'get') {
      $options[CURLOPT_HTTPGET] = true;
    } else {
      $options[CURLOPT_CUSTOMREQUEST] = 'POST';
      $options[CURLOPT_POSTFIELDS] = $params;
    }
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $response = json_decode($response);
    return $response;
  }

  private function prepareParams($params = array())
  {
    $result = '';
    if ($params) {
      foreach ($params as $k => $v)
      {
        if ($result == '') {
          $prefix = '';
        }
        else
        {
          $prefix = '&';
        }
        $result .= $prefix . $k . '=' . $v;
      }
    }
    return $result;
  }

}

?>
