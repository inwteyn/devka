<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Subscription.php 28.08.12 17:51 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_Model_Subscription extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  protected $_modifiedTriggers = false;

  protected $_user;

  protected $_gateway;

  protected $_offer;

  protected $_statusChanged;

  public function getUser()
  {
    if (empty($this->user_id)) {
      return null;
    }
    if (null === $this->_user) {
      $this->_user = Engine_Api::_()->getItem('user', $this->user_id);
    }
    return $this->_user;
  }

  public function getGateway()
  {
    if (empty($this->gateway_id)) {
      return null;
    }
    if (null === $this->_gateway) {
      $this->_gateway = Engine_Api::_()->getItem('payment_gateway', $this->gateway_id);
    }
    return $this->_gateway;
  }

  public function getOffer()
  {
    if (empty($this->offer_id)) {
      return null;
    }
    if (null === $this->_offer) {
      $this->_offer = Engine_Api::_()->getItem('offer', $this->offer_id);
    }
    return $this->_offer;
  }

  public function cancel()
  {
    // Try to cancel recurring payments in the gateway
    if (!empty($this->gateway_id) && !empty($this->gateway_profile_id)) {
      try {
        $gateway = Engine_Api::_()->getItem('payment_gateway', $this->gateway_id);
        if ($gateway) {
          $gatewayPlugin = $gateway->getPlugin();
          if (method_exists($gatewayPlugin, 'cancelSubscription')) {
            $gatewayPlugin->cancelSubscription($this->gateway_profile_id);
          }
        }
      } catch (Exception $e) {
        // Silence?
      }
    }
    // Cancel this row
    $this->active = false; // Need to do this to prevent clearing the user's session
    $this->onCancel();
    return $this;
  }


  // Active

  public function setActive($flag = true, $deactivateOthers = null)
  {
    $this->active = true;

    if ((true === $flag && null === $deactivateOthers) ||
      $deactivateOthers === true
    ) {
      $table = $this->getTable();
      $select = $table->select()
        ->where('user_id = ?', $this->user_id)
        ->where('active = ?', true);
      foreach ($table->fetchAll($select) as $otherSubscription) {
        $otherSubscription->setActive(false);
      }
    }

    $this->save();
    return $this;
  }

  public function clearStatusChanged()
  {
    $this->_statusChanged = null;
    return $this;
  }

  public function didStatusChange()
  {
    return (bool)$this->_statusChanged;
  }

  public function onPaymentSuccess()
  {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active'))) {

      // If the subscription is in initial or pending, set as active and
      // cancel any other active subscriptions
      if (in_array($this->status, array('initial', 'pending'))) {
        $this->setActive(true);
      }

      // Update expiration to expiration + recurrence or to now + recurrence?
      $offer = $this->getOffer();
      $expiration = $offer->getExpirationDate();
      if ($expiration) {
        $this->expiration_date = $expiration;
      }

      // Change status
      if ($this->status != 'active') {
        $this->status = 'active';
        $this->_statusChanged = true;
      }

      // Update user if active
      if ($this->active) {
        $offer->decreaseCouponsCount();
      }
    }
    $this->save();

    return $this;
  }

  public function onPaymentPending()
  {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active'))) {
      // Change status
      if ($this->status != 'pending') {
        $this->status = 'pending';
        $this->_statusChanged = true;
      }
    }
    $this->save();

    return $this;
  }

  public function onPaymentFailure()
  {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue'))) {
      // Change status
      if ($this->status != 'overdue') {
        $this->status = 'overdue';
        $this->_statusChanged = true;
      }
    }
    $this->save();

    return $this;
  }

  public function onCancel()
  {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'cancelled'))) {
      // Change status
      if ($this->status != 'cancelled') {
        $this->status = 'cancelled';
        $this->_statusChanged = true;
      }
    }
    $this->save();

    return $this;
  }

  public function onExpiration()
  {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'expired'))) {
      // Change status
      if ($this->status != 'expired') {
        $this->status = 'expired';
        $this->_statusChanged = true;
      }
    }
    $this->save();

    return $this;
  }

  public function onRefund()
  {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'refunded'))) {
      // Change status
      if ($this->status != 'refunded') {
        $this->status = 'refunded';
        $this->_statusChanged = true;
      }
    }
    $this->save();

    return $this;
  }

  public function onUsed()
  {
    $this->_statusChanged = false;
    if (in_array($this->status, array('active'))) {
      // Change status
      if ($this->status != 'used') {
        $this->status = 'used';
        $this->active = 0;
        $this->_statusChanged = true;
      }
    }
    $this->save();

    return $this;
  }

  public function getCouponCode()
  {
    return $this->coupon_code;
  }
}