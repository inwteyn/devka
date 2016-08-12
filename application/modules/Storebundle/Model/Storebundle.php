<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Api.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Storebundle_Model_Storebundle extends Core_Model_Item_Abstract
{
  public function getStoreProducts() {
    $rows = $this->getProducts();
    $result = array();
    foreach($rows as $row) {
      $product = $row->getProduct();
      if(!$product) {
        continue;
      }
      $title = str_replace("'", "`", $product->getTitle());
      $result[] = array(
        'title' => $title,
        'image' => $product->getPhotoUrl(),
        'id' => $product->getIdentity(),
        'price' => $product->getPrice()
      );
    }

    return $result;
  }

  public function getProducts()
  {
    /**
     * @var $productsTable Storebundle_Model_DbTable_Products
     */
    $productsTable = Engine_Api::_()->getDbTAble('products', 'storebundle');

    $select = $productsTable->select()
      ->setIntegrityCheck(false)
      ->from(array('bp' => $productsTable->info('name')))
      ->join(array('sp' => 'engine4_store_products'), 'sp.product_id = bp.product_id')
      ->where('sp.quantity > 0')
      ->where('bp.bundle_id=?', $this->getIdentity())
    ;

    //$select->where('bundle_id=?', $this->getIdentity());
    $rows = $productsTable->fetchAll($select);
    return $rows;
  }

  public function getProductsCount()
  {
    return count($this->getProducts());
  }

  public function removeProducts()
  {
    $db = $this->getTable()->getAdapter();
    $where = $db->quoteInto('bundle_id = ?', $this->getIdentity());
    $db->delete( $this->getTable()->getTablePrefix() . 'storebundle_products', $where);
  }

  public function delete() {
    $this->removeProducts();
    parent::delete();
  }

  public function updateProducts($ids = '')
  {
    if (!strlen(trim($ids))) {
      throw new Exception('No products in bundle');
    }

    $db = Engine_Api::_()->getDbTable('products', 'storebundle');
    $where = $db->getAdapter()->quoteInto('bundle_id = ?', $this->getIdentity());

    $db->getAdapter()->delete( $db->getTablePrefix() . 'storebundle_products', $where);

    $ids = explode(',', $ids);

    foreach($ids as $id) {
      $db->insert(array(
        'bundle_id' => $this->getIdentity(),
        'product_id' => $id
      ));
    }
  }

}