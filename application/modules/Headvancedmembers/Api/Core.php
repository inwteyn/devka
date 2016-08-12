<?php

class Headvancedmembers_Api_Core extends Core_Api_Abstract
{

  public function isActive($subject)
  {
    $table = Engine_Api::_()->getDbTable('status', 'headvancedmembers');
    $select  = $table->select()->where('user_id = ?',$subject->getIdentity())->where('status = 1');
    $row = $table->fetchRow($select);
    if($row){
      return true;
    }
    return false;
  }
  public function usersSupported($subject)
  {
    $table = Engine_Api::_()->getDbTable('verification', 'headvancedmembers');
    $select  = $table->select()->where('user_id = ?',$subject->getIdentity());
    $row = $table->fetchAll($select);
    return count($row);
  }


}