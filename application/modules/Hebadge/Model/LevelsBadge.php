<?php
/**
 * Created by Hire-Experts LLC.
 * Author: Ulan
 * Date: 22.08.2015
 * Time: 12:14
 */
 
class Hebadge_Model_LevelsBadge extends Core_Model_Item_Abstract
{
  public function setLevels($data = array())
   {
     if (empty($data)){
       return;
     }
     $table = Engine_Api::_()->getDbTable('require', 'hebadge');

     foreach ($table->fetchAll(array('badge_id = ?' => $this->getIdentity())) as $item){
       $item->delete();
     }

     foreach ($data as $type => $item){
       $table->createRow(array('badge_id' => $this->getIdentity(), 'type' => $type, 'params' => $item))->save();
     }
   }

   public function getLevels()
   {
     $table = Engine_Api::_()->getDbTable('require', 'hebadge');

     $select = $table->select()
         ->where('badge_id = ?', $this->getIdentity())
         ->where('type IN (?)', array_keys(Engine_Api::_()->hebadge()->getRequireList()));

     return $table->fetchAll($select);

   }
}