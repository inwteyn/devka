<?php
/***/
class Highlights_Model_DbTable_Maps extends Engine_Db_Table {

  protected $_primary = "id";

  public function getSelectTipsMap()
  {
    $select = $this->select()->setIntegrityCheck(false)
      ->from(array('hm' => $this->info('name')));
      $select->joinInner(array('u' => 'engine4_user_fields_meta'), 'u.field_id = hm.tip_id', array('type' ,'label'));
    $select->order('hm.order');
    return $select;
  }

  public function getTipsMap($type, $option_id)
  {
    $select = $this->getSelectTipsMap();

    $select->where('hm.tip_type = ?', $type)
      ->where('hm.option_id = ?', $option_id);

    return $this->fetchAll($select);
  }

  public function addTip($tipsData)
  {
    $data = array(
      'tip_id' => $tipsData['tip_id'],
      'option_id' => $tipsData['option_id'],
      'tip_type' => $tipsData['tip_type']
    );

    try {
      $insert = $this->createRow($data);
      $newTipId = $insert->save();
      $select = $this->getSelectTipsMap();
      $select->where('id = ?', $newTipId);

      return $this->fetchRow($select);
    } catch (Exception $e) {
      throw $e;
    }
  }
  public function orderTips($tips_ids)
  {
    $i = 0;
    foreach($tips_ids as $id){
      $this->update(array('order' => ++$i), array('id = ?' => $id));
    }
  }

  public function deleteTip($tip_id)
  {
    $this->delete(array('id = ?' => $tip_id));
  }
}