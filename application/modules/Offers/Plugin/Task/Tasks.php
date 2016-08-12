<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Send.php 2012-09-18 17:20 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Offers_Plugin_Task_Tasks extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    /**
     * @var $offersTbl Offers_Model_DbTable_Offers
     * @var $followsTbl Offers_Model_DbTable_Follows
     */

    // ------------------- Follow tasks -----------------------
    $offersTbl = Engine_Api::_()->getDbTable('offers', 'offers');
    $followsTbl = Engine_Api::_()->getDbTable('follows', 'offers');
    $follows = $followsTbl->getFollows();

    foreach ($follows as $follow) {
      $user = Engine_Api::_()->user()->getUser($follow->user_id);
      $offer = Engine_Api::_()->getItem('offer', $follow->offer_id);
      $requires = $offer->getRequire();
      $require_complete = Engine_Api::_()->getDbTable('require', 'offers')->getCompleteRequireIds($user, $offer);
      $requireIsComplete = true;
      $view = Zend_Registry::get('Zend_View');

      foreach ($requires as $item) {
        if (!in_array($item->getIdentity(), $require_complete)) {
          $requireIsComplete = false;
          break;
        }
      }
      if ($requireIsComplete) {
        // Make params
        $mail_settings = array(
          'date' => time(),
          'recipient_name' => $user->displayname,
          'recipient_link' => '<a href="http://'. $_SERVER['HTTP_HOST'] . $view->baseUrl() .$view->url(array('id' => $user->user_id), 'user_profile', true) . '">'.$user->displayname.'</a>',
          'offer_name' => '"'. $offer->title . '"',
          'link' => '<a href="http://'. $_SERVER['HTTP_HOST'] . $view->baseUrl() .$view->url(array('action' => 'view' ,'offer_id' => $follow->offer_id), 'offers_specific', true) . '">'.$offer->title.'</a>'
        );

        // send email
        Engine_Api::_()->getApi('mail', 'core')->sendSystemRaw(
          $user->email,
          'offers_follow_template',
          $mail_settings
        );

        $followsTbl->setFollowStatus($offer->offer_id, $user->user_id, 'finished');
      }
    }

    // ---------------- Expiration tasks ---------------------
    $usersAcceptedOffers = $offersTbl->getUsersAcceptedOffers();
    $currentTime = time();
    $i = 0;
    $view = Zend_Registry::get('Zend_View');

    foreach ($usersAcceptedOffers as $item) {
      $endtime = strtotime($item->endtime);
      // if until the end of the period is less than 2 days
      $different = $endtime - $currentTime;
      if ($different < 172800) {
        $leftTime = Engine_Api::_()->offers()->availableOffer($item);

        $mail_settings = array(
          'date' => time(),
          'recipient_name' => $item->displayname,
          'recipient_link' => '<a href="http://'. $_SERVER['HTTP_HOST'] . $view->baseUrl() .$view->url(array('id' => $item->user_id), 'user_profile', true) . '">'.$item->displayname.'</a>',
          'offer_name' => '"'. $item->title . '"',
          'link' => '<a href="http://'. $_SERVER['HTTP_HOST'] . $view->baseUrl() .$view->url(array('action' => 'view' ,'offer_id' => $item->offer_id), 'offers_specific', true) . '">'.$item->title.'</a>',
          'days' => $leftTime['days'],
          'hours' => $leftTime['hours'],
        );

        // send email
        Engine_Api::_()->getApi('mail', 'core')->sendSystemRaw(
          $item->email,
          'offers_expiration_template',
          $mail_settings
        );
        $i++;
        $offersTbl->setExpirationNotifyStatus($item->offer_id, $item->user_id);
      }
    }
  }

}