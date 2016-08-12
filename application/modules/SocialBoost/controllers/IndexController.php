<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: IndexController.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class SocialBoost_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    /**
     * @var $modules Core_Model_DbTable_Modules
     * @var $settings Core_Model_DbTable_Settings
     */

    $from = $this->_getParam('from', '');

    if ($from != 'SocialBoost_Plugin_Core') {
//      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $api = Engine_Api::_()->getApi('core', 'socialBoost');

    $this->standart = 1;
    $this->view->allowOffers = $api->isOfferAllowed();
    $this->view->allowCredits = $api->isCreditAllowed();
    $this->view->isNewsletter = $api->isSubscribeAllowed();

    $this->view->serverHost = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->baseUrl(), '/') . '/';

    $this->view->fbAppId = $settings->getSetting('socialboost.facebook.app.id', false);
    $this->view->facebook = $settings->getSetting('socialboost.admin.facebook', false);
    $this->view->twitter = $settings->getSetting('socialboost.admin.twitter', false);
    $this->view->google = $settings->getSetting('socialboost.admin.google', false);
  }

  public function subscribeAction()
  {
    $email = $this->_getParam('email');

    $validate = new Zend_Validate_EmailAddress();

    if (!$validate->isValid($email)) {
      $this->view->message = $this->view->translate('Invalid email address.');
      $this->view->status = false;
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $type = Engine_Api::_()->getApi('core', 'socialBoost')->getPopupType();

    $subscriberTb = $this->_helper->api()->getDbtable('subscribers', 'updates');

    if ($subscriberTb->getSubscriber(0, $email)) {
      $this->view->status = false;
      $this->view->message = $this->view->translate('This email address already added.');
      return;
    }

    $subscriber = $subscriberTb->createRow();
    $subscriber->email_address = $email;
    $subscriber->name = $viewer->username;
    $subscriber->user_id = $viewer->getIdentity();
    $subscriber->save();

    $api = Engine_Api::_()->getApi('core', 'socialBoost');
    $allowOffers = $api->isOfferAllowed();
    $allowCredits = $api->isCreditAllowed();
    if ($allowCredits) {
      $this->giveCredit('subscribe');
    }
    if ($allowOffers) {
      $this->giveOffer('subscribe');
    }

    $this->view->status = true;
    $this->view->message = $this->view->translate('Thank you very much!');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $settings->setSetting('socialboost.user.like.'.$viewer->getIdentity(), 1);
  }

  public function likeAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $type = $this->_getParam('type');

    if ($this->_getParam('format', 0) != 'json') {
      $this->view->status = false;
      return;
    }

    $api = Engine_Api::_()->getApi('core', 'socialBoost');
    $allowOffers = $api->isOfferAllowed();
    $allowCredits = $api->isCreditAllowed();
    if ($allowCredits) {
      $this->giveCredit($type);
    }
    if ($allowOffers) {
      $this->giveOffer($type);
    }

    $this->view->message = $this->view->translate('Thank you very much!');
    $this->view->status = true;

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $settings->setSetting('socialboost.user.like.'.$viewer->getIdentity(), 1);
  }

  protected function giveCredit($type)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $dataTbl = Engine_Api::_()->getDbTable('datas', 'socialBoost');

    $creditApi = Engine_Api::_()->credit();
    $amount = $settings->getSetting('socialboost.credit.amount', 5);
    $creditApi->giveCredits($viewer, $amount);

    $row = $dataTbl->createRow();
    $row->user_id = $viewer->getIdentity();
    $row->type = $type;
    $row->credit = $amount;
    $row->save();

    $this->view->goto_url = $this->view->url(array('action' => 'manage'), 'credit_general');
  }

  protected function giveOffer($type)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $dataTbl = Engine_Api::_()->getDbTable('datas', 'socialBoost');
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'offers');

    $offer_id = $settings->getSetting('socialboost.offer.id', 0);
    $offer = Engine_Api::_()->getItem('offer', $offer_id);

    // Giving offer
    $params = array(
      'offer_id' => $offer_id,
      'user_id' => $viewer->getIdentity(),
      'status' => 'active',
      'active' => true,
      'creation_date' => new Zend_Db_Expr('NOW()'),
    );

    if ($offer->enable_unique_code) {
      $params['coupon_code'] = Engine_Api::_()->offers()->generateCouponsCode();
    }

    $subscription = $subscriptionsTable->createRow();
    $subscription->setFromArray($params);
    $expiration = $offer->getExpirationDate();
    if ($expiration) {
      $subscription->expiration_date = $expiration;
    }
    $offer->decreaseCouponsCount();
    $subscription->save();

    $activity = Engine_Api::_()->getDbTable('actions', 'activity');
    $activity->addActivity($viewer, $offer, 'offers_accept');

    Engine_Api::_()->getApi('mail', 'core')->sendSystemRaw($viewer, 'offers_subscription_active', array(
      'subscription_title' => $offer->title,
      'subscription_description' => $offer->description,
      'subscription_terms' => $offer->getOfferDescription('active'),
      'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
        Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
    ));

    $row = $dataTbl->createRow();
    $row->user_id = $viewer->getIdentity();
    $row->type = $type;
    $row->offer_id = $offer_id;
    $row->save();

    $this->view->goto_url = $offer->getHref();
  }
}
