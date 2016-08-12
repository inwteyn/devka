<?php
/**
 * Created by Hire-Experts LLC.
 * Author: Mirlan
 * Date: 22.08.2015
 * Time: 12:03
 */
 class Hebadge_Model_DbTable_Levelsbadges extends Engine_Db_Table
 {

   public function setLevels($badge_id, $data = array())
    {
      if (empty($data)){
        return;
      }
      //We delete that that was earlier
      $this->delete(array('badge_id = ?' => $badge_id));

      //We set new levels
      foreach ($data as $level_id){
        $this->createRow(array('badge_id' => $badge_id, 'level_id' => $level_id))->save();
      }
    }

    public function getLevels($badge_id)
    {
      $select = $this->select()->where('badge_id = ?', $badge_id);
      $items = $this->fetchAll($select);
      return $items;
    }

   public function getLevelsByLevelId($subject)
   {
     $select = $this->select()->where('level_id = ?', $subject->level_id);
     $items = $this->fetchAll($select);
     return $items;
   }

   public function getLevelsToArray($badge_id)
   {
     $levels_array = array();
     $select = $this->select()->where('badge_id = ?', $badge_id);
     $items = $this->fetchAll($select);
     if ($items) {
       foreach ($items as $item) {
         array_push($levels_array, $item->level_id);
       }
     }
     return $levels_array;
   }
 }
 ?>