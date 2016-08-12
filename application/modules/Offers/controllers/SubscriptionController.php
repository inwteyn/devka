<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: SubscriptionController.php 28.08.12 17:51 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_SubscriptionController extends Core_Controller_Action_Standard
{
  /**
   * @var User_Model_User
   */
  protected $_user;
  
  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Offers_Model_Order
   */
  protected $_order;

  /**
   * @var Payment_Model_Gateway
   */
  protected $_gateway;

  /**
   * @var Offers_Model_Subscription
   */
  protected $_subscription;

  /**
   * @var Offers_Model_Offer
   */
  protected $_offer;
  
  public function init()
  {
    $this->_session = new Zend_Session_Namespace('Offer_Subscription');
    // Get offer
    $offerId = $this->_getParam('offer_id', $this->_session->offer_id);

    if (!$offerId || !($this->_offer = Engine_Api::_()->getItem('offer', $offerId))) {
      $this->_goBack(false);
    }

    if (!($this->_offer->getCouponsCount() || $this->_offer->coupons_unlimit)) {
      $this->_goBack();
    }

    // Get user and session
    $this->_user = Engine_Api::_()->user()->getViewer();
    if ($this->_offer->isSubscribed($this->_user)) {
      $this->_goBack();
    }

    if ($this->_offer->getOfferType() == 'reward' || $this->_offer->getOfferType() == 'store') {
      $requires = $this->_offer->getRequire();
      $require_complete = Engine_Api::_()->getDbTable('require', 'offers')->getCompleteRequireIds($this->_user, $this->_offer);
      $requireIsComplete = true;
      foreach ($requires as $item) {
        if (!in_array($item->getIdentity(), $require_complete)) {
          $requireIsComplete = false;
          break;
        }
      }
      if (!$requireIsComplete) {
        $this->_goBack();
      }
    }

    // If there are no enabled gateways, disable
    if (!Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() > 0
      && !$this->_offer->isOfferCredit()
      && $this->_offer->getPrice()
    ) {
      $this->_goBack();
    }

    // Check viewer and user
    if (!$this->_user || !$this->_user->getIdentity()) {
      if (!empty($this->_session->user_id)) {
        $this->_user = Engine_Api::_()->getItem('user', $this->_session->user_id);
      }
      // If no user, redirect to home?
      if (!$this->_user || !$this->_user->getIdentity()) {
        $this->_session->unsetAll();
        $this->_goBack();
      }
    }
  }

  public function indexAction()
  {
    return $this->_forward('choose');
  }
  
  public function chooseAction()
  {
    print_firebug('choose');
    print_firebug(Zend_Json::encode($this->_getAllParams()));
    // Check subscription status
    if ($status = $this->_checkOfferStatus()) {
      return $this->_finishPayment($status);
    }
    // Unset certain keys
    unset($this->_session->offer_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);
    unset($this->_session->errorMessage);
    
    // Process
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'offers');
    $user = $this->_user;

    // Insert the new temporary subscription
    $db = $subscriptionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $values = array(
        'offer_id' => $this->_offer->offer_id,
        'user_id' => $user->getIdentity(),
        'status' => 'initial',
        'active' => false, // Will set to active on payment success
        'creation_date' => new Zend_Db_Expr('NOW()'),
      );

      if ($this->_offer->enable_unique_code) {
        $values['coupon_code'] = Engine_Api::_()->offers()->generateCouponsCode();
      }

      $subscription = $subscriptionsTable->createRow();
      $subscription->setFromArray($values);
      $subscription->save();

      // If the offer is free, let's set it active now
      if (!$this->_offer->getPrice()) {
        $subscription->setActive(true);
        $subscription->onPaymentSuccess();
        $activity = Engine_Api::_()->getDbTable('actions', 'activity');
        $page = $this->_offer->getPage();
        if ($page) {
          $action = $activity->addActivity($this->_user, $page, 'page_offers_accept', null, array('link' => $this->_offer->getLink()));
          $activity->attachActivity($action, $this->_offer, Activity_Model_Action::ATTACH_DESCRIPTION);
          $activity->addActivity($this->_user, $this->_offer, 'offers_accept');
        } else {
          $activity->addActivity($this->_user, $this->_offer, 'offers_accept');
        }
      }

      Engine_Api::_()->getApi('mail', 'core')->sendSystemRaw($user, 'offers_subscription_active', array(
        'subscription_title' => $this->_offer->title,
        'subscription_description' => $this->_offer->description,
        'subscription_terms' => $this->_offer->getOfferDescription('active'),
        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
          Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
      ));

      $subscription_id = $subscription->subscription_id;

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    $this->_session->subscription_id = $subscription_id;

    // Otherwise redirect to the payment page
    return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
  }

  public function gatewayAction()
  {
    print_firebug('gateway');
    print_firebug(Zend_Json::encode($this->_getAllParams()));
    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if( !$subscriptionId ||
        !($subscription = Engine_Api::_()->getItem('offers_subscription', $subscriptionId))  ) {
      $this->_goBack();
    }
    $this->view->subscription = $subscription;

    // Check subscription status
    if ($status = $this->_checkOfferStatus($subscription)) {
      return $this->_finishPayment($status);
    }

    // Get subscription
    if (!$this->_user ||
        $subscription->user_id != $this->_user->getIdentity() ||
        !($offer = Engine_Api::_()->getItem('offer', $subscription->offer_id))) {
      $this->_goBack();
    }

    $this->view->offer = $offer;

    // Unset certain keys
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);

    $this->_session->offer_id = $offer->getIdentity();

    // Gateways
    if (null != ($page = $this->_offer->getPage())) {
      $gatewayTable = Engine_Api::_()->getDbtable('apis', 'offers');
      $gateways = $gatewayTable->getEnabledGateways($page->getIdentity());
    } else {
      $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
      $gatewaySelect = $gatewayTable->select()
        ->where('enabled = ?', 1);
      $gateways = $gatewayTable->fetchAll($gatewaySelect);
    }

    $gatewayPlugins = array();
    foreach( $gateways as $gateway ) {
      $gatewayPlugins[] = array(
        'gateway' => $gateway,
      );
    }
    $this->view->gateways = $gatewayPlugins;
  }

  public function processAction()
  {
    print_firebug('process');
    print_firebug(Zend_Json::encode($this->_getAllParams()));
    // Get gateway
    $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
    if (null == ($page = $this->_offer->getPage())) {
      if (!$gatewayId ||
          !($gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId)) ||
          !($gateway->enabled)) {
        return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
      }
    } else {
      if (!$gatewayId ||
          !($gateway = Engine_Api::_()->getDbTable('apis', 'offers')->getApi($page->getIdentity(), $gatewayId)) ||
          !($gateway->enabled)) {
        return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
      }
    }

    $this->view->gateway = $gateway;

    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if (!$subscriptionId ||
        !($subscription = Engine_Api::_()->getItem('offers_subscription', $subscriptionId))) {
      $this->_goBack();
    }
    $this->view->subscription = $subscription;

    // Get package
    $offer = $subscription->getOffer();

    $this->view->offer = $offer;

    // Check subscription?
    if ($status = $this->_checkOfferStatus($subscription) ) {
      return $this->_finishPayment($status);
    }

    // Process
    
    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'offers');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    $ordersTable->insert(array(
      'user_id' => $this->_user->getIdentity(),
      'gateway_id' => $gateway->gateway_id,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => 'offers_subscription',
      'source_id' => $subscription->subscription_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Unset certain keys
    unset($this->_session->offer_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);

    // Prepare host info
    $schema = 'http://';
    if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];
    

    // Prepare transaction
    $params = array();
    $params['language'] = $this->_user->language;
    $localeParts = explode('_', $this->_user->language);
    if( count($localeParts) > 1 ) {
      $params['region'] = $localeParts[1];
    }
    $params['vendor_order_id'] = $order_id;
    $params['return_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      . '&state=' . 'return';
    $params['cancel_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      . '&state=' . 'cancel';
    $params['ipn_url'] = $schema . $host
      . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'offers'))
      . '?order_id=' . $order_id;

    // Process transaction
    if ($page) {
      $api = Engine_Api::_()->getDbTable('apis', 'offers')->getApi($page->getIdentity(), $gatewayId);
      $gatewayPlugin = $api->getGateway();
      $plugin = $api->getPlugin();
    } else {
      $api = Engine_Api::_()->offers();
      $gatewayPlugin = $api->getGateway($gateway->gateway_id);
      $plugin = $api->getPlugin($gateway->gateway_id);
    }

    $transaction = $plugin->createOfferTransaction($this->_user, $subscription, $offer, $params);
    
    // Pull transaction params
    $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData = $transactionData = $transaction->getData();

    // Handle redirection
    if ($transactionMethod == 'GET') {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }

    // Post will be handled by the view script
  }

  public function returnAction()
  {
    print_firebug('return');
    print_firebug(Zend_Json::encode($this->_getAllParams()));
    // Get order
    if (!$this->_user ||
        !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
        !($order = Engine_Api::_()->getItem('offers_order', $orderId)) ||
        $order->source_type != 'offers_subscription' ||
        !($subscription = $order->getSource()) ||
        !($offer = $subscription->getOffer())) {
      $this->_goBack();
    }

    if (null == ($page = $offer->getPage())) {
      if (!($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id))) {
        $this->_goBack();
      }
    } else {
      if (!($gateway = Engine_Api::_()->getDbTable('apis', 'offers')->getApi($page->getIdentity(), $order->gateway_id))) {
        $this->_goBack();
      }
    }

    if ($page) {
      $api = Engine_Api::_()->getDbTable('apis', 'offers')->getApi($page->getIdentity(), $gateway->gateway_id);
      $gatewayPlugin = $api->getGateway();
      $plugin = $api->getPlugin();
    } else {
      $api = Engine_Api::_()->offers();
      $gatewayPlugin = $api->getGateway($gateway->gateway_id);
      $plugin = $api->getPlugin($gateway->gateway_id);
    }

    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin;

    // Process return
    unset($this->_session->errorMessage);
    try {
      $status = $plugin->onOfferTransactionReturn($order, $this->_getAllParams());
      if ($status == 'active') {
        $activity = Engine_Api::_()->getDbTable('actions', 'activity');
        $page = $this->_offer->getPage();
        if ($page) {
          $action = $activity->addActivity($this->_user, $page, 'page_offers_purchase', null, array('link' => $this->_offer->getLink()));
          $activity->attachActivity($action, $this->_offer, Activity_Model_Action::ATTACH_DESCRIPTION);
          $activity->addActivity($this->_user, $this->_offer, 'offers_purchase');
        } else {
          $activity->addActivity($this->_user, $this->_offer, 'offers_purchase');
        }
      }
    } catch( Payment_Model_Exception $e ) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }
    
    return $this->_finishPayment($status);
  }

  public function finishAction()
  {
    $this->view->status = $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
  }

  protected function _checkOfferStatus(Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if( !$this->_user ) {
      return false;
    }

    if (null === $subscription) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'offers');
      $subscription = $subscriptionsTable->fetchRow(array(
        'user_id = ?' => $this->_user->getIdentity(),
        'offer_id = ?' => $this->_offer->getIdentity(),
        'active = ?' => true,
      ));
    }

    if (!$subscription) {
      return false;
    }
    
    if ($subscription->status == 'active' || $subscription->status == 'trial') {
      return 'active';
    } else if ($subscription->status == 'pending') {
      return 'pending';
    }
    
    return false;
  }

  protected function _finishPayment($state = 'active')
  {
    // No user?
    if( !$this->_user ) {
      $this->_goBack();
    }
    
    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $userIdentity = $this->_session->user_id;
    $this->_session->unsetAll();
    $this->_session->user_id = $userIdentity;
    $this->_session->errorMessage = $errorMessage;

    if ($state == 'active' && (in_array($this->_offer->getOfferType(), array('reward', 'free', 'store')))) {
      $state = 'accept';
    }

    // Redirect
    return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
  }

  protected function _goBack($back = true)
  {
    unset($this->_session->offer_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);
    unset($this->_session->errorMessage);

    if ($back) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'offer_id' => $this->_offer->getIdentity()), 'offers_specific', true);
    }
    return $this->_helper->redirector->gotoRoute(array(), 'offers_upcoming', true);
  }
}