<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Apis.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Storebundle_Model_DbTable_Storebundles extends Engine_Db_Table
{
  protected $_rowClass = 'Storebundle_Model_Storebundle';

  public function getItems() {
    $select = $this->select()->where('enabled=?', 1);
    return $this->fetchAll($select);
  }

  public function getBundles($params = array())
  {
    $select = $this->select();

    $ipp = (isset($params['ipp']) && $params['ipp']) ? $params['ipp'] : 20;
    $p = (isset($params['p']) && $params['p']) ? $params['p'] : 1;
    $items = Zend_Paginator::factory($select);

    $items->setItemCountPerPage($ipp);
    $items->setCurrentPageNumber($p);
    return $items;
  }

  public function getProductBundles($product_id = 0, $limit = 0)
  {
    if(!$product_id) {
      return null;
    }

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('b' => $this->info('name')))
      ->joinInner(array('p' => 'engine4_storebundle_products'), 'p.bundle_id = b.storebundle_id', array())
      ->where('p.product_id=?', $product_id)
      ->where('b.enabled=?', 1)
    ;
    if($limit) {
      $select->limit($limit);
    }

    if($limit == 1) {
      $items = $this->fetchRow($select);
    } else {
      $items = $this->fetchAll($select);
    }

    return $items;
  }

  public function getProductBundle($product_id = 0)
  {
    if(!$product_id) {
      return null;
    }

    return $this->getProductBundles($product_id, 1);
  }

}