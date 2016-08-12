<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminLocationsController.php 3/22/12 11:29 AM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_AdminLocationsController extends Store_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->activeMenu = 'store_admin_main_locations';

    $this->view->parent_id = $parent_id = $this->_getParam('parent_id', 0);
    /**
     * @var $table       Store_Model_DbTable_Locations
     * @var $locationApi Store_Api_Location
     */
    $table = Engine_Api::_()->getDbTable('locations', 'store');
    $locationApi = Engine_Api::_()->getApi('location', 'store');

    $select = $table->select()->where('location_id = ?', $parent_id)->order('location ASC');
    $this->view->parent = $table->fetchRow($select);

    $this->view->paginator = $locationApi->getPaginator(0, $this->_getParam('page', 1), $parent_id);

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->view->ww_enabled = $settings->__get('store.ww.enabled', false);
    $this->view->ww_price = $settings->__get('store.ww.price', 0);
    $this->view->ww_days = $settings->__get('store.ww.days', 0);
    $this->view->ww_tax = $settings->__get('store.ww.tax', 0);

    $this->view->success_message = $this->view->translate('STORE_save_ww_success_message');
    $this->view->error_message = $this->view->translate('STORE_save_ww_error_message');
  }

  public function saveWwAction()
  {
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      return;
    }

    $en = (boolean) $this->_getParam('ww_enabled');
    $p = (double) $this->_getParam('ww_price');
    $d = (int) $this->_getParam('ww_days');
    $t = (int) $this->_getParam('ww_tax');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $settings->__set('store.ww.enabled', $this->_getParam('ww_enabled'));
    $settings->__set('store.ww.price', $p);
    $settings->__set('store.ww.days', $d);
    $settings->__set('store.ww.tax', $t);
    $this->view->status = true;
  }

  public function supportedAction()
  {
    $location_id = (int)$this->_getParam('location_id', 0);
    $do = $this->_getParam('do', 'add');

    if (!$location_id || !in_array($do, array('add', 'remove'))) {
      $this->view->status = false;
      return;
    }

    /**
     * @var $table      Store_Model_DbTable_Locations
     * @var $tableShips Store_Model_DbTable_Locationships
     * @var $location   Store_Model_Location
     */
    $table = Engine_Api::_()->getDbTable('locations', 'store');
    $tableShips = Engine_Api::_()->getDbTable('locationships', 'store');
    $db = $table->getDefaultAdapter();
    $db->beginTransaction();

    try {
      if ($do == 'add') {
        // he@todo should we add children?
        $ids = $table->getTreeIds($location_id);
        $select = $table->select()->where("location_id IN($ids) ");
        foreach ($table->fetchAll($select) as $location) {
          try {
            $tableShips->insert(array(
              'page_id' => 0,
              'location_id' => $location->getIdentity(),
              'shipping_amt' => $location->shipping_amt,
              'shipping_days' => $location->shipping_days,
              'shipping_tax' => $location->shipping_tax,
              'creation_date' => new Zend_Db_Expr('NOW()'),
            ));
            $db->commit();
          } catch (Exception $e) {
            continue;
          }

          if ($location_id == $location->getIdentity()) {
            $location_id = $location->parent_id;
          }
        }

        // Add location's parents
        while (null != ($location = $table->fetchRow(array('location_id=?' => $location_id)))) {

          if (null == $tableShips->fetchRow(array('page_id = ?' => 0, 'location_id = ?' => $location->getIdentity()))) {
            $tableShips->insert(array(
              'page_id' => 0,
              'location_id' => $location->getIdentity(),
              'shipping_amt' => $location->shipping_amt,
              'shipping_days' => $location->shipping_days,
              'creation_date' => new Zend_Db_Expr('NOW()'),
            ));
          }

          $location_id = $location->parent_id;
        }
        $db->commit();
        $this->view->status = true;
        return;
      } elseif ($do == 'remove') {
        $tableShips->delete(array('location_id IN (' . $table->getTreeIds($location_id) . ')', 'page_id = ?' => 0));
        $db->commit();
        $this->view->status = true;
        return;
      }

    } catch (Exception $e) {
      $db->rollBack();

      print_firebug($e . '');
    }

    $this->view->status = false;
  }

  public function removeSupportedAction()
  {
    $location_id = $this->_getParam('location_id');

    /**
     * @var $table    Store_Model_DbTable_Locations
     * @var $location Store_Model_Location
     */
    $table = Engine_Api::_()->getDbTable('locations', 'store');
    if (null == $location = $table->findRow($location_id)) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'messages' => Zend_Registry::get('Zend_Translate')->_('STORE_No location found')
      ));
    }

    $this->view->form = $form = new Store_Form_Admin_Locations_Remove(array('location' => $location));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getParams())) {
      return;
    }

    /**
     * @var $tableShips Store_Model_DbTable_Locationships
     */
    $tableShips = Engine_Api::_()->getDbTable('locationships', 'store');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $tableShips->delete(array('location_id IN (' . $table->getTreeIds($location_id) . ')', 'page_id = ?' => 0));
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10
    ));
  }

  public function editSupportedAction()
  {
    $location_id = $this->_getParam('location_id');

    /**
     * @var $location Store_Model_Locations
     */
    if (null == $location = Engine_Api::_()->getDbTable('locations', 'store')->findRow($location_id)) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'messages' => Zend_Registry::get('Zend_Translate')->_('STORE_No location found'),
      ));
    }

    /**
     * @var $shipTable Store_Model_DbTable_Locationships
     */
    $shipTable = Engine_Api::_()->getDbTable('locationships', 'store');
    $select = $shipTable
      ->select()
      ->where('page_id = ?', 0)
      ->where('location_id = ?', $location->getIdentity());
    $ship = $shipTable->fetchRow($select);

    $this->view->form = $form = new Store_Form_Admin_Locations_Edit(array('location' => $location));
    $form->removeElement('location');
    $form->removeElement('location_code');
    $form->getElement('shipping_amt')->setValue($ship->shipping_amt);
    $form->getElement('shipping_days')->setValue($ship->shipping_days);
    $form->getElement('shipping_tax')->setValue($ship->shipping_tax);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $data = $this->getRequest()->getParams();

    if (!$form->isValid($data)) {
      return;
    }

    $db = Engine_Api::_()->getDbTable('products', 'store')->getAdapter();
    $db->beginTransaction();

    try {
      if ((float)$data['shipping_amt'] <= 0)
        $ship->shipping_amt = null;
      else
        $ship->shipping_amt = (float)$data['shipping_amt'];

      if ((int)$data['shipping_days'] <= 0)
        $ship->shipping_days = 1;
      else
        $ship->shipping_days = (int)$data['shipping_days'];

      if ((int)$data['shipping_tax'] <= 0)
        $ship->shipping_tax = 0;
      else
        $ship->shipping_tax = (int)$data['shipping_tax'];

      $ship->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }


    $this->view->status = true;
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
    ));
  }

  public function allAction()
  {
    $format = $this->_getParam('format');

    $this->view->isSmoothbox = $isSmoothbox = ($format == 'smoothbox') ? true : false;
    $this->view->flag = (int)$this->_getParam('nc', 0);
    if (!$isSmoothbox) {
      $this->view->activeMenu = 'store_admin_main_locations';
    }

    $this->view->parent_id = $parent_id = $this->_getParam('parent_id', 0);
    $this->view->location_name = $location_name = $this->_getParam('location_name', null);
    $page = $this->_getParam('page', 1);
    /**
     * @var $table       Store_Model_DbTable_Locations
     * @var $parent      Store_Model_Location
     * @var $locationApi Store_Api_Location
     */
    $table = Engine_Api::_()->getDbTable('locations', 'store');
    $locationApi = Engine_Api::_()->getApi('location', 'store');

    $select = $table->select()->where('location_id = ?', $parent_id);
    $this->view->parent = $parent = $table->fetchRow($select);

    $this->view->paginator = $paginator = $locationApi->getPaginator(
      0,
      $page,
      $parent_id,
      'all',
      0,
      $location_name
    );
    $this->view->locationsOnly = false;
  }

  public function addSubAction()
  {
    $this->view->addSub = true;
    $this->view->parent_id = $parent_id = (int)$this->_getParam('parent_id', 0);
  }

  public function addAction()
  {
    $location = $this->_getParam('location');
    $location_code = $this->_getParam('location_code');
    $shipping_amt = $this->_getParam('shipping_amt', null);
    $shipping_tax = $this->_getParam('shipping_tax', null);
    $shipping_days = $this->_getParam('shipping_days', null);
    $parent_id = (int)$this->_getParam('parent_id', 0);
    $this->view->flag = (int)$this->_getParam('nc', 0);
    /**
     * @var $table Store_Model_DbTable_Locations
     */
    $table = Engine_Api::_()->getDbTable('locations', 'store');
    $row = $table->fetchRow(array('location_code = ?' => $location_code, 'parent_id = ?' => $parent_id));
    if ($row) {
      $this->view->status = false;
      $this->view->errorMessage = $this->view->translate('STORE_This location is already exist');
      return;
    }
    try {
      /**
       * @var $locationRow Store_Model_Location
       */
      $locationRow = $table->createRow();
      $locationRow->setFromArray(array(
        'parent_id' => $parent_id,
        'location' => $location,
        'location_code' => strtoupper($location_code),
      ));
      if (!is_null($shipping_amt)) {
        $locationRow->shipping_amt = (double)$shipping_amt;
      }
      if (!is_null($shipping_tax)) {
        $locationRow->shipping_tax = (double)$shipping_tax;
      }
      if (!is_null($shipping_days)) {
        $locationRow->shipping_days = (int)$shipping_days;
      }
      $locationRow->save();
    } catch (Exception $e) {
      $this->view->status = false;
      return;
    }
    /**
     *
     * @var $locationApi Store_Api_Location
     */
    $locationApi = Engine_Api::_()->getApi('location', 'store');

    $this->view->status = 1;
    $paginator = $locationApi->getPaginator(0, 1, $parent_id, 'all');
    $this->view->html = $this->view->partial('admin-locations/all.tpl', array(
      'locationsOnly' => true,
      'parent_id' => $parent_id,
      'paginator' => $paginator,
    ));
  }

  public function validateAction()
  {
    $location = array();

    $parent_id = (int)$this->_getParam('parent_id', 0);
    if ($parent_id) {
      $country = Engine_Api::_()->getItem('store_location', $parent_id);
      if ($country) {
        $location[] = str_replace(' ', '+', $country->location);
      }
    }

    $location_name = $this->_getParam('location');
    if ($location_name) {
      $location[] = str_replace(' ', '+', $location_name);
    }

    $location_code = $this->_getParam('location_code');
    if ($location_code) {
      $location[] = str_replace(' ', '+', $location_code);
    }

    if (!count($location)) {
      $this->view->status = false;
      $this->view->errorMessage = $this->view->translate('STORE_Location Name and Location Code are empty');
      return;
    }

    $url = 'http://maps.googleapis.com/maps/api/geocode/xml?address=' . implode('+', $location) . '&sensor=true';
    $a = curl_init($url);
    curl_setopt($a, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($a, CURLOPT_SSL_VERIFYPEER, false);
    $b = curl_exec($a);
    $result = simplexml_load_string($b);
    curl_close($a);

    if ($result->status != 'OK') {
      $this->view->status = false;
      $this->view->errorMessage = $this->view->translate('STORE_No location found');
      return;
    }

    if (!$parent_id) {
      $searchResult = '';
      foreach ($result->result->address_component as $variants) {
        $type = (array)$variants->type;
        if ($type[0] == 'country') {
          $searchResult .= '<span style="color: red">' . $this->view->translate('STORE_Location Name') . ':</span> "' . $variants->long_name . '", <span style="color: red">' . $this->view->translate('STORE_Location Code') . ':</span> "' . $variants->short_name . '"' . "<br>";
          break;
        }
      }

      if ($searchResult == '') {
        $this->view->status = false;
        $this->view->errorMessage = $this->view->translate('STORE_No location found');
        return;
      }
    } else {
      $searchResult = $this->view->translate('STORE_Choose one of these:') . "<br>";
      $i = 1;
      foreach ($result->result->address_component as $variants) {
        $type = (array)$variants->type;
        if ($type == 'country' and $parent_id) {
          continue;
        }
        $searchResult .= $i . ') <span style="color: red">' . $this->view->translate('STORE_Location Name') . ':</span> "' . $variants->long_name . '", <span style="color: red">' . $this->view->translate('STORE_Location Code') . ':</span> "' . $variants->short_name . '"' . "<br>";
        $i++;
      }

      if ($searchResult == '') {
        $this->view->status = false;
        $this->view->errorMessage = $this->view->translate('STORE_No location found');
        return;
      }
    }

    $this->view->status = true;
    $this->view->noticeMessage = $searchResult;
    return;
  }

  public function editAction()
  {
    $location_id = $this->_getParam('location_id');

    /**
     * @var $location Store_Model_Location
     */
    if (null == $location = Engine_Api::_()->getDbTable('locations', 'store')->findRow($location_id)) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'messages' => Zend_Registry::get('Zend_Translate')->_('STORE_No location found'),
      ));
    }

    $this->view->form = $form = new Store_Form_Admin_Locations_Edit(array('location' => $location));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $data = $this->getRequest()->getParams();

    if (!$form->isValid($data)) {
      return;
    }

    $db = Engine_Api::_()->getDbTable('products', 'store')->getAdapter();
    $db->beginTransaction();

    try {
      $location->location = $data['location'];
      $location->location_code = $data['location_code'];
      if (strlen($data['shipping_amt']) <= 0)
        $location->shipping_amt = null;
      else
        $location->shipping_amt = $data['shipping_amt'];

      if (strlen($data['shipping_tax']) <= 0)
        $location->shipping_tax = null;
      else
        $location->shipping_tax = $data['shipping_tax'];

      if ((int)$data['shipping_days'] <= 0)
        $location->shipping_days = 1;
      else
        $location->shipping_days = (int)$data['shipping_days'];

      $location->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }


    $this->view->status = true;
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
    ));
  }

  public function deleteAction()
  {
    $location_id = $this->_getParam('location_id');

    /**
     * @var $table    Store_Model_DbTable_Locations
     * @var $location Store_Model_Location
     */
    $table = Engine_Api::_()->getDbTable('locations', 'store');
    if (null == $location = $table->findRow($location_id)) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'messages' => Zend_Registry::get('Zend_Translate')->_('STORE_No location found'),
      ));
    }

    $this->view->form = $form = new Store_Form_Admin_Locations_Delete(array('location' => $location));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $location->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }


    $this->view->status = true;
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
    ));
  }
}
