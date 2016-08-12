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
class Storebundle_Model_Product extends Core_Model_Item_Abstract
{

  public function getProduct() {
    return Engine_Api::_()->getItem('store_product', $this->product_id);
  }

  public function delete() {
    $bundle = Engine_Api::_()->getItem('storebundle', $this->bundle_id);

    $table = $this->getTable();
    $select = $table->select()
      ->where('bundle_id=?', $this->bundle_id)
      ->where('item_id <> ?', $this->item_id);

    $prods = $table->fetchAll($select);
    $ids = array();
    foreach($prods as $prod) {
      $ids[] = $prod->product_id;
    }

    $bundle->products = implode(',', $ids);
    $bundle->save();

    parent::delete();
  }


}