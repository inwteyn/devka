<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GetOfferPrice.php 7244 2012-09-01 01:49:53Z taalay $
 * @author     TJ
 */

/**
 * @category   Application_Core
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_View_Helper_GetOfferPrice extends Zend_View_Helper_Abstract
{
  public function getOfferPrice(Offers_Model_Offer $item )
  {
    /**
     * @var $api Offers_Api_Core
     */
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $api = Engine_Api::_()->offers();

    $html = '';
    if ($item->getPrice() == 0) {
      if ($item->getOfferType() == 'free') {
        return '<span class="offer_price_free">' . $this->view->translate('OFFERS_offer_price_free') . '</span>';
      } else if ($item->getOfferType() == 'reward') {
        return '<span class="offer_price_free">' . $this->view->translate('OFFERS_form_reward') . '</span>';
      } else if ($item->getOfferType() == 'store') {
        return '<span class="offer_price_free">' . $this->view->translate('OFFERS_form_store') . '</span>';
      }
    } else {
      $priceStr = $this->view->locale()->toCurrency((double)$item->getPrice(), $currency);
    }

    $html .= '<span class="offer-price">' . $priceStr . '</span>';

    if ($item->isOfferCredit()) {
      $html .= '/<span class="offers_credit_icon">';
      $priceStr = $api->getCredits((double)$item->getPrice());
      $html .= '<span class="offers-credit-price">' . $priceStr . '</span></span>';
    }

		return $html;
  }
}