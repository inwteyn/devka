<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: FacebookController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Wall_SoundcloudController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {
    $client = Engine_Api::_()->wall()->getServiceClass('soundcloud');
    $session = new Zend_Session_Namespace("wall_service_soundcloud_token");
    // redirect user to authorize URL
    $redirect_uri = Engine_Api::_()->wall()->getUrl(array('' => ''));
    $client->setRedirectUri($redirect_uri);
    if($this->_getParam('code')){
      $access_token = $client->accessToken($this->_getParam('code'));
      $session->token = $access_token['access_token'];

    }
    if($this->_getParam('error')){
      die('error');
    }

    if(!$this->_getParam('code') && !$this->_getParam('error')) {
      header("Location: " . $client->getAuthorizeUrl());
    }
    $this->view->token = $access_token['access_token'];
  }




}