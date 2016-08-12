<?php

class Offers_Api_Core extends Core_Api_Abstract
{
  protected $_plugin;

  public function availableOffer($offer, $str = false, $enableMinutes = false)
  {
    $offer_id = $offer->getIdentity();
    $view = Zend_Registry::get('Zend_View');
    if ($offer_id && $offer_id != 0 && $offer->time_limit == 'limit') {
      $endTime = strtotime($offer->endtime);
      $currentTime = time();//strtotime($this->getDatetime());
      $different = $endTime - $currentTime;
      if ($different > 0) {
        $days = floor($different / (60 * 60 * 24));
        $hours = floor(($different - $days * 60 * 60 * 24) / (60 * 60));
        $minutes = round(($different - ($days * 60 * 60 * 24) - ($hours * 60 * 60)) / 60);

        $leftTime['days'] = $days;
        $leftTime['hours'] = $hours;
        $leftTime['minutes'] = $minutes;

        if ($str) {
          if ($days >= 1) {
            return $view->translate(array('OFFERS_%s day', '%s days', $days), $days);
          }
          if ($hours >= 1) {
            return $view->translate(array('OFFERS_%s hour', '%s hours', $hours), $hours);
          }
          if ($enableMinutes && $minutes >= 1) {
            return $view->translate(array('OFFERS_%s minute', '%s minutes', $minutes), $minutes);
          }
        } else {
          return $leftTime;
        }
      } else {
        return $view->translate('OFFERS_expired');
      }
    } else {
      return $view->translate('OFFERS_unlimit');
    }
  }

  public function timeInterval($offer)
  {
    $offer_id = $offer->getIdentity();
    if ($offer_id && $offer_id != 0 && $offer->time_limit == 'limit') {
      $timeInterval = Engine_Api::_()->getDbTable('offers', 'offers')->getTimeInterval($offer_id);
      if ($timeInterval) {
        $timeInterval = $timeInterval->toArray();
        return date('M j, Y', strtotime($timeInterval['starttime'])) . ' - ' . date('M j, Y', strtotime($timeInterval['endtime']));
      } else {
        return '';
      }
    } else {
      return Zend_Registry::get('Zend_Translate')->translate('OFFERS_Unlimit');
    }
  }

  public function relevanceOffer($offer)
  {

  }

  public function getAllCategories()
  {
    $categories = Engine_Api::_()->getDbtable('categories', 'offers')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array();

    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    return $categoryOptions;
  }

  public function getCategories()
  {
    $categoriesTbl = Engine_Api::_()->getDbTable('categories', 'offers');
    $offersTbl = Engine_Api::_()->getDbTable('offers', 'offers');
    $select = $categoriesTbl->select()->setIntegrityCheck(false)->from(array('c' => $categoriesTbl->info('name')), array('category_id', 'title', 'count' => 'COUNT(c.category_id)'))->join(array('o' => $offersTbl->info('name')), 'c.category_id=o.category_id', array())->group('c.category_id')->order('c.category_id');

    return $categoriesTbl->fetchAll($select)->toArray();
  }

  public function getRequireList($place = 0)
  {
    $enable_modules = Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames();

    $data = array();
    foreach (Zend_Registry::get('Engine_Manifest') as $item) {
      if (empty($item['offers'])) {
        continue;
      }

      if ($place === 0) {
        foreach ($item['offers'] as $type => $item2) {
          if (!empty($item2['module']) && !in_array($item2['module'], $enable_modules)) {
            continue;
          }
          $data[$type] = $item2;
        }
      } else {
        foreach ($item['offers'][$place] as $type => $item2) {
          if (!empty($item2['module']) && !in_array($item2['module'], $enable_modules)) {
            continue;
          }
          $data[$type] = $item2;
          $item['offers'][$place];
        }
      }
    }

    return $data;
  }

  public function getRequire($type, $owner_type = 0)
  {
    if (!$type) {
      return;
    }

    $requires = $this->getRequireList($owner_type);

    if (!array_key_exists($type, $requires)) {
      return;
    }
    return $requires[$type];
  }

