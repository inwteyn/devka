<?php

/**
 * @category   Application_Extensions
 * @package    Heemoticon
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Heemoticon_Model_DbTable_Purchaseds extends Engine_Db_Table
{

  /**
   * @param $collection_id
   * @param $user_id
   * @return bool|int
   */
  public function getUsed($collection_id, $user_id)
    {
        if ($collection_id > 0 && $user_id > 0) {
            $select = $this->select()->where('collection_id = ?', $collection_id)->where('user_id = ?', $user_id);
            $row = $this->fetchRow($select);
            if ($row) {
                return 1;
            }
        }
        return false;
    }

  /**
   * @param $collection_id
   * @param $user_id
   * @return bool|void
   */
  public function AddCollection($collection_id, $user_id)
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

  /**
   * @param $collection_id
   * @param $user_id
   * @return bool
   * @throws Zend_Db_Table_Row_Exception
   */
  public function RemoveCollection($collection_id, $user_id)
    {
        if ($collection_id > 0 && $user_id > 0) {
            $select = $this->select()->where('collection_id = ?', $collection_id)->where('user_id = ?', $user_id);
            $row = $this->fetchRow($select);
            $row->delete();
        }
        return true;
    }

  /**
   * @param $collection_id
   */
  public function RemoveCollectionById($collection_id)
    {
        $this->delete(array('collection_id = ?' => $collection_id));
    }

  /**
   * @param array $collection_Ids
   * @param $user_id
   * @return bool
   */
  public function RemoveCollections($collection_Ids = array(), $user_id)
  {
    if (is_array($collection_Ids) && count($collection_Ids) > 0) {
      try {
        $this->delete(array('user_id' => $user_id, 'collection_id IN(?)' => $collection_Ids));
        return true;
      } catch (Exception $e) {
        return false;
      }
    } else {
      return false;
    }
  }
}
