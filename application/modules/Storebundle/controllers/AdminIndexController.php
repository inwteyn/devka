<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminFaqController.php 27.04.12 18:27 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Storebundle_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->activeAddonMenu = 'store_admin_addons_bundle';
    $this->view->activeMenu = 'store_admin_main_addons';
  }

  public function products() {
    $products = $this->getProducts();
    $tmpP = array();
    foreach ($products as $product) {
      $title = str_replace("'", "`", $product->getTitle());
      $tmpP[] = array(
        'id' => $product->getIdentity(),
        'title' => $title,
        'image' => $product->getPhotoUrl(),
        'price' => $product->getPrice()
      );
    }

    $this->view->products = Zend_Json::encode($tmpP);
    $this->view->productsCnt = count($tmpP);
  }

  public function indexAction()
  {
    $bundles = Engine_Api::_()->getItemTable('storebundle');

    $this->view->items = $bundles->getBundles();

    $this->products();

    if ($this->getParam('format') == 'json') {
      $this->view->status = true;
      $this->view->html = $this->view->partial('admin-index/list.tpl', array(
        'items' => $this->view->items
      ));
    }


    // some code
    $url = Zend_Registry::get('Zend_Controller_Front')->getRequest();

    $modules = $url->getModuleName();
    $hecore = Engine_Api::_()->getDbTable('modules', 'hecore');

    $select = $hecore->select();

    $strin_for_get = array();
    foreach ($hecore->fetchAll($select) as $plugins) {
      if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled($plugins['name']) || $plugins['name'] == 'likes' || $plugins['name'] == 'pages') {
        array_push($strin_for_get, $plugins['name']);
      }
    }
    $all_modules = implode(",", $strin_for_get);
    $this->view->all_modules = $all_modules;
    $this->view->module = $modules;
  }

  public function createAction()
  {
    $this->_helper->layout->disableLayout();
    $this->view->form = $form = new Storebundle_Form_Admin_Create();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $params = $this->getRequest()->getParams();
    $row = null;

    $bundlesTable = Engine_Api::_()->getDbTable('storebundles', 'storebundle');
    $db = $bundlesTable->getAdapter();
    $db->beginTransaction();
    try {
      $row = $bundlesTable->createRow($params);
      $row->save();
      $db->commit();
    } catch (Exception $e) {
      $this->view->code = 1;
      $this->view->status = false;
      $this->view->message = $e->getMessage();
      $db->rollback();
    }

    if ($row) {
      try {
        $row->updateProducts($params['products']);
        $db->commit();
      } catch (Exception $e) {
        $this->view->code = 2;
        $this->view->status = false;
        $this->view->message = $e->getMessage();
        $row->delete();
        $db->rollback();
      }
    }
    $this->view->status = true;
  }

  public function editAction()
  {
    $this->_helper->layout->disableLayout();

    $bundle_id = $this->_getParam('bundle_id', 0);
    $bundle = Engine_Api::_()->getItem('storebundle', $bundle_id);
    if (!$bundle) {
      $this->view->status = false;
      $this->view->message = $this->view->translate('Invalid bundle');
      return;
    }
    $this->view->selectedProducts = $bundle->getStoreProducts();
    $this->view->selectedProducts = json_encode($this->view->selectedProducts);

    $this->view->form = $form = new Storebundle_Form_Admin_Create(1);
    $form->populate($bundle->toArray());

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $params = $this->getRequest()->getParams();
    $ok = true;

    $bundlesTable = Engine_Api::_()->getDbTable('storebundles', 'storebundle');
    $db = $bundlesTable->getAdapter();
    $db->beginTransaction();
    try {
      $bundle->setFromArray($params);
      $bundle->save();
      $ok = true;
      $db->commit();
    } catch (Exception $e) {
      print_die($e);
      $db->rollback();
    }

    if ($ok) {
      $prodsTable = Engine_Api::_()->getDbTable('products', 'storebundle');
      try {
        $bundle->updateProducts($params['products']);
        $db->commit();
      } catch (Exception $e) {
        print_arr(2);
        print_die($e);
        $db->rollback();
      }
    }
  }

  public function enableAction()
  {
    $this->_helper->layout->disableLayout();

    $bundle_id = $this->_getParam('bundle_id', 0);
    $bundle = Engine_Api::_()->getItem('storebundle', $bundle_id);
    if (!$bundle) {
      $this->view->status = false;
      $this->view->message = $this->view->translate('Invalid bundle');
      return;
    }

    $bundle->enabled = !$bundle->enabled;
    $bundle->save();

    $this->view->enabled = $bundle->enabled;
    $this->view->status = true;
  }

  public function deleteAction()
  {
    $bundle_id = $this->_getParam('bundle_id', 0);
    $bundle = Engine_Api::_()->getItem('storebundle', $bundle_id);
    if (!$bundle) {
      $this->view->status = false;
      $this->view->message = $this->view->translate('Invalid bundle');
      return;
    }

    $bundle->delete();

    $this->view->status = true;
    $this->products();
  }

  public function deleteProductAction()
  {
    if(!$this->getRequest()->isPost()) {
      $this->view->status = false;
      return;
    }
    $product_id = $this->_getParam('product_id', 0);

    $bundleProducts = Engine_Api::_()->getDbTable('products', 'storebundle');
    $product = $bundleProducts->fetchRow(
      $bundleProducts->select()->where('product_id=?', $product_id)
    );

    if($product) {
      $product->delete();
    }

    $this->view->status = true;
  }

  public function completerAction()
  {
    $value = trim($this->_getParam('value'));

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      return;
    }

    $products = $this->getProducts($value);
    if (!$products || !$products->getTotalItemCount()) {
      $this->view->status = false;
      return;
    }
    $items = array();
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    foreach ($products as $product) {
      $title = str_replace("'", "`", $product->getTitle());
      $items[] = array(
        'title' => $title,
        'image' => $product->getPhotoUrl(),
        'id' => $product->getIdentity(),
        'price' => $product->getPrice(),
        'currency' => $this->view->locale()->toCurrency(0, $currency)
      );
    }

    $this->view->status = true;
    $this->view->items = $items;
  }

  public function getProducts($search = '')
  {
    /**
     * @var $table Store_Model_DbTable_Products
     * @var $tbl Store_Model_DbTable_Products
     */
    $bundleProducts = Engine_Api::_()->getDbTable('products', 'storebundle');
    $ids = $bundleProducts->getIds();

    $fc = Zend_Controller_Front::getInstance();
    $request = $fc->getRequest();
    $params = $request->getParams();


    $table = Engine_Api::_()->getDbtable('products', 'store');
    $prefix = $table->getTablePrefix();

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('m' => $prefix . 'store_product_fields_maps'), array("m.*"))
      ->where("m.option_id = ?", 0)
      ->where("m.field_id = ?", 1)
      ->limit(1);

    if (null !== ($row = $table->fetchRow($select))) {
      $this->view->child_id = $child_id = $row->child_id;
      $this->view->subCat_id = $subCat_id = ($request->getParam('field_' . $child_id)) ? $request->getParam('field_' . $child_id) : $request->getParam('sub_cat', 0);
    }

    /**
     * @var $select Zend_Db_Table_Select
     * @var $settings Core_Model_DbTable_Settings
     */

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'))
      ->joinLeft(array('v' => $prefix . 'store_product_fields_values'), "v.item_id = " . $prefix . "store_products.product_id")
      ->joinLeft(array('o' => $prefix . 'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label"))
      ->group($prefix . 'store_products.product_id');

    $select = $table->setStoreIntegrity($select);

    $values = array(
      'order' => $prefix . 'store_products.product_id',
      'order_direction' => 'DESC',
    );

    $this->view->assign($values);
    $field = $fc->getRequest()->getParam('field');
    if (!empty($field)) {
      $select
        ->where('v.field_id = 1 AND ' . 'v.value = ?', $field);
    }
    if (!empty($search)) {
      $select
        ->joinLeft($prefix . 'core_tags', $prefix . "core_tags.text LIKE '%$search%'", array())
        ->joinLeft($prefix . 'core_tagmaps', $prefix . "core_tagmaps.tag_id = " . $prefix . "core_tags.tag_id", array())
        ->where($prefix . 'store_products.product_id = ' . $prefix . 'core_tagmaps.resource_id')
        ->where($prefix . 'core_tagmaps.resource_type = ?', 'store_product')
        ->orWhere($prefix . 'store_products.title LIKE ?', '%' . $search . '%');
    }
    if (!empty($minPrice) && is_numeric($minPrice)) {
      $select
        ->where($prefix . 'store_products.price > ?', $minPrice);
    }
    if (!empty($maxPrice) && is_numeric($maxPrice)) {
      $select
        ->where($prefix . 'store_products.price < ?', $maxPrice);
    }
    if (!empty($category_id)) {
      if (!empty($subCat_id)) {
        $select
          ->where('v.value = ?', $subCat_id);
      } else {
        $select
          ->where('o.option_id = ?', $category_id);
      }
    }

    if (!isset($params['profile_type'])) {
      $params['fields'] = '';
    } else {
      // Process options
      $tmp = array();
      foreach ($params as $k => $v) {
        if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
          continue;
        } else if (false !== strpos($k, '_field_')) {
          list($null, $field) = explode('_field_', $k);
          $tmp['field_' . $field] = $v;
        } else if (false !== strpos($k, '_alias_')) {
          list($null, $alias) = explode('_alias_', $k);
          $tmp[$alias] = $v;
        }
      }
      $params['fields'] = $tmp;
    }

    if (!empty($params['fields'])) {
      $fields = (is_array($params['fields'])) ? $params['fields'] : array($params['fields']);

      $select
        ->joinLeft($prefix . 'store_product_fields_search', $prefix . 'store_product_fields_search.item_id = ' . $prefix . 'store_products.product_id', array());
      $searchParts = Engine_Api::_()->fields()->getSearchQuery('store_product', $fields);

      foreach ($searchParts as $k => $v) {
        $select->where("`" . $prefix . "store_product_fields_search`.{$k}", $v);
      }
    }
    $select
      ->order($prefix . 'store_products.creation_date DESC');

    $select
      ->where($prefix . 'store_products.quantity <> 0 OR ' . $prefix . 'store_products.type = ?', 'digital')
      ->order($prefix . 'store_products.sponsored DESC')
      ->order($prefix . 'store_products.featured DESC');

    $select->where($prefix . 'store_products.price > 0');

    if(count($ids)) {
      $select->where($prefix . 'store_products.product_id not in (?)', $ids);
    }



    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());
    $paginator->setCurrentPageNumber(1);
    return $paginator;
  }
}
