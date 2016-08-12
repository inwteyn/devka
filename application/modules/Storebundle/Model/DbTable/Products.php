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
class Storebundle_Model_DbTable_Products extends Engine_Db_Table
{

  protected $_rowClass = 'Storebundle_Model_Product';

  public function getIds() {
    $rows = $this->fetchAll($this->select());
    $ids = array();
    foreach($rows as $row) {
      $ids[] = $row->product_id;
    }

    return $ids;
  }


}
