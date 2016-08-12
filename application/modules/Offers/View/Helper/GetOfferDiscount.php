<?php

class Offers_View_Helper_GetOfferDiscount extends Zend_View_Helper_Abstract
{

  public function getOfferDiscount(Offers_Model_Offer $offer)
  {
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

    if ($offer->discount_type == 'percent') {
      return $offer->discount . '%';
    } else {
      return $this->view->locale()->toCurrency((double)$offer->discount, $currency);
    }
  }

}