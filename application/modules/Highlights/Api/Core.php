<?php
/***/
class Highlights_Api_Core extends Core_Api_Abstract {
  public function getTipsMeta($type, $option_id = 1)
  {
    $tipsMeta = array();
    if (isset($type) && is_string($type) && !empty($type)) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $select = $db->select()
        ->from(array('ufm' => 'engine4_user_fields_meta'))
        ->joinInner(array('um' => 'engine4_user_fields_maps'), 'um.child_id = ufm.field_id AND um.option_id = '.$option_id, array());
      $metaData = $db->fetchAll($select);
      foreach($metaData as $meta){
        if ($meta['type'] == 'profile_type' || $meta['type'] == 'about_me' || $meta['type'] == 'textarea' || $meta['type'] == 'heading') continue;
        $tipsMeta[$meta['field_id']] = $meta['label'];
      }
      return $tipsMeta;
    }
    else{
      return $this->translate('Unknown type');
    }
  }
  public function getTipsMap($type, $option_id = 1)
  {
    return Engine_Api::_()->getDbTable('maps', 'highlights')->getTipsMap($type, $option_id);
  }

  public function getTipsTypes()
  {
    return Engine_Api::_()->getDbTable('types', 'hetips')->getListTypes();
  }

  public function getUserInfo($userId)
  {
    $db = Engine_Db_Table::getDefaultAdapter();

    $selectUser = $db->select()
      ->from(array('userinfo' => 'engine4_user_fields_values'), array('field_id', 'value'))
      ->joinLeft(array('fields' => 'engine4_user_fields_meta'), 'fields.field_id = userinfo.field_id', array())
      ->where('userinfo.item_id=?', $userId);
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->select()
      ->from('engine4_core_modules', 'version')
      ->where('name = ?', 'core')
      ->query()
      ->fetchColumn();
    if (version_compare($coreVersion, '4.5.0') > 0) {
      $selectUser->where("userinfo.privacy='everyone' OR userinfo.privacy IS NULL");
    }
    $userInfo = $db->fetchPairs($selectUser);
    $profileType = 1;
    foreach ($userInfo as $key => $val) {
      if ($key == 1) {
        $profileType = $val;
        break;
      }
    }

    $selectFieldsToShow = $db->select()
      ->from(array('hm' => 'engine4_highlights_maps'), array('order', 'tip_id'))
      ->where('hm.option_id=?', $profileType)
      ->order('hm.order ASC');
    $fields = $db->fetchPairs($selectFieldsToShow);
    if (!empty($fields)) {
      $fieldsOptions = $db->select()
        ->from(array('fo' => 'engine4_user_fields_options'), array('option_id', 'label'))
        ->where('fo.field_id IN(' . implode(',', $fields) . ')');
      $fieldsOptions = $db->fetchPairs($fieldsOptions);

      $info = array();

      foreach ($fields as $field) {
        if ($userInfo[$field] != '') {
          if (is_numeric($userInfo[$field]) && isset($fieldsOptions[$userInfo[$field]])) {
            $info[] = $fieldsOptions[$userInfo[$field]];
          } elseif (strtotime($userInfo[$field])) {

            $userDateOfBirth = $userInfo[$field];
            $userDateOfBirth = new Zend_Date($userDateOfBirth,'yyyy-MM-dd');
            $userDateOfBirth = $userDateOfBirth->toString('U');
            $userDateOfBirth = (int)$userDateOfBirth;

            $currentDate = new Zend_Date();
            $currentDate = $currentDate->toString('U');
            $currentDate = (int)$currentDate;

            $userAge = (int)floor(($currentDate - $userDateOfBirth)/31556926);
            $translate = Zend_Registry::get('Zend_Translate');
            $info[] = $userAge . $translate->_('HIGHLIGHT_years');
          } else {
            $info[] = $userInfo[$field];
          }
        }
      }
      return implode(',', $info);
    } else {
      return '';
    }
  }
}