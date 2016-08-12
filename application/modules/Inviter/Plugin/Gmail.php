<?php

class Inviter_Plugin_Gmail
{

  private $client_id = null;
  private $client_secret = null;

  private $auth_url = 'https://accounts.google.com/o/oauth2/auth';
  private $redirect_url = '';

  private $access_token_url = 'https://accounts.google.com/o/oauth2/token';

  private $profile_url = 'https://www.googleapis.com/plus/v1/people/me';
  private $contacts_url = 'https://www.google.com/m8/feeds/contacts/default/full';

  public function __construct()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->client_id = $settings->getSetting('inviter.gmail.consumer.key', ''); //'35724721021-og7h3vrjcjg3a65svd7u87koti6ke4oa.apps.googleusercontent.com';
    $this->client_secret = $settings->getSetting('inviter.gmail.consumer.secret', ''); //'KFgpWD4NmA-5-DifN9fudOQS';
  }

  public function setRedirectUrl($redirectUrl)
  {
    $this->redirect_url = $redirectUrl;
  }

  public function getAuthUrl()
  {
    $url = $this->auth_url;
    $url .= '?client_id=' . $this->client_id . '&approval_prompt=force&response_type=code&redirect_uri=' . $this->redirect_url . '&scope=profile+https://www.google.com/m8/feeds&access_type=online';
    return $url;
  }

  public function getAccessToken($redirect_url = null, $code = null)
  {
    if (!$redirect_url || !$code)
      return false;

    $params = array();
    $params['client_id'] = $this->client_id;
    $params['client_secret'] = $this->client_secret;
    $params['grant_type'] = 'authorization_code';
    $params['code'] = $code;
    $params['redirect_uri'] = $redirect_url;

    $sk = $this->request($this->access_token_url, $params);

    if (!property_exists($sk, 'error')) {
      $token_access_params = array(
        'oauth_token' => $sk->access_token,
        'oauth_token_secret' => $sk->id_token,
        'expiration_date' => new Zend_Db_Expr("NOW() + {$sk->expires_in}")
      );
      return $token_access_params;
    }
    return false;
  }

  public function getAccountInfo($token = null)
  {
    if (!$token) {
      return array();
    }

    $url = $this->profile_url . '?access_token=' . $token['oauth_token'];
    $user = $this->request($url, array(), true, false);

    $result = array();

    if (is_object($user)) {
      if (!$user->error) {
        $result = array(
          'oauth_token' => $token['oauth_token'],
          'oauth_token_secret' => $token['oauth_token_secret'],
          'object_id' => $user->id,
          'object_name' => ($user->displayName) ? $user->displayName : 'empty',
          'expiration_date' => $token['expiration_date']
        );
      }
    }
    return $result;
  }

  public function getContacts($token = null)
  {
    if (!$token) {
      return '';
    }

    $url = $this->contacts_url . '?access_token=' . $token;

    $response = $this->request($url, array(), true, true);

    return $response;
  }

  private function request($url = null, $params = null, $isGet = false, $flag = false)
  {
    if (!$url)
      return false;

    $pstr = '';
    foreach ($params as $key => $value) {
      $pstr .= $key . '=' . $value . '&';
    }

    $ch = curl_init();
    $options = array();
    $options[CURLOPT_URL] = $url;
    $options[CURLOPT_SSL_VERIFYPEER] = false;
    $options[CURLOPT_RETURNTRANSFER] = true;
    if (!$isGet) {
      $options[CURLOPT_POST] = true;
      $options[CURLOPT_POSTFIELDS] = $pstr;
    }

    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    if ($flag) {
      return $response;
    }
    $response = json_decode($response);
    return $response;
  }

}
