<?php

class Offers_Model_DbTable_Infopage extends Engine_Db_Table
{
  public function getInfoPage(User_Model_User $user, $page_id)
  {

    $select = $this->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('page_id = ?', $page_id);

    $row = $this->fetchRow($select);

    if (!$row){
      $row = $this->createRow(array('user_id' => $user->getIdentity(), 'page_id' => $page_id));
      $row->save();
    }

    return $row;
  }
}