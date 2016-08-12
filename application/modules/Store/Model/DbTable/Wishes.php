<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Wishes.php 12.04.12 13:17 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Wishes extends Engine_Db_Table
{

    public function getWishesCount($product_id = 0) {
        if(!$product_id) {
            return 0;
        }

        $select = $this->select()
            ->from($this->_name, array('count' => 'COUNT(*)'))
            ->where('product_id=?', $product_id);

        $count = $this->fetchRow($select);
        return $count->count;
    }

}
