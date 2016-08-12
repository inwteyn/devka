<?php

/**
 * @category   Application_Extensions
 * @package    Heemoticon
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Heemoticon_Model_DbTable_Collections extends Engine_Db_Table
{
  protected $_rowClass = 'Heemoticon_Model_Collection';

  public function deleteCollection($id)
  {
    Engine_Api::_()->getDbTable('stickers', 'heemoticon')->deleteCollectionStickers($id);
    Engine_Api::_()->getDbTable('purchaseds', 'heemoticon')->RemoveCollectionById($id);
    $this->delete(array('collection_id = ?' => $id));
  }

  public function changeCollectionStatus($id, $current_status)
  {
    $stickers = Engine_Api::_()->getDbTable('stickers', 'heemoticon')->getSickersByCollectionID($id);
    $usedsTbl = Engine_Api::_()->getDbTable('useds', 'heemoticon');

    foreach ($stickers as $sticker) {
      $usedsTbl->changeUsedStickerStatus($sticker->getIdentity(), $current_status ? 0 : 1);
    }
    $this->update(array('status' => $current_status ? 0 : 1), array('collection_id = ?' => $id));
  }

  public function getPrice($id)
  {
    $select = $this->select()->from($this->info('name'), array('price'))->where('collection_id = ?', $id);
    $row = $this->fetchRow($select);
    if ($row){
      $row->toArray();
      return $row['price'];
    }
    else {
      return null;
    }
  }

  public function getPaid($id)
  {
    $select = $this->select()->from($this->info('name'), array('price'))->where('collection_id = ?', $id);
    $row = $this->fetchRow($select);
    if ($row){
      $price = $row->toArray();
      $price['price'] != 0 ? $result = true : $result = false;
      return $result;
    } else {
      return null;
    }
  }
}