<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Subscriptions.php 28.08.12 17:51 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_Model_DbTable_Subscriptions extends Engine_Db_Table
{
  protected $_rowClass = 'Offers_Model_Subscription';
  
  public function check(User_Model_User $user)
  {
    $packagesTable = Engine_Api::_()->getDbtable('offers', 'offers');
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');

    // Have any gateways or packages been added yet?
    if( $gatewaysTable->getEnabledGatewayCount() <= 0 ||
        $packagesTable->getEnabledNonFreePackageCount() <= 0 ) {
      return true;
    }
    
    // Check for the user's plan
    $subscription = $this->fetchRow(array(
      'user_id = ?' => $user->getIdentity(),
      'active = ?' => true,
    ));

    // Check if there is no subscription
    if( !$subscription ) {

      // Check if they are an admin or moderator (don't require subscriptions from them)
      $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
      if( in_array($level->type, array('admin', 'moderator')) ) {
        return true;
      }

      return null;
    }

    // Subscription is active
    if( $subscription->status == 'active' || $subscription->status == 'trial' ) {
      return true;
    }

    // Get the package
    $package = $packagesTable->find($subscription->package_id)
      ->current();

    // If there is no plan, return null
    if( !$package ) {
      return null;
    }

    // If this is a free plan, return true
    if( $package->price <= 0 ) {
      return true;
    }

    // Subscription needs to be paid
    return false;
  }

  public function cancelAll(User_Model_User $user, $note = null,
      Payment_Model_Subscription $except = null)
  {
    $select = $this->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('active = ?', true)
      ;
    
    if( $except ) {
      $select->where('subscription_id != ?', $except->subscription_id);
    }
    
    foreach( $this->fetchAll($select) as $subscription ) {
      try {
        $subscription->cancel();
      } catch( Exception $e ) {
        $subscription->getGateway()->getPlugin()->getLog()
          ->log($e->__toString(), Zend_Log::ERR);
      }
    }

    return $this;
  }
  
  public function activateDefaultPlan(User_Model_User $user)
  {
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');

    // Have any gateways or packages been added yet?
    if( $gatewaysTable->getEnabledGatewayCount() <= 0 ||
        $packagesTable->getEnabledNonFreePackageCount() <= 0 ) {
      return false;
    }
    
    // See if they've had a plan before
    $hasSubscription = (bool) $this->select()
        ->from($this, new Zend_Db_Expr('TRUE'))
        ->where('user_id = ?', $user->getIdentity())
        ->limit(1)
        ->query()
        ->fetchColumn();
    if( $hasSubscription ) {
      return false;
    }
    
    // Get the default package
    $package = $packagesTable->fetchRow(array(
      '`default` = ?' => true,
      'enabled = ?' => true,
      'price <= ?' => 0,
    ));
    
    if( !$package ) {
      return false;
    }
    
    // Create the default subscription
    $subscription = $this->createRow();
    $subscription->setFromArray(array(
      'package_id' => $package->package_id,
      'user_id' => $user->getIdentity(),
      'status' => 'initial',
      'active' => false,
      'creation_date' => new Zend_Db_Expr('NOW()'),
    ));
    $subscription->save();

    // Set active
    $subscription->setActive(true);
    $subscription->onPaymentSuccess();
    
    return $subscription;
  }

  public function getSubscribe($user_id, $offer_id)
  {

    if (!$user_id) {
      $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }

    return $this->fetchRow($this->select()->where('user_id = ?', $user_id)
                ->where('offer_id = ?', $offer_id));
  }

  public function getSubscribersByOfferId($offer_id)
  {
    return $this->fetchAll($this->select()->where('offer_id = ?', $offer_id));
  }

  public function changeStatusCoupon($offer_id, $user_id)
  {
    if (!$user_id) {
      $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
    $select = $this->select()
                   ->where('offer_id = ?', $offer_id)
                   ->where('user_id = ?', $user_id);

    $subscriber = $this->fetchRow($select);
    $new_status = ($subscriber->status == 'active') ? 'used' : 'active';
    $active = ($new_status == 'used') ? 0 : 1;

    $this->update(array('status' => $new_status, 'active' => $active), array('offer_id = ?' => $offer_id, 'user_id = ?' => $user_id));

    return $new_status;
  }
}