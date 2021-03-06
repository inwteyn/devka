<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright 2009-2013 Hire-Experts Developments
 * @license    http://hire-experts.com/
 * @version    $Id: Recurring.php 2013-03-22 02:08:08Z teajay $
 * @author     TJ
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright 2009-2013 Hire-Experts Developments
 * @license    http://hire-experts.com/
 */
class Credit_Plugin_Task_Recurring extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    // Have any gateways or packages been added yet?
    if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ||
      Engine_Api::_()->getDbtable('packages', 'payment')->getEnabledNonFreePackageCount() <= 0
    ) {
      return;
    }

    $subscriptionsTable = Engine_Api::_()->getDbTable('subscriptions', 'payment');
    $subscriptions = $subscriptionsTable
      ->fetchAll(array(
        'status = ?' => 'active',
        'active = ?' => 1,
        'notes = ?' => 'credit',
        'expiration_date <= ?' => new Zend_Db_Expr('NOW()')
      ));

    foreach ($subscriptions as $subscription) {
      $package = $subscription->getPackage();
      if (!$package->recurring) {
        continue;
      }

      $settings = Engine_Api::_()->getDbTable('settings', 'core');
      $defaultPrice = $settings->getSetting('credit.default.price', 100);
      $credits = ceil($package->price * $defaultPrice);
      $user = Engine_Api::_()->user()->getViewer();

      $balance = Engine_Api::_()->getItem('credit_balance', $user->getIdentity());

      if ($package->isOneTime() || $credits > $balance->current_credit) {
        $subscription->onExpiration();
        // send notification
        if ($subscription->didStatusChange()) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_expired', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }
        return ;
      }

      if ($package->duration_type == 'forever') {
        Engine_Api::_()->credit()->buyLevel($user, (-1) * $credits, $package->getTitle());
        $subscription->onPaymentSuccess();
      } else {
        switch ($package->duration_type) {
          case 'day':
            $part = Zend_Date::DAY;
            break;
          case 'week':
            $part = Zend_Date::WEEK;
            break;
          case 'month':
            $part = Zend_Date::MONTH;
            break;
          case 'year':
            $part = Zend_Date::YEAR;
            break;
          default:
            throw new Engine_Payment_Exception('Invalid recurrence_type');
            break;
        }

        $relDate = new Zend_Date(strtotime($subscription->creation_date));
        $relDate->add((int)$package->duration, $part);

        if ($relDate->toValue() <= time()) {
          $subscription->onExpiration();
          // send notification
          if ($subscription->didStatusChange()) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_expired', array(
              'subscription_title' => $package->title,
              'subscription_description' => $package->description,
              'subscription_terms' => $package->getPackageDescription(),
              'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
          }
        } else {
          Engine_Api::_()->credit()->buyLevel($user, (-1) * $credits, $package->getTitle());
          $subscription->onPaymentSuccess();
        }
      }

      return ;
    }
  }
}