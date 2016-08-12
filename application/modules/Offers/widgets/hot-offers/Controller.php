<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-09-13 11:42:11 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Offers_Widget_HotOffersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('offers', 'offers');
    $limit = $this->_getParam('itemCountPerPage', 5);

    $params = array('sort' => 'hot', 'limit' => $limit);
    $this->view->offers = $offers = $table->getOffersPaginator($params);
    $hotOffers = array();
    $i = 0;

    foreach($offers as $offer) {
      // if until the end of the period is less than 2 days
      $leftTime = Engine_Api::_()->offers()->availableOffer($offer);
      if ($leftTime !== 'expired' && $leftTime['days'] < 2) {
        if ($limit <= $i) {
          break;
        }
        $hotOffers[$i]['id'] = $offer->offer_id;
        $hotOffers[$i]['days_left'] = $leftTime['days'];
        $hotOffers[$i]['hours_left'] = $leftTime['hours'];
        $i++;
      }
    }
    $this->view->hotOffers = $hotOffers;

    if (!count($hotOffers)) {
      return $this->setNoRender();
    }
  }
}