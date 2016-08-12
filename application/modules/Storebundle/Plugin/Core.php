<?php

/**
 * Created by PhpStorm.
 * User: asmproger
 * Date: 31.07.15
 * Time: 12:21
 */
class Storebundle_Plugin_Core extends Zend_Controller_Plugin_Abstract
{

  public function onItemDeleteAfter($event)
  {
    $payload = $event->getPayload();
    if (is_array($payload) && $payload['type'] == 'store_product') {
      $prodsT = Engine_Api::_()->getDbTable('products', 'storebundle');
      $select = $prodsT->select()->where('product_id=?', $payload['identity']);
      $bundleProd = $prodsT->fetchRow($select);
      if ($bundleProd) {
        $bundleProd->delete();
      }
    }
  }
}