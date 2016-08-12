<?php

class Store_Widget_StoreCurrencyController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
      $viewer = Engine_Api::_()->user()->getViewer();
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $multicurrency = $settings->getSetting('hestore.multicurrency.enabled', 0);
      if(!$multicurrency || !$viewer) {
          $this->setNoRender();
      } else {
          $site_default_currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
          $user_currency = Engine_Api::_()->getDbTable('settings', 'user')->getSetting($viewer, 'store-user-currency');
          $base_currency = Engine_Api::_()->getDbTable('currencies', 'store')->getCurrencyByCode($user_currency);
          $this->view->usercurrency = $currency = Engine_Api::_()->getDbTable('settings', 'user')->getSetting($viewer, 'store-user-currency');

          if (!$base_currency->value || !$base_currency->enabled || $currency == $site_default_currency) {
              $this->view->usercurrency = $site_default_currency;
          }
      }

      $this->view->currencies = Engine_Api::_()->getDbTable('currencies', 'store')->getActiveCurrencies();
  }
}