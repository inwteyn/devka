<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Offer.php 2012-06-08 11:14 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Model_Offer extends Core_Model_Item_Collection
{
  protected $_type = 'offer';

  protected $_collectible_type = "offersphoto";

  public function setPhoto($photo, $offer_id)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Event_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
     'parent_id' => $offer_id,
     'parent_type'=>'offer'
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $mainPath = $path . '/m_' . $name;
    $image = Engine_Image::factory();
    $image->open($file)
      //->resize(175, 200)
      ->resize(230, 300)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . '/in_' . $name;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(180, 230)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $iconPath = $path . '/is_' . $name;
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();

    // Store
    try {
      $iMain = $storage->create($path.'/m_'.$name, $params);
      $iIconNormal = $storage->create($path.'/in_'.$name, $params);
      $iSquare = $storage->create($path.'/is_'.$name, $params);

      $iMain->bridge($iIconNormal, 'thumb.normal');
      $iMain->bridge($iSquare, 'thumb.icon');
    } catch( Exception $e ) {
      // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
      @unlink($iconPath);
      // Throw
      if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    // Remove temp files
    @unlink($mainPath);
    @unlink($normalPath);
    @unlink($iconPath);

    // Update row
    $this->photo_id = $iMain->file_id;
    $this->save();

    return $this;
  }

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'offers_specific',
      'action' => 'view',
      'reset' => true,
      'offer_id' => $this->offer_id
    ), $params);

    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, $reset);
  }

  public function getType()
  {
    return $this->_type;
  }

  public function setRequire($data = array(), $offer_id, $page_id = 0)
  {
    if (empty($data)) {
      return ;
    }

    $table = Engine_Api::_()->getDbTable('require', 'offers');

    foreach ($table->fetchAll(array('offer_id = ?' => $offer_id)) as $item) {
      if (!in_array($item->type, $data)) {
        $item->delete();
      } else {
        if ($data[$item->type] == $item['params']['count']) {
          unset($data[$item->type]);
        } else {
          $item->delete();
        }
      }
    }
    foreach ($data as $type => $item) {
      if (!is_array($item) && strpos($type, 'check_') === false) {
        $params = '{"count":"'. $item .'"}';
        $table->createRow(array('offer_id' => $offer_id, 'type' => $type, 'params' => $params, 'page_id' => $page_id))->save();
      }
    }
  }

  public function getRequire()
  {
    $table = Engine_Api::_()->getDbTable('require', 'offers');

    $place = ($this->page_id > 0) ? 'page' : 0;

    $select = $table->select()
        ->where('offer_id = ?', $this->getIdentity())
        ->where('type IN (?)', array_keys(Engine_Api::_()->offers()->getRequireList($place)));

    return $table->fetchAll($select);

  }

  public function getRequireParams($place = 0)
  {
    $table = Engine_Api::_()->getDbTable('require', 'offers');

    $require_list = Engine_Api::_()->offers()->getRequireList($place);

    $select = $table->select()
      ->where('offer_id = ?', $this->getIdentity())
      ->where('type IN (?)', array_keys($require_list));

    $data = array();
    foreach ($table->fetchAll($select) as $item) {
      if (empty($item->params)) {
        continue;
      }
      $data[$item->type] = $item->params;
    }

    return $data;
  }

  public function saveValues($params)
  {
    // getting photo_ids
    $photo_ids = array();
    foreach ($params as $key => $value) {
      if (strpos($key, 'offers_') !== false) {
        $photo_ids[] = substr($key, 7);
      }
    }

    $offersPhotosTbl = Engine_Api::_()->getDbTable('offersphotos', 'offers');

    // deleting photo
    $deletedPhoto_id = 0;
    foreach ($params as $key => $value) {
      if (strpos($key, 'offers_') !== false) {
        foreach ($photo_ids as $photo_id) {
          if ($params['offers_'.$photo_id]['delete'] == 1) {
            $offersPhotosTbl->delete(array('photo_id = ?' => $photo_id));
            $deletedPhoto_id = $photo_id;
          }
        }
      }
    }

    // changing cover
    $cover = $params['cover'];
    $offersTbl = Engine_Api::_()->getDbTable('offers', 'offers');
    $photo = Engine_Api::_()->getItem("offersphoto", $cover);
    if ($photo == null ) {
      foreach ($photo_ids as $photo_id) {
        if ($photo_id != $cover) {
          $where = array('offer_id = ?' => $params['offer_id']);
          $offersTbl->update(array('photo_id' => $photo_id), $where);
          break;
        }
      }
    }
    else {
      $offersTbl->update(array('photo_id' => $cover), array('offer_id = ?' => $params['offer_id']));
    }

    // saving title and caption
    foreach ($photo_ids as $photo_id) {
      if ($photo_id == $deletedPhoto_id) {
        continue;
      }
      $where = array('collection_id = ?' => $params['offer_id'], 'photo_id = ?' => $photo_id);
      $offersPhotosTbl->update(array('title' => $params['offers_'.$photo_id]['title'], 'description' => $params['offers_'.$photo_id]['description']), $where);
    }
  }

  public function getPrice()
  {
    if ($this->type == 'free') {
      return 0;
    }

    return $this->price_offer;
  }

  public function getOfferType()
  {
    return $this->type;
  }

  public function getOfferDescription($status = '')
  {
    $translate = Zend_Registry::get('Zend_View');
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $html = '';
    if ($this->getDiscountType() == 'percent') {
      $html .= $translate->translate('OFFERS_%s discount for %s', $this->getDiscount().'%', $this->getPrice().'('.$currency.')');
    } else {
      $html .= $translate->translate('OFFERS_%s discount for %s', $this->getDiscount().'('.$currency.')', $this->getPrice().'('.$currency.')');
    }
    if ($status == 'active') {
      $html .= "\n" . $translate->translate('OFFERS_Discount code is %s', $this->getCouponCode());
    }
    return $html;
  }

  public function getExpirationDate()
  {
    return $this->endtime;
  }
  
  public function getPage()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
      return false;
    }
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

  public function isOfferCredit()
  {
    $isCreditEnabled = Engine_Api::_()->offers()->isCreditEnabled();
    if (!$isCreditEnabled) {
      return false;
    }

    if (!$this->getPage()) {
      return $this->via_credits;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $isPageCreditEnabled = $settings->getSetting('offers.credit.pages', 0);
    if (!$isPageCreditEnabled) {
      return false;
    }

    return $this->via_credits;
  }

  public function isSubscribed(User_Model_User $user)
  {
    /**
     * @var $table Offers_Model_DbTable_Subscriptions
     */
    $subscription = $this->getSubscription($user->getIdentity());
    if ($subscription && ($subscription->status == 'active' || $subscription->status == 'used')) {
      return true;
    }
    return false;
  }

  public function isUsed() {
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $subscription = $this->getSubscription($user_id);

    if ($subscription->status == 'used') {
      return true;
    }

    return false;
  }

  public function getPaymentParams()
  {
    $params = array();

    $translate = Zend_Registry::get('Zend_View');

    // General
    $params['name'] = $translate->translate('Buying %s offer', $this->getTitle());
    $params['price'] = $this->getPrice();
    $params['description'] = $this->getOfferDescription();
    $params['vendor_product_id'] = $this->coupons_code;
    $params['tangible'] = false;
    $params['recurring'] = false;

    return $params;
  }

  public function getProducts()
  {
    /**
     * @var $offerProductsTable Offers_Model_DbTable_Products
     * @var $storeProductsTable Store_Model_DbTable_Products
     */
    $offerProductsTable = Engine_Api::_()->getDbTable('products', 'offers');
    $storeProductsTable = Engine_Api::_()->getDbTable('products', 'store');
    $select = $offerProductsTable->select()
      ->setIntegrityCheck(false)
      ->from(array('op' => $offerProductsTable->info('name')))
      ->joinInner(array('sp' => $storeProductsTable->info('name')), 'op.product_id=sp.product_id', array())
      ->where('op.offer_id = ?', $this->offer_id)
      ->where('sp.owner_id = ?', $this->owner_id)
    ;
    return $offerProductsTable->fetchAll($select);
  }

  public function getProductsToArray()
  {
    $products = $this->getProducts();
    $productsArray = array();
    foreach ($products as $product) {
      $productsArray[$product->product_id] = $product;
    }
    return $productsArray;
  }

  public function getProductsStore($offer_id)
  {
    $isStoreEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store');
    if (!$isStoreEnabled) {
      return false;
    }
    try {
      $product_ids = Engine_Api::_()->offers()->getProductsOffer($offer_id);

      $storeProductsTable = Engine_Api::_()->getDbTable('products', 'store');
      $select = $storeProductsTable->select()
                                  ->where('product_id IN (?)', $product_ids);

      return $storeProductsTable->fetchAll($select);
    }catch (Exception $e) {
      return 0;
    }
  }

  public function getDiscountPrice($price)
  {
    if ($this->getDiscountType() == 'percent') {
      return $price - $price * $this->getDiscount()/100;
    } elseif ($this->getDiscountType() == 'currency') {
      return $price - $this->getDiscount();
    }
  }

  public function getDiscountType()
  {
    return $this->discount_type;
  }

  public function getDiscount()
  {
    return $this->discount;
  }

  /**
   * @return Offers_Model_Subscription
   */
  public function getSubscription($user_id = null)
  {
    /**
     * @var $user User_Model_User
     * @var $subscriptionsTable Offers_Model_DbTable_Subscriptions
     * @var $subscription Offers_Model_Subscription
     */
    $subscription = null;
    $user = ($user_id) ? Engine_Api::_()->getItem('user', $user_id) : Engine_Api::_()->user()->getViewer();

    if ($user) {
      $subscriptionsTable = Engine_Api::_()->getDbTable('subscriptions', 'offers');
      $select = $subscriptionsTable->select()
        ->where('user_id = ?', $user->getIdentity())
        ->where('offer_id = ?', $this->getIdentity())
      ;
      $subscription = $subscriptionsTable->fetchRow($select);
    }
    return $subscription;
  }

  public function getSubscriptions()
  {

    $userTbl = Engine_Api::_()->getDbTable('users', 'user');
    $select = $userTbl->select()
                  ->setIntegrityCheck(false)
                  ->from(array('u' => 'engine4_users'))
                  ->joinInner(array('s' => 'engine4_offers_subscriptions'), 's.user_id = u.user_id')
                  ->where('s.offer_id = ?', $this->getIdentity());

    return $userTbl->fetchAll($select);
  }


  public function getLink()
  {
    return sprintf("<a href='%s'>%s</a>", $this->getHref(), $this->getTitle());
  }

  public function checkTime($time)
  {
    $result = preg_match('/(0{4})-(0{2})-(0{2}) (0{2}):(0{2}):(0{2})/', $time);

    if ($result) {
      return true;
    }

    if (strtotime($time) < strtotime(date("Y-m-d H:i:s"))) {
      return false;
    }

    return true;
  }

  public function decreaseCouponsCount()
  {
    $this->coupons_count = $this->coupons_count - 1;
    $this->save();
  }

  public function getCouponsCount()
  {
    return $this->coupons_count;
  }

  public function getCouponCode($user_id = null)
  {
    if ($this->enable_unique_code) {

      if (!$user_id) {
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      }

      $subscription = $this->getSubscription($user_id);
      return $subscription->getCouponCode();
    } else {
      return $this->coupons_code;
    }
  }

  public function isEnable()
  {
    return $this->enabled;
  }

  public function isOwner(Core_Model_Item_Abstract $owner)
  {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if ($viewer_id == $this->owner_id) {
      return true;
    }

    return false;
  }

  public function getPhotoUrl($type = null)
  {
    if( empty($this->photo_id) ) {
      $view = Zend_Registry::get('Zend_View');
      return $view->layout()->staticBaseUrl . 'application/modules/Offers/externals/images/nophoto_offer_thumb_normal.png';
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }

  public function getDescription() {
    return strip_tags($this->description);
  }
}