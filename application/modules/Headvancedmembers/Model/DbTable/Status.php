<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bolot
 * Date: 16.05.13
 * Time: 14:38
 * To change this template use File | Settings | File Templates.
 */
class Headvancedmembers_Model_DbTable_Status extends Engine_Db_Table
{
  public function sendVerification ($data){
    if($data) {
      $insert = $this->createRow($data);
      $new = $insert->save();
    }else{
      return false;
    }
  }
  public function getCountForUser($user){

  }
}