  public function getRequireClass($type, $owner_type = 0)
  {

    $require = $this->getRequire($type, $owner_type);

    if (!$require) {
      return;
    }
    return Engine_Api::_()->loadClass(@$require['plugin']);
  }

  public function setItemsType($items)
  {
    $types = array();
    foreach ($items as $item) {

      $type = $item['type'];
      $id = $item['id'];

      if (!isset($types[$type])) {
        $types[$type] = array();
      }
      $types[$type][] = $id;

    }
    return $types;
  }

  public function setItemsGuid($items)
  {
    $new_items = array();
    foreach ($items as $item) {

      $guid = $item['type'] . '_' . $item['id'];

      if (!isset($new_items[$guid])) {
        $new_items[$guid] = array();
      }
      $new_items[$guid] = $item;

    }
    return $new_items;
  }

  public function guidsToItems($guids)
  {
    $items = array();
    if (!empty($guids)) {
      foreach ($guids as $guid) {
        $parts = explode('_', $guid);
        if (count($parts) == 2) {
          $items[] = array('type' => $parts[0], 'id' => $parts[1]);
        }
      }
    }
    return $items;
  }

  public function getItems($items)
  {
    $item_array = array();

    foreach ($this->setItemsType($items) as $type => $ids) {

      if (!Engine_Api::_()->hasItemType($type)) {
        continue;
      }
      $table = Engine_Api::_()->getItemTable($type);

      $matches = $table->info('primary');
      $primary = array_shift($matches);
      if (!$primary) {
        continue;
      }

      foreach ($this->getTableItems($table, $ids) as $item) {
        if (!isset($item_array[$type])) {
          $item_array[$type] = array();
        }
        $item_array[$type][$item->{$primary}] = $item;
      }

    }

    $ready_items = array();
    foreach ($items as $item) {

      $type = $item['type'];
      $id = $item['id'];

      if (!isset($item_array[$type]) || !isset($item_array[$type][$id])) {
        continue;
      }
      $ready_items[] = $item_array[$type][$id];
    }

    return $ready_items;

  }

  public function getTableItems(Zend_Db_Table_Abstract $table, $ids)
  {
    try {
      $matches = $table->info('primary');
      $primary = array_shift($matches);
      if (!$primary) {
        return array();
      }
      if (empty($ids)) {
        return array();
      }
      $select = $table->select()->where("$primary IN (?)", $ids);

      return $table->fetchAll($select);

    } catch (Exception $e) {
      throw $e;
      return;
    }
  }

  public function createPhoto($params, $file)
  {
    if ($file instanceof Storage_Model_File) {
      $params['file_id'] = $file->getIdentity();
    } else {
      // Get image info and resize
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');

      $mainName = $path . '/m_' . $name . '.' . $extension;
      $thumbNormal = $path . '/n_' . $name . '.' . $extension;
      $thumbIcon = $path . '/i_' . $name . '.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
        ->resize(500, 500)
        ->write($mainName)
        ->destroy();

      $image = Engine_Image::factory();
      if (get_class($image) == 'Engine_Image_Adapter_Gd') {
        $image = new Offers_Image_Adapter_Gd();
      } elseif (get_class($image) == 'Engine_Image_Adapter_Imagick') {
        $image = new Offers_Image_Adapter_Imagick();
      }

      $image->open($file['tmp_name'])
        ->resize(220, 200)
        ->write($thumbNormal)
        ->destroy();

      // Resize image (icon)
      $image = Engine_Image::factory();
      $image->open($file['tmp_name']);

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;

      $image->resample($x, $y, $size, $size, 48, 48)
        ->write($thumbIcon)
        ->destroy();

      // Store photos
      $photo_params = array('parent_id' => $params['owner_id'], 'parent_type' => 'offers');

      try {
        $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
        $thumbNormalFile = Engine_Api::_()->storage()->create($thumbNormal, $photo_params);
        $thumbIconFile = Engine_Api::_()->storage()->create($thumbIcon, $photo_params);
      } catch (Exception $e) {
        if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE) {
          echo $e->getMessage();
          exit();
        }
      }

      $photoFile->bridge($thumbNormalFile, 'thumb.normal');
      $photoFile->bridge($thumbIconFile, 'thumb.icon');

      // Remove temp files
      @unlink($mainName);
      @unlink($thumbNormal);
      @unlink($thumbIcon);

      $params['file_id'] = $photoFile->file_id; // This might be wrong
      $params['photo_id'] = $photoFile->file_id;
    }

