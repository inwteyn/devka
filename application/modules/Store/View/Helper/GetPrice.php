<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GetPrice.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_View_Helper_GetPrice extends Zend_View_Helper_Abstract
{
    public function getPrice(Core_Model_Item_Abstract $item, $cItem = null)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $multicurrency = $settings->getSetting('hestore.multicurrency.enabled', 0);
        $site_default_currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
        if (!$multicurrency || !$viewer) {
            $currency = $site_default_currency;
            $multiplier = 1;
        } else {
            $currency = Engine_Api::_()->getDbTable('settings', 'user')->getSetting($viewer, 'store-user-currency');
            $base_currency = Engine_Api::_()->getDbTable('currencies', 'store')->getCurrencyByCode($currency);
            if (!$base_currency->value || !$base_currency->enabled || $currency == $site_default_currency) {
                $multiplier = 1;
                $currency = $site_default_currency;
            } else {
                $multiplier = (double)$base_currency->value;
            }
        }
        $api = Engine_Api::_()->store();

        $price = $item->getPrice();

        if ($cItem) {
            $price = $cItem->getPrice(true);
        }

        $html = '';


        if ($cItem) {
            $html .= '<span class="store-list-price">' . @$this->view->locale()->toCurrency((double)$item->getPrice(), $currency) . '</span>&nbsp;';
        } else {
            if (isset($item->price_type) && $item->price_type == 'discount') {
                $html .= '<span class="store-list-price">' . @$this->view->locale()->toCurrency((double)$item->list_price, $currency) . '</span>&nbsp;';
            }
        }

        if ($price == 0) {
            $priceStr = $this->view->translate('Free');
        } else {
            $priceStr = @$this->view->locale()->toCurrency((double)($price * $multiplier), $currency);
        }

        $html .= '<span class="store-price">' . $priceStr . '</span>';

        if ($price && $item->isStoreCredit()) {
            $html .= '/<span class="store_credit_icon">';
            $priceStr = $api->getCredits((double)$price);
            $html .= '<span class="store-credit-price">' . $priceStr . '</span></span>';
        }

        return $html;
    }
}