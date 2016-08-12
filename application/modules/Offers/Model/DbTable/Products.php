<?php

class Offers_Model_DbTable_Products extends Engine_Db_Table
{
  public function setProducts($products_ids, $offer_id, $page_id)
  {
    $products_ids = explode(',', $products_ids);
    foreach ($this->fetchAll(array('offer_id = ?' => $offer_id)) as $item) {
      $item->delete();
    }

    foreach ($products_ids as $product_id) {
      if ($product_id != 0) {
        $this->createRow(
          array(
            'product_id' => $product_id,
            'offer_id' => $offer_id,
            'page_id' => $page_id
          ))->save();
      }
    }
  }

  public function getProduct($product_id)
  {
    return $this->fetchAll($this->select()->where('product_id = ?', $product_id));
  }
}