    $row = $this->getPhotoTable()->createRow();
    $row->setFromArray($params);
    $row->save();

    return $row;
  }

  public function getPhotoTable()
  {
    return Engine_Api::_()->getDbTable('photos', 'offers');
  }

  public function getContentItems($params)
  {
    /**
     * @var $productsTbl Store_Model_DbTable_Products
     * @var $offersTbl Offers_Model_DbTable_Offers
     */
    $owner_type = $params['owner_type'];
    $owner_id = $params['owner_id'];

    $productsTbl = Engine_Api::_()->getDbTable('products', 'store');

    if ($owner_type == 'page') {
      $select = $productsTbl->select()->from(array('p' => $productsTbl->info('name')))->where('p.page_id = ?', $owner_id);
      $items = $productsTbl->fetchAll($select);
    } else {
      $select = $productsTbl->select()->from(array('p' => $productsTbl->info('name')))->where('p.owner_id = ?', $owner_id);
      $items = $productsTbl->fetchAll($select);
    }

    return Zend_Paginator::factory($items);
  }

  public function getProductsOffer($offer_id)
  {
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
      return false;
    }
    $db = Engine_Db_Table::getDefaultAdapter();

    $select = $db->select()
      ->from(array('op' => 'engine4_offers_products'), array())
      ->joinInner(array('p' => 'engine4_store_products'), 'p.product_id = op.product_id', array('product_id'))
      ->where('op.offer_id = ?', $offer_id);
    return $db->fetchCol($select);
  }

  public function getContentItemsChecked($params)
  {
    return array_map('intval', explode(',', $params['checked_products']));
  }

  public function getPageContacts($page_id)
  {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = $db->select()->from('engine4_page_pages', array('country', 'city', 'state','address' => 'street', 'website', 'phone'))->where('page_id = ?', $page_id);

    return $db->fetchRow($select);
  }

  public function getContactsOffer($offer_id, array $contacts = array())
  {
    return Engine_Api::_()->getDbTable('contacts', 'offers')->getContacts($offer_id, $contacts);
  }

  public function generateCouponsCode()
  {
    $code = '';
    $array = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
    $c = count($array) - 1;
    for ($i = 0; $i < 8; $i++) {
      $code .= $array[rand(0, $c)];
    }

    return $code;
  }

  public function generateUniqueCodeForUsers($offer_id)
  {
    $subscriptionsTbl = Engine_Api::_()->getDbTable('subscriptions', 'offers');
    $subscriptionsTbl->update(array('coupon_code' => $this->generateCouponsCode()), array('subscription_id = ?' => $subscription->subscription_id));
    $select = $subscriptionsTbl->select()
                               ->where('offer_id = ?', $offer_id)
                               ->where('coupon_code = ?', '0');

    $subscriptions = $subscriptionsTbl->fetchAll($select);

    if (count($subscriptions)) {
      foreach ($subscriptions as $subscription) {
        $subscriptionsTbl->update(array('coupon_code' => $this->generateCouponsCode()), array('subscription_id = ?' => $subscription->subscription_id));
      }
    }

    return true;
  }

  public function isPageCreditEnabled()
  {
    if (!$this->isCreditEnabled()) {
      return false;
    }

    $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
    if (!$isPageEnabled) {
      return false;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $isOfferPageEnabled = $settings->getSetting('offers.credit.pages', 0);
    if (!$isOfferPageEnabled) {
      return false;
    }

    return true;
  }

  public function isCreditEnabled()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $switcher = $settings->getSetting('offers.credit.enabled', 0);
    $isModuleEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('credit');
    if (!$isModuleEnabled || !$switcher) {
      return false;
    }

    return true;
  }

  public function getCredits($price)
  {
    /**
     * @var $settings Core_Model_DbTable_Settings
     */

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $defaultPrice = $settings->getSetting('credit.default.price', 100);

    return (int)ceil($price * $defaultPrice);
  }

  public function getPlugin($gateway_id)
  {
    if (null === $this->_plugin) {
      /**
       * @var $gatewayTb Payment_Model_Gateway
       */
      if (null == ($gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id))) {
        return null;
      }

      Engine_Loader::loadClass($gateway->plugin);
      if (!class_exists($gateway->plugin)) {
        return null;
      }

      $class = str_replace('Payment', 'Offers', $gateway->plugin);

      Engine_Loader::loadClass($class);
      if (!class_exists($class)) {
        return null;
      }

      $plugin = new $class($gateway);

      if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    }

    return $this->_plugin;
  }

  public function getGateway($gateway_id)
  {
    return $this->getPlugin($gateway_id)->getGateway();
  }

  public function getService($gateway_id)
  {
    return $this->getPlugin($gateway_id)->getService();
  }

  public function getNavigation($page)
  {
    $page_id = $page->getIdentity();
    $navigation = new Zend_Navigation();

    $navigation->addPages(array(array('label' => "Gateway", 'route' => 'offer_page_backend', 'action' => 'gateway', 'params' => array('page_id' => $page_id)), array('label' => "Transactions", 'route' => 'offer_page_backend', 'action' => 'transactions', 'params' => array('page_id' => $page_id)),));
    return $navigation;
  }

  /**
   * @param $page_id
   * @param $gateway_id
   * @return bool
   */
  public function isGatewayEnabled($page_id, $gateway_id)
  {
    /**
     * @var $table Offers_Model_DbTable_Apis
     */
    $table = Engine_Api::_()->getDbTable('apis', 'offers');

    return (boolean)$table->select()->from($table, new Zend_Db_Expr('TRUE'))->where('page_id = ?', $page_id)->where('gateway_id = ?', $gateway_id)->where('enabled = 1')->query()->fetchColumn();
  }


  // Return a featured offer or a favorite offer
  public function getSpecialOffer($type, $page_id = 0)
  {
    $offersTbl = Engine_Db_Table::getDefaultAdapter();
    $select = $offersTbl->select()
      ->from(array('o' => 'engine4_offers_offers'), array('offer_id'));

    if ($page_id) {
      $select->where('page_id = ?', $page_id);
    }

    $select->where($type . ' = ?', 1);

    $ids = $offersTbl->fetchCol($select);
    $id = array_rand($ids);

    return Engine_Api::_()->getDbTable('offers', 'offers')->getOfferById($ids[$id]);
  }

  public function getFeaturedOffer()
  {

  }

  public function getTimezone()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if ($settings->__get('core.locale.timezone')) {
      return $settings->__get('core.locale.timezone');
    } else {
      return Zend_Registry::get('timezone');
    }
  }

  public function getDatetime($datetime = null)
  {
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($oldTz);

    $dt = new Zend_Date();

    if ($datetime != null) {
      $dt->setTime($datetime);
    }

    return $dt->get(Zend_Date::DATETIME);
  }


  public function getGeoLocationMarker($address)
  {
    $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . $address['address'] . ",+" . $address['city'] . ",+" . $address['state'] . ",+" . $address['country'] . "&sensor=false";
    $url = str_replace(' ', '%20', $url);
    if (file_get_contents($url)) {
      $result = Zend_Json_Decoder::decode(file_get_contents($url));
      return $result['results'][0]['geometry']['location'];
    }
    return false;
  }

  public function getPositionMarker($offer_id)
  {
    return Engine_Api::_()->getDbTable('contacts', 'offers')->getPositionMarker($offer_id);
  }

  public function getUsersSubscription($offer_id, $limit = null)
  {
    $db = Engine_Api::_()->getDbTable('users', 'user');

    $select = $db->select()->setIntegrityCheck(false)->from(array('os' => 'engine4_offers_subscriptions'), array(''))->joinInner(array('u' => 'engine4_users'), 'u.user_id = os.user_id')->where('os.offer_id = ?', $offer_id);

    if ($limit) {
      $select->limit($limit);
    }

    $select->order('os.subscription_id DESC');

    return $db->fetchAll($select);
  }
}