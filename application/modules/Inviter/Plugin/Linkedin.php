<?php

class Inviter_Plugin_Linkedin
{

  private $client_id = '';
  private $client_secret = '';

  private $auth_url = 'https://www.linkedin.com/uas/oauth2/authorization';

  private $access_token_url = 'https://www.linkedin.com/uas/oauth2/accessToken';


  public function init()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->client_id = $settings->getSetting('inviter.linkedin.consumer.key');
    $this->client_secret = $settings->getSetting('inviter.linkedin.consumer.secret');
  }

  public function shareSomething($token)
  {
    $url = 'https://api.linkedin.com/v1/people/~/shares?format=json';
    $params = array(
      'content' => array(
        'title' => 'Test Title',
        'description' => 'Test Description',
        'submitted-url' => 'http://wasm.ru'
      ),
      'visibility' => array(
        'code' => 'connections-only'
      )
    );
    $this->request2($url, $params, $token);
  }

  public function getSomeInfo($token) {
    //:(id,first-name,skills,educations,languages,twitter-accounts,following)
    $url = "https://api.linkedin.com/v1/people/~:(id,first-name,skills,educations,languages,twitter-accounts,following)?format=json";
    $this->request3($url, array(), $token);
  }

  public function getAuthUrl($redirect_url = '')
  {
    $t = $this->auth_url;
    $redirect_url = urlencode($redirect_url);
    $state = md5(md5(time()));
    $scope = 'r_emailaddress&r_fullprofile&w_share';
    //$scope = 'w_share';
    $t .= "?response_type=code&client_id={$this->client_id}&state={$state}&redirect_uri={$redirect_url}&scope={$scope}";

    return $t;
  }

  public function getAccessToken($code = null, $redirect_url = '')
  {
    if (!$code)
      return false;
    $url = $this->access_token_url . '?client_id=' . $this->client_id
      . '&client_secret=' . $this->client_secret
      . '&grant_type=authorization_code'
      . '&redirect_uri=' . urlencode($redirect_url)
      . '&code=' . $code;
    $response = $this->request($url, 'POST');
    $token = $response->access_token;
    return $token;
  }

  public function resetAccessToken($token = null)
  {
    if (!$token)
      return false;
    $token = $token->oauth_token;
    $signouturl = 'https://foursquare.com/oauth2/oauth_token_invalidation_request';
    $url = $this->$signouturl . '?oauth_token =' . $token;
    $response = $this->request($url, 'get', 1);
    $token = $response->access_token;
    return $token;
  }

  public function getUser($token = null, $user_id = 'self')
  {
    if (!$token)
      return false;
    $url = 'https://api.foursquare.com/v2/users/' . $user_id . '?oauth_token=' . $token . '&v=' . date('Ymd');
    $response = $this->request($url);
    $user = $response->response->user;
    if ($user)
      return $user;
    return false;
  }

  public function getFriends($token = null, $user_id = 'self')
  {
    if (!$token)
      return false;
    $url = 'https://api.foursquare.com/v2/users/' . $user_id . '/friends?oauth_token=' . $token . '&v=' . date('Ymd');
    $response = $this->request($url);
    $friends = $response->response->friends->items;
    $result = array();
    $tmp = array();
    if ($friends) {
      foreach ($friends as $friend) {
        $full_user = $this->getUser($token, $friend->id);
        $fname = $friend->firstName;
        $lname = $friend->lastName;
        $tmp['id'] = $friend->id;
        $tmp['name'] = $fname . ((trim($fname) != '') ? ' ' : '') . $lname;
        $tmp['email'] = $full_user->contact->email;
        $result[] = $tmp;
      }
      return $result;
    }
    return false;
  }

  public function getUserInfo($user = null)
  {
    if (!$user)
      return false;
    $fname = $user->firstName;
    $lname = $user->lastName;
    $name = $fname . ((trim($fname) != '') ? ' ' : '') . $lname;
    $info = array();
    $info['object_id'] = $user->id;
    $info['object_name'] = $name;
    $info['oauth_token_secret'] = $this->client_secret;
    return $info;
  }

  public function getUserContactInfo($user = null)
  {
    if (!$user)
      return false;
    $contacts = array();
    return $contacts;
  }

  private function request($url = '', $method = 'get', $ttt = null)
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
    }
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);

    $response = json_decode($response);
    return $response;
  }

  private function request2($url, $params, $token)
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
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);

    print_arr($response);
  }

  private function request3($url = '', $token)
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
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    print_arr($response);
  }

}

?>
