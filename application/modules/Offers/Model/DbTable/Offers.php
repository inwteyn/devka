<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Offers.php 2012-06-08 10:50 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Model_DbTable_Offers extends Engine_Db_Table
{
  protected $_rowClass = "Offers_Model_Offer";
  protected $values = array();

  public function getOffersSelect($params = array())
  {
    $table = Engine_Api::_()->getDbTable('offers', 'offers');
    $modules = Engine_Api::_()->getDbTable('modules', 'hecore');
    $tblPrefix = $table->getTablePrefix();
    $tblName = $table->info('name');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('o' => $tblName))
      ->join(array('u' => $tblPrefix . 'users'), 'u.user_id = o.owner_id', array());
      if ($modules->isModuleEnabled('page')) {
        $select->joinLeft(array('p' => $tblPrefix . 'page_pages'), 'o.page_id = p.page_id', array('p.title AS page_title'));
      }

    if (isset($params['filter']) && !empty($params['filter'])) {
      if ($params['filter'] == 'upcoming') {
        $select->where('o.time_limit = \'unlimit\' OR CURRENT_DATE() < o.endtime')
          ->where('(o.coupons_count > 0) OR (o.coupons_unlimit = 1)')
          ->where('o.enabled = ?', 1);
      } else if ($params['filter'] == 'past') {
        $select->where('(((CURRENT_DATE() > o.endtime) AND (o.time_limit = \'limit\')) OR (o.coupons_count = 0 AND coupons_unlimit = 0))')
          ->where('o.enabled = ?', 1);
      } else if ($params['filter'] == 'mine') {
        $select->joinInner(array('s' => $tblPrefix . 'offers_subscriptions'), 'o.offer_id = s.offer_id')
          ->joinLeft(array('c' => $tblPrefix . 'offers_contacts'), 'o.offer_id = c.offer_id', array('c.country', 'c.state', 'c.city', 'c.address', 'c.phone', 'c.website'));

        if ($params['my_offers_filter'] == 'upcoming') {
          $select
            ->where('o.time_limit = \'unlimit\' OR CURRENT_DATE() < o.endtime')
            ->where('(o.coupons_count > 0) OR (o.coupons_unlimit = 1)')
            ->where('s.active = 1 AND s.status = ?', 'active')
            ->where('s.user_id = ?', $params['user_id']);
        } else if ($params['my_offers_filter'] == 'past') {
          $select->where('(o.time_limit = \'limit\' AND CURRENT_DATE() > o.endtime) OR (s.active = 0 AND s.status = ?)', 'used')
            ->where('s.user_id = ?', $params['user_id']);
        }
      } else if (isset($params['manage']) && $params['manage'] == 'manage') {
        $select->where('o.owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
      }
    }

    if (!empty($params['category_id'])) {
      $select->where('o.category_id = ?', $params['category_id']);
    }

    if (!empty($params['searchText'])) {
      $select->where("o.title LIKE '%{$params['searchText']}%'");
    }

    if (isset($params['page_id']) && !empty($params['page_id']) && $params['page_id'] > 0) {
      $select->where('o.page_id = ?', $params['page_id']);
    }

    if (isset($params['mine']) && !empty($params['mine']) && $params['mine'] > 0) {
      $select->where('o.owner_id  = ?', $params['mine']);
    }

    if (isset($params['sort']) && !empty($params['sort'])) {
      switch ($params['sort']) {
        case 'popular' :
          $select->joinLeft(array('s' => $tblPrefix . 'offers_subscriptions'), 'o.offer_id = s.offer_id', array('count_offers' => 'COUNT(s.offer_id)'))
            ->where('s.status = "active"')
            ->where('s.active = 1')
            ->group('s.offer_id')
            ->having('count_offers > 1')
            ->order('count_offers DESC')
            ->limit($params['limit']);
          break;
        case 'recent' :
          $select
            ->where('o.enabled = 1')
            ->where('(o.time_limit = "limit" AND CURRENT_DATE()  <  o.endtime) OR o.time_limit = "unlimit"')
            ->order('o.creation_date DESC')
            ->limit($params['limit']);
          break;
        case 'hot' :
          $select
            ->where('o.enabled = 1')
            ->where('CURRENT_DATE()  <  o.endtime')
            ->order('o.endtime ASC')
            ->limit($params['limit']);
          break;
      }
    }

    if (isset($params['filter']) && $params['filter'] == 'upcoming') {
      $select->order('featured DESC');
    }

    if (!empty($params['orderby'])) {
     $select->order($params['orderby'] . ' DESC');
    } else {
      $select->order('o.offer_id DESC');
    }

    /**
     *  @var $productTbl Offers_Model_DbTable_Products
     *  @var $storeProductTbl Store_Model_DbTable_Products
     */
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('store')) {
      $storeProductTbl = Engine_Api::_()->getDbTable('products', 'store');

      $productTbl = Engine_Api::_()->getDbTable('products', 'offers');
      $productSelect = $productTbl->select()
        ->from(array('offer' => $productTbl->info('name')), array('offer_id'))
        ->setIntegrityCheck(false)
        ->joinLeft(array('store' => $storeProductTbl->info('name')), 'store.product_id = offer.product_id', array())
        ->where('store.quantity = 0');
      $select->where('o.offer_id NOT IN ?', $productSelect);
    }
    return $select;
  }

  public function getOfferById($offer_id)
  {
    return $this->fetchRow($this->select()->where('offer_id = ?', $offer_id));
  }

  public function getAvailableOffer($offer_id)
  {
    $select = $this->select()
      ->where('offer_id = ?', $offer_id);
    return $this->fetchRow($select);
  }

  public function getTimeInterval($offer_id)
  {
    $tableName = $this->info('name');
    $select = $this->select()
      ->from(array($tableName), array('starttime', 'endtime'))
      ->where('offer_id = ?', $offer_id);

    return $this->fetchRow($select);
  }

  public function getOffersPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getOffersSelect($params));
    if (!empty($params['page_num'])) {
      $paginator->setCurrentPageNumber($params['page_num']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    } else {
      $paginator->setItemCountPerPage(12);
    }

    return $paginator;
  }

  public function getCount($params)
  {
    return count($this->fetchAll($this->getOffersSelect($params)));
  }

  public function setOffer($values, $viewer)
  {
    $db = $this->getAdapter();
    $db->beginTransaction();

    try {
      $offer = $this->createRow();

      $offer->setFromArray($values);

      if (!empty($values['enable_time_left'])) {
        $offer->time_limit = 'limit';
      }

      if (empty($values['enable_coupon_count'])) {
        $offer->coupons_unlimit = 1;
      }

      if ($values['type_code'] == 'unique_code') {
        $offer->enable_unique_code = 1;
      }

      $offer->owner_id = $viewer;

      if (!is_array($values['file'])) {
        $values['file'] = explode(' ', trim($values['file']));
      }

      $offer_id = $offer->save();

      // Add Photos
      if ($values['file'][0] != '') {
        $offer->photo_id = $values['file'][0];
        foreach ($values['file'] as $photo_id) {
          $photo = Engine_Api::_()->getItem("offersphoto", $photo_id);
          $photo->collection_id = $offer_id;
          $photo->save();
        }
      }

      // Add Products
      if ($values['type'] == 'store') {
        $this->setProductsOffer($values['products_ids'], $offer_id, $values['page_id']);
      }

      // Add Require
      if ($values['type'] == 'reward' || $values['type'] == 'store') {
        if (isset($values['require'])) {
          $this->setRequiresOffer($values['require'], $offer_id, $values['page_id']);
        }
      }
      $db->commit();
      return $offer_id;
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function editOffer($values, $offer_id)
  {
    try {
      $db = $this->getAdapter();
      $db->beginTransaction();

      $offer = Engine_Api::_()->getItem('offer', $offer_id);

      $offer->setFromArray($values);

      if (!empty($values['enable_time_left'])) {
        $offer->time_limit = 'limit';
      }

      if (empty($values['enable_coupon_count'])) {
        $offer->coupons_unlimit = 1;
        $offer->coupons_count = 0;
      }

      if ($values['type_code'] == 'unique_code') {
        $offer->enable_unique_code = 1;
        $offer->coupons_code = '';
      } else {
        $offer->enable_unique_code = 0;
        if (empty($values['coupons_code'])) {
          $offer->coupons_code = Engine_Api::_()->offers()->generateCouponsCode();
        } else {
          $offer->coupons_code = $values['coupons_code'];
        }
      }


      $offer->save();

      // Edit products
      if ($values['type'] == 'store') {
        $this->setProductsOffer($values['products_ids'], $offer_id, $values['page_id']);
      }

      // Edit requires
      if ($values['type'] == 'reward' || $values['type'] == 'store') {
        if (isset($values['require'])) {
          $this->setRequiresOffer($values['require'], $offer_id, $values['page_id']);
        }
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      return false;
    }
  }

  public function deleteOffer($id) {
    if (is_array($id)) {
      $this->delete(array('offer_id IN(?)' => $id));
    } else {
      $this->delete(array('offer_id = ?' => $id));
    }

  }


  private function setProductsOffer($products_ids, $offer_id, $page_id = 0)
  {
    Engine_Api::_()->getDbTable('products', 'offers')->setProducts($products_ids, $offer_id, $page_id);
  }

  private function setRequiresOffer($list_requires, $offer_id, $page_id = 0)
  {
    $checkedRequires = array();
    foreach ($list_requires as $key => $value) {
      if ($value && $value > 0) {
        $checkedRequires[] = $key;
      }
    }

    $requires = array();
    foreach ($list_requires as $key => $value) {
      foreach ($checkedRequires as $require)
        if ($key == $require) {
          $requires[$require] = $value;
        }
    }
    $offer = Engine_Api::_()->getItem('offer', $offer_id);
    $offer->setRequire($requires, $offer_id, $page_id);
  }

  public function getMyUpcomingStoreOffers()
  {
    /**
     * @var $subscriptionsTable Offers_Model_DbTable_Subscriptions
     * @var $offersTable Offers_Model_DbTable_Offers
     * @var $viewer User_Model_User
     */
    $subscriptionsTable = Engine_Api::_()->getDbTable('subscriptions', 'offers');
    $offersTable = Engine_Api::_()->getDbTable('offers', 'offers');
    $viewer = Engine_Api::_()->user()->getViewer();

    $select = $offersTable->select()
      ->setIntegrityCheck(false)
      ->from(array('o' => $offersTable->info('name')))
      ->joinInner(array('s' => $subscriptionsTable->info('name')), 's.offer_id=o.offer_id', array())
      ->where('o.type = ?', 'store')
      ->where('s.status = ?', 'active')
      ->where('s.user_id = ?', $viewer->getIdentity());

    return $offersTable->fetchAll($select);
  }

  public function getMyUpcomingStoreOffersProductsToArray()
  {
    /**
     * @var $offer Offers_Model_Offer
     */

    $offers = $this->getMyUpcomingStoreOffers();
    if (!$offers->count()) {
      return null;
    }

    $products = array();
    foreach ($offers as $offer) {
      $products[$offer->getIdentity()] = $offer->getProductsToArray();
    }

    return $products;
  }

  public function getUsersAcceptedOffers()
  {
    $subscriptionsTbl = Engine_Api::_()->getDbTable('subscriptions', 'offers');
    $usersTbl = Engine_Api::_()->getDbTable('users', 'user');
    $select = $subscriptionsTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('s' => $subscriptionsTbl->info('name')))
      ->joinLeft(array('o' => $this->info('name')), 'o.offer_id=s.offer_id')
      ->joinLeft(array('u' => $usersTbl->info('name')), 'u.user_id=s.user_id')
      ->where('s.status = ?', 'active')
      ->where('s.active = ?', 1)
      ->where('CURRENT_DATE()  <  o.endtime')
      ->where('o.time_limit = "limit"')
      ->where('s.expiration_notified = 0');

    return $subscriptionsTbl->fetchAll($select);
  }

  public function setExpirationNotifyStatus($offer_id, $user_id)
  {
    $subscriptionsTbl = Engine_Api::_()->getDbTable('subscriptions', 'offers');
    $subscriptionsTbl->update(array('expiration_notified' => 1), array('offer_id = ?' => $offer_id, 'user_id = ?' => $user_id));
  }

  public function getPagesIdsActiveOffers()
  {
    $select = $this->select()
      ->from(array('o'=>$this->info('name')), array('page_id'))
      ->where('o.page_id <> 0')
      ->where('o.time_limit = "unlimit" OR CURRENT_DATE() < o.endtime')
      ->where('o.enabled = 1')
      ->group('o.page_id');

    return $this->fetchAll($select);
  }

  public function setDefaultCategory($category_id)
  {
    $select = $this->select()
      ->where('category_id = ?', $category_id);
    $offers = $this->fetchAll($select);

    foreach ($offers as $offer) {
      $offer->category_id = 1;
      $offer->save();
    }
  }

  public function checkCouponCodeOffer($code = null)
  {
    if (!$code) {
      return false;
    }

    if(count($this->fetchRow(array('coupons_code = ?' => $code)))) {
      return false;
    }
    return true;
  }

  public function setFeature($offer_id)
  {
    $select = $this->select()->from($this->info('name'), 'featured')->where('offer_id = ?', $offer_id);
    $status_featured = $this->fetchRow($select);
    $this->update(array('featured' => (!$status_featured->featured) ? 1 : 0 ), array('offer_id = ?' => $offer_id));
  }

  public function setFavoriteOffer($offer_id, $status)
  {
    $this->update(array('favorite' => $status), array('offer_id = ?' => $offer_id));
  }
}