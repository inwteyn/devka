<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GetEmailContent.php 7244 2012-10-01 12:44:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_View_Helper_GetEmailContent extends Zend_View_Helper_Abstract
{
  public function getEmailContent($offer_id, $user_id = false)
  {
    $offerTbl = Engine_Api::_()->getDbTable('offers', 'offers');
    $contactsTbl = Engine_Api::_()->getDbTable('contacts', 'offers');

    $select = $offerTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('o'=>$offerTbl->info('name')))
      ->joinLeft(array('c'=>$contactsTbl->info('name')), 'o.offer_id = c.offer_id', array('c.country','c.state','c.city','c.address','c.phone', 'c.website'))
      ->where('o.offer_id = ?', $offer_id);

    $isPageModuleEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page');
    if ($isPageModuleEnabled) {
      $pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');
      $select->joinLeft(array('p'=>$pagesTbl->info('name')), 'o.page_id = p.page_id', array('p.title AS page_title'));
    }
    $offer = $offerTbl->fetchRow($select);
    $currentDate = date('Y-m-d h:i:s', strtotime(Engine_Api::_()->offers()->getDatetime()));

    $offerPhotoUrl = (substr($offer->getPhotoUrl('thumb.normal'), 0, 4) == 'http')
      ? $offer->getPhotoUrl('thumb.normal')
      : 'http://' . $_SERVER['HTTP_HOST'] . $offer->getPhotoUrl('thumb.normal');

    $emailContent = '<div style="border:1px dashed #cccccc; padding: 10px; width: 660px;">
      <table cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td>
            <a href="http://' . $_SERVER['HTTP_HOST'] . $offer->getHref() .'" style="border:medium none;height:200px;width:100%">
              <img src="' . $offerPhotoUrl . '" width="220" height="200" border="0"/>
            </a>
          </td>
          <td valign="top">
            <table style="margin-left: 12px; line-height: 18px;" cellspacing="0" cellpadding="0" border="0">
              <tr>
                <td>
                  <h3 style="margin-bottom: 12px">
                    <a href="http://' . $_SERVER['HTTP_HOST'] . $offer->getHref() . '" style="color: #5F93B4; text-decoration: none;">'. $offer->getTitle() .'</a>
                  </h3>
                </td>
              </tr>
              <tr>
                <td>
                  <span>'. $this->view->translate("OFFERS_offer_discount") . '</span>
                  <span style="font-weight: bold">' . $offer->discount;
    if ($offer->discount_type == "percent") {
      $emailContent .= '%';
    }
    $emailContent .= '</span>
                </td>
              </tr>
              <tr>
                <td>
                  <span>'.$this->view->translate("OFFERS_offer_coupon_code").'</span>
                  <span style="font-weight: bold">'.$offer->getCouponCode($user_id).'</span>
                </td>
              </tr>
              <tr>
                <td>
                  <span>' . $this->view->translate("OFFERS_offer_available") . ' </span>
                  <span style="font-weight: bold">' . (($offer->coupons_unlimit) ? $this->view->translate("OFFERS_Unlimit") : $this->view->translate('%s coupons', $offer->coupons_count)) . '</span>
                </td>
              </tr>
              <tr>
                <td>
                  <span>' . $this->view->translate("OFFERS_Redeem"). '</span>
                  <span style="font-weight: bold">' . Engine_Api::_()->offers()->timeInterval($offer) .'</span>
                </td>
              </tr>
              <tr>
                <td>';
    if ($offer->page_id) {
      $emailContent .= '<span>' . $this->view->translate("OFFERS_Presented by") . '</span>
                    <span style="font-weight: bold">' . $offer->page_title . '</span>';
    }
    $emailContent .= '
                </td>
              </tr>
              <tr>
                <td>';
    if ($currentDate >= $offer->endtime && $offer->time_limit == "limit") {
      $emailContent .= '<span>' . $this->view->translate("OFFERS_Status") .'</span>
                    <span style="font-weight: bold; color: #CC1A1A;"> ' . $this->view->translate("OFFERS_Expired") .'</span>';
    }
    $emailContent .= '
                </td>
              </tr>
              <tr>
                <td>';
    if($offer->address || $offer->city || $offer->state || $offer->country || $offer->phone || $offer->website) {
      $emailContent .= '
                    <div style="background: none repeat scroll 0 0 #E9F4FA;border: 1px solid #D0E2EC;border-radius: 3px 3px 3px 3px;float: left;margin-top: 10px;padding: 6px;width: 400px;">
                      <div style="font-size: 0.8em;padding-bottom: 4px;">
                        <span style="float: left; margin-right: 5px;">
                          <img src="http://' . $_SERVER['HTTP_HOST'] . $this->view->baseUrl() . '/application/modules/Offers/externals/images/map.png">
                        </span>
                        ' . $offer->address.", ".$offer->city.", ".$offer->state.", ".$offer->country . '
                      </div>
                      <div style="font-size: 0.8em;padding-bottom: 4px;">
                        <span style="float: left; margin-right: 5px;">
                          <img src="http://'. $_SERVER['HTTP_HOST'] . $this->view->baseUrl() .'/application/modules/Offers/externals/images/phone.png">
                        </span>
                        ' . $offer->phone . '
                      </div>
                      <div style="font-size: 0.8em;padding-bottom: 4px; margin-top: -1px">
                        <span style="float: left; margin-right: 5px; margin-top: 2px">
                          <img src="http://' . $_SERVER['HTTP_HOST'] . $this->view->baseUrl() . '/application/modules/Offers/externals/images/website.png">
                        </span>
                        ' . $offer->website . '
                      </div>
                    </div>';
    }
    $emailContent .= '
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>';

    return $emailContent;
  }
}