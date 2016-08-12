<?php

class Offers_View_Helper_GetPopularOffers extends Zend_View_Helper_Abstract
{
  public function GetPopularOffers()
  {
    /**
     * @var $api Offers_Api_Core
     */
    $setting_count = Engine_Api::_()->getDbTable("settings", "core")->getSetting('offers_popular_count', 3);
    $db = Engine_Db_Table::getDefaultAdapter();
    $popular = $db->query("select offer_id from engine4_offers_subscriptions as f where `status`='active'
                          and (select count(subscription_id) from engine4_offers_subscriptions where `status`='active'
                          and f.offer_id=offer_id )>".$setting_count."
                          group by offer_id")->fetchAll();
    return $popular;
  }
}