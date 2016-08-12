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
class Store_View_Helper_GetWish extends Zend_View_Helper_Abstract
{
  public function getWish(Core_Model_Item_Abstract $item)
  {
    $view = Zend_Registry::get('Zend_View');
    $viewer = Engine_Api::_()->user()->getVIewer();
    $html = '';
    if ($viewer->getIdentity()) {

      if (!$item->isWished()) {
        $html .= '<a alt="'.$view->translate('STORE_Add to Wishlist').'" title="'.$view->translate('STORE_In Wishlist').'"
         href="javascript:void(0)" style="font-size: 22px; "';
        $html .= 'class="he-glyphicon he-glyphicon-heart-empty store-add-wish-list-button wishlist_button wishlist-button-'.$item->getIdentity().'"';
        $html .= 'onclick="store_cart.product.addToWishList(1, '.$item->getIdentity().')"';
        $html .= "id='add-to-wish-list-".$item->getIdentity()."'></a>";
      } else {
        $html .= '<a alt="'.$view->translate('STORE_In Wishlist').'" title="'.$view->translate('STORE_In Wishlist').'" href="javascript:void(0)"
        style="font-size: 22px; "';
        $html .= 'class="he-glyphicon he-glyphicon-heart store-remove-wish-list-button wishlist_button wishlist-button-'.$item->getIdentity().'"';
        $html .= 'onclick="store_cart.product.removeFromWishList(1, '.$item->getIdentity().')"';
        $html .= "id='add-to-wish-list-".$item->getIdentity()."'></a>";
      }
    }

    return $html;
  }
}