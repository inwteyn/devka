<?php

/**
 * @category   Application_Extensions
 * @package    Heemoticon
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Heemoticon_Model_DbTable_Buyeds extends Engine_Db_Table
{
  /**
   * @param $collection_id
   * @param $user_id
   * @return bool
   */
  public function getBuyed($collection_id, $user_id)
    {
        if ($collection_id > 0 && $user_id > 0) {
            $select = $this->select()->where('collection_id = ?', $collection_id)->where('user_id = ?', $user_id);
            $row = $this->fetchRow($select);
            if ($row) {
                return true;
            }
        }
        return false;
    }

  /**
   * @param $collection_id
   * @param $user_id
   * @return bool|void
   */
  public function BuyCollection($collection_id, $user_id)
    {
        if ($collection_id > 0 && $user_id > 0) {
            $insert = $this->insert(array(
                'collection_id' => $collection_id,
                'user_id' => $user_id,
            ));
        }
        if ($insert) {
            return $insert;
        }
        return false;
    }

}
