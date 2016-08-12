<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Campaign.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Model_Campaign extends Core_Model_Item_Abstract
{
  protected $_type = 'campaign';
  
  protected function getSelectClause($select, $recievers)
  {
    switch($recievers['users'])
    {
      case 'all_users':
        break;

      case 'member_levels':
        $levels_str = "0";
        if ( (int)$recievers['member_levels'] > 0) {
          if (is_array($recievers['member_levels'])) {
            foreach ($recievers['member_levels'] as $member_level) {
              $levels_str .= ','.$member_level;
            }
          } else {
            $levels_str = $recievers['member_levels'];
          }
        }

        $select->where("u.level_id IN ({$levels_str})");
        break;

      case 'networks':
        $networks_str = "0";
        if ( (int)$recievers['networks'] > 0) {
          if (is_array($recievers['networks'])) {
            foreach ($recievers['networks'] as $network) {
              $networks_str .= ','.$network;
            }
          } else {
            $networks_str = $recievers['networks'];
          }
        }

        $networkTb = Engine_Api::_()->getDbtable('membership', 'network');
        $select->join(array('n'=>$networkTb->info('name')), 'n.user_id = u.user_id', null)
          ->where("n.resource_id IN ($networks_str)")
          ->where("n.active=1")
          ->where("n.resource_approved=1")
          ->where("n.user_approved=1");
        break;

      case 'profile_types':
        $types_str = "'0'";
        if ( (int)$recievers['profile_types'] > 0) {
          if (is_array($recievers['profile_types'])) {
            foreach ($recievers['profile_types'] as $profile_type) {
              $types_str .= ",'" . $profile_type . "'";
            }
          } else {
            $types_str = $recievers['profile_types'];
          }
        }

        /*$searchTb = Engine_Api::_()->fields()->getTable('user', 'search');
        $select->join(array('s'=>$searchTb->info('name')), 's.item_id = u.user_id' , null)
          ->where("s.profile_type IN ({$types_str})");*/
        $fieldsValuesTbl = Engine_Api::_()->fields()->getTable('user', 'values');
        $select->join(array('v' => $fieldsValuesTbl->info('name')), 'v.item_id = u.user_id', null)
          ->where("v.field_id = 1")
          ->where("v.value IN(" . $types_str . ")");
        break;

      case 'custom':
        $levels_str = "0";
        if ( (int)$recievers['member_levels'] > 0) {
          if (is_array($recievers['member_levels'])) {
            foreach ($recievers['member_levels'] as $member_level) {
              $levels_str .= ($member_level !== '') ? ','.$member_level : '';
            }
          } else {
            $levels_str = $recievers['member_levels'];
          }
        }

        $levels_str = trim($levels_str, ',');
        if ($levels_str !== '0') {
          $select->where("u.level_id IN ({$levels_str})");
        }

        $networks_str = "0";
        if ( isset($recievers['networks']) and (int)$recievers['networks'] > 0) {
          if (is_array($recievers['networks'])) {
            foreach ($recievers['networks'] as $network) {
              $networks_str .= ($network !== '') ? ',' . $network : '';
            }
          } else {
            $networks_str = $recievers['networks'];
          }
        }

        $networks_str = trim($networks_str, ',');
        if($networks_str !== '0'){
          $networkTb = Engine_Api::_()->getDbtable('membership', 'network');
          $select->join(array('n'=>$networkTb->info('name')), 'n.user_id = u.user_id', null)
           ->where("n.resource_id IN ($networks_str)")
           ->where("n.active=1")
           ->where("n.resource_approved=1")
           ->where("n.user_approved=1");
        }

        $profile_type = (isset($recievers['profile_type']) &&  strlen(trim($recievers['profile_type']))>0) ? $recievers['profile_type'] : 0;
        /*$searchTb = Engine_Api::_()->fields()->getTable('user', 'search');
        $select->join(array('s'=>$searchTb->info('name')), 's.item_id = u.user_id', null)
           ->where("s.profile_type=?", $profile_type);*/

        $fieldsValuesTbl = Engine_Api::_()->fields()->getTable('user', 'values');
        $select->join(array('v'=>$fieldsValuesTbl->info('name')), 'v.item_id = u.user_id', null)
          ->where("v.field_id = 1");
        if($profile_type > 0) {
          $select
            ->where("v.value = ?", $recievers['profile_type']);
        }
        break;
      default:

        break;
    }

    switch( $recievers['profile_photo'] )
    {
      case 1:
        $select->where('u.photo_id > ?', 0);
        break;

      case 2:
        $select->where('u.photo_id = ?', 0);
        break;

      default: break;
    }

    if ( (int)$recievers['last_logged_count'] > 0 )
    {
      $count = (int)$recievers['last_logged_count'];
      $date = '';

      switch( $recievers['last_logged_type'])
      {
        case 'days':
          $date = date('Y-m-d', strtotime('-' . $count . ' day'));
          break;

        case 'weeks':
          $date = date('Y-m-d', strtotime('-' . $count . ' week'));
          break;

        case 'months':
          $date = date('Y-m-d', strtotime('-' . $count . ' month'));
          break;
      }

      $select->where('u.lastlogin_date >= ?', $date);
    }

    return $select;
  }

  /**
   * @param int $limit
   * @return Zend_Db_Table_Rowset
   */

  public function getRegisteredRecipients( $limit )
  {
    $campaign_id = $this->campaign_id;
    $recievers = $this->recievers;

    $userTb = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTb->info('name');
    if($recievers['users'] != 'custom') {
      $select = $userTb->select()
        ->setIntegrityCheck(false)
        ->from(array('u' => $userTableName), 'u.*');

      $select = $this->getSelectClause($select, $recievers);

      $select
        ->where('u.enabled = 1')
        ->where('u.verified = 1')
        ->where('u.updates_subscribed = 1')
        ->where('u.updates_campaign_id != ?', $campaign_id)
        ->order('u.user_id ASC')
        ->group('u.user_id')
        ->limit($limit);
    }
    if($recievers['users'] == 'custom'){
      $select = $userTb->select()
        ->setIntegrityCheck(false)
        ->from(array('u' => $userTableName), array('u.user_id'));

      $select = $this->getSelectClause($select, $recievers);

      $strUsers_id = '0';

      $user_ids = $select
        ->where('u.enabled = 1')
        ->where('u.verified = 1')
        ->where('u.updates_subscribed = 1')
        ->where('u.updates_campaign_id != ?', $campaign_id)
        ->order('u.user_id ASC')
        ->group('u.user_id')
        //->limit($limit)
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

      $strUsers_id = '0';

      $strUsers_id = implode(',', $user_ids);

      $mapsTable = Engine_Api::_()->fields()->getTable('user', 'maps');
      $mapsTableName = $mapsTable->info('name');

      $metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
      $metaTableName = $metaTable->info('name');

      $valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
      $valuesTableName = $valuesTable->info('name');

      $selectMeta = $metaTable->select()
        ->from(array('meta' => $metaTableName), array('field_id','type'))
        ->joinLeft(array('maps' => $mapsTableName), "meta.field_id = maps.child_id", null)
        ->where('maps.option_id = ?', $recievers['profile_type']);

      $select = $userTb->select()
        ->from($userTableName)
        ->joinLeft($valuesTableName, "`{$valuesTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
        ->where("{$userTableName}.user_id IN ({$strUsers_id})")
        ->where("{$userTableName}.enabled = ?", 1)
        ->order("{$userTableName}.displayname ASC");

      $fieldsTypes = $metaTable->fetchAll($selectMeta);
      $arrFieldsTypes = $fieldsTypes->toArray();

      $handledOptions = array();

      foreach($recievers as $key=>$value ) {
        if(preg_match("/[0-9]/", $key)) {
          $intermediateKey = explode('_', $key);
          if($intermediateKey[4] == 'gender' or $intermediateKey[4] == 'birthdate') {
            $handledOptions[$intermediateKey[4]] = $value;
          }
        }
      }

      foreach($arrFieldsTypes as $item) {
        if($item['type'] == 'gender' and !empty($handledOptions[$item['type']])) {
          $select->where("`{$valuesTableName}`.field_id = {$item['field_id']}");
          $select->where("`{$valuesTableName}`.value = '{$handledOptions[$item['type']]}'");
        }
        if($item['type'] == 'birthdate' and isset($handledOptions[$item['type']])) {
          if(empty($handledOptions[$item['type']]['min']) and empty($handledOptions[$item['type']]['max'])) {
            continue;
          }
          $currentDateTime = time();
          $minAge = empty($handledOptions[$item['type']]['min']) ? 0 : 60*60*24*365*$handledOptions[$item['type']]['min'];
          $maxAge = 60*60*24*365*$handledOptions[$item['type']]['max'];
          $minDate = $currentDateTime - $minAge;
          $maxDate = $currentDateTime - $maxAge;
          $minValue = date('Y-m-d',$minDate);
          $maxValue = empty($handledOptions[$item['type']]['max']) ? '0000-00-00' : date('Y-m-d',$maxDate);

          $selectValues = $valuesTable->select()
            ->from(array($valuesTableName), array('item_id'))
            ->where("`{$valuesTableName}`.field_id = {$item['field_id']}")
            ->where("STR_TO_DATE(`{$valuesTableName}`.value, '%Y-%m-%d') <= '{$minValue}' AND STR_TO_DATE(`{$valuesTableName}`.value, '%Y-%m-%d') >= '{$maxValue}'")
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

          $values_str = !empty($selectValues) ? implode(',', $selectValues) : 0;
          $select->where("`{$valuesTableName}`.item_id IN( {$values_str} )")->group($valuesTableName . '.item_id');
        }
      }
      $select->group($userTableName . '.user_id');

      $select->limit($limit);
    }

    $users = $userTb->fetchAll($select);

    return $users;
  }

  public function getTotalRegisteredRecipients()
  {
    $recievers = $this->recievers;

    $userTb = Engine_Api::_()->getDbTable('users', 'user');
    $select = $userTb->select()
      ->setIntegrityCheck(false)
      ->from(array('u'=>$userTb->info('name')), array('u.user_id'));

    $select = $this->getSelectClause($select, $recievers);

    $select
      ->where('u.enabled = 1')
      ->where('u.verified = 1')
      ->where('u.updates_subscribed = 1');

    $users = $userTb->fetchAll($select);
    // checking: is used at least one field for searching?
    $isAnyFieldUsed = false;
    $i = -1;
    if(isset($recievers['fields_value'])) {
      foreach ($recievers['fields_value'] as $key => $itemValue) {
        if ($itemValue == '' || $itemValue == '0') {
          $isAnyFieldUsed = false;
        }
        else {
          $isAnyFieldUsed = true;
          break;
        }
      }
    }

    if (!$isAnyFieldUsed) {
      return $users->count();
    }

    $handledField_id = array();
    $fieldsID_handled = array();

    $i = -1;
    // handling fields_id
    foreach ($recievers['fields_id'] as $field)
    {
      if(stripos($field, 'min') !== false || stripos($field, 'max') !== false) {
        continue;
      }
      $i++;
      $handledField_id = explode('-',$field);
      $fieldsID_handled[$i] = $handledField_id[0];
    }

    // copy $fieldsID_handled for search
    $fieldsID_forSearch = $fieldsID_handled;

    $searchItem = '';
    $duplicateFields_id = array();
    $i = -1;

    // getting duplicate values
    foreach ($fieldsID_handled as $itemKey => $itemField_id) {
      $searchItem = $itemField_id;
      $searchKey = $itemKey;
      foreach($fieldsID_forSearch as $itemKeyCompare => $itemField_idCompare) {
        if ($searchItem == $itemField_idCompare && $searchKey != $itemKeyCompare) {
          $i++;
          $duplicateFields_id[$i] = $searchItem;
        }
      }
    }

    // getting unique values
    $uniqFields_id = array_unique($duplicateFields_id);

    // getting a copy of fields values with full keys. Some keys in $recievers['fields_value'] are absent.
    $fieldsValue = array();
    $i = -1;
    foreach ($recievers['fields_value'] as $itemValue) {
      $i++;
      $fieldsValue[$i] = $itemValue;
    }

    $minMonth = '';
    $minDay  = '';
    $minYear = '';
    $maxMonth = '';
    $maxDay = '';
    $maxYear = '';
    $minDate = '';
    $maxDate = '';
    $minMaxDate = array();
    $i = -1;

    // getting min max date
    foreach ($recievers['fields_id'] as $field_id) {
      $i++;
      if (stripos($field_id, 'min-month') !== false && $fieldsValue[$i] != 0) {
        $minMonth = $fieldsValue[$i];
      }
      if (stripos($field_id, 'min-day') !== false && $fieldsValue[$i] != 0) {
        $minDay = $fieldsValue[$i];
      }
      if (stripos($field_id, 'min-year') !== false && $fieldsValue[$i] != 0) {
        $minYear = $fieldsValue[$i];
      }

      if ($minMonth != '' && $minDay != '' && $minYear != '') {
        $minMaxDate['min'] =  $minYear .'-'. $minMonth .'-'. $minDay;
        $minYear = '';
        $minMonth = '';
        $minDay = '';
      }

      if (stripos($field_id, 'max-month') !== false && $fieldsValue[$i] != 0) {
        $maxMonth = $fieldsValue[$i];
      }
      if (stripos($field_id, 'max-day') !== false && $fieldsValue[$i] != 0) {
        $maxDay = $fieldsValue[$i];
      }
      if (stripos($field_id, 'max-year') !== false && $fieldsValue[$i] != 0) {
        $maxYear = $fieldsValue[$i];
      }

      if ($maxMonth != '' && $maxDay != '' && $maxYear != '') {
        $minMaxDate['max'] =  $maxYear .'-'. $maxMonth .'-'. $maxDay;
        $maxYear = '';
        $maxMonth = '';
        $maxDay = '';
      }
    }

    $options = array();
    $minMaxValue = array();
    $arrValueForOneField_id = array();
    $field_idForArrayValue = array();
    $i = -1;
    $j = -1;
    $flag1 = false;
    $flag2 = false;

    // prepare fields key and values for sql query
    foreach ($recievers['fields_id'] as $field)
    {
      $i++;
      if (stripos($field, 'min-month') !== false) {
        $handledField = explode('-',$field);
        $options[$handledField[0]] = $minMaxDate;
      }
      elseif (stripos($field, 'min-day') !== false || stripos($field, 'min-year') !== false ||
        stripos($field, 'max-month') !== false || stripos($field, 'max-day') !== false || stripos($field, 'max-year') !== false) {
        continue;
      }
      elseif (stripos($field, 'min') !== false) {
        $minMaxValue['min'] = $fieldsValue[$i];
        $minMaxValue['max'] = $fieldsValue[$i+1];
        $handledField = explode('-',$field);
        $options[$handledField[0]] = $minMaxValue;
      }
      else
      {
        if (stripos($field, 'max') !== false) {
          continue;
        }
        $handledField = explode('-',$field);
        foreach ($uniqFields_id as $itemUniqField_id) {
          if ($handledField[0] == $itemUniqField_id) {
            $j++;
            $field_idForArrayValue = $itemUniqField_id;
            $arrValueForOneField_id[$j] = $fieldsValue[$i];
            $flag1 = true;
            $flag2 = true;
            break;
          }
        }
        if ($flag1) {
          $flag1 = false;
          continue;
        }
        if ($flag2) {
          $flag2 = false;
          $options[$field_idForArrayValue] = $arrValueForOneField_id;
          unset($arrValueForOneField_id);
        }
        $options[$handledField[0]] = $fieldsValue[$i];
      }
    }

    // Process options
    $tmp = array();
    $originalOptions = $options;
    foreach( $options as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $options = $tmp;

    $i = -1;
    $strUsers_id = '0';
    $arrUsers_id = array();

    foreach ($users as $user) {
      $i++;
      $strUsers_id .= ','.$user['user_id'];
      $arrUsers_id[$i] = $user['user_id'];
    }

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');
    $valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
    $valuesTableName = $valuesTable->info('name');

    //extract($options); // displayname
    $profile_type = @$options['profile_type'];
    $displayname = @$options['displayname'];
    if (!empty($options['extra'])) {
      extract($options['extra']); // is_online, has_photo, submit
    }

    // Construct query
    $select = $table->select()
    //->setIntegrityCheck(false)
      ->from($userTableName)
      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
      ->joinLeft($valuesTableName, "`{$valuesTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
    //->group("{$userTableName}.user_id")
      ->where("{$userTableName}.user_id IN ({$strUsers_id})")
    //->where("{$userTableName}.search = ?", 1)
      ->where("{$userTableName}.enabled = ?", 1)
      ->order("{$userTableName}.displayname ASC");

    // Build the photo and is online part of query
    if( isset($has_photo) && !empty($has_photo) ) {
      $select->where($userTableName.'.photo_id != ?', "0");
    }

    if( isset($is_online) && !empty($is_online) ) {
      $select
        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
        ->group("engine4_user_online.user_id")
        ->where($userTableName.'.user_id != ?', "0");
    }

    // Add displayname
    if( !empty($displayname) ) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    }

    $mapsTable = Engine_Api::_()->fields()->getTable('user', 'maps');
    $mapsTableName = $mapsTable->info('name');

    $metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
    $metaTableName = $metaTable->info('name');

    $selectMeta = $metaTable->select()
      ->from(array('meta' => $metaTableName), array('field_id','type'))
      ->joinLeft(array('maps' => $mapsTableName), "meta.field_id = maps.child_id", null)
      ->where('maps.option_id = ?', $recievers['profile_type']);

    $fieldsTypes = $metaTable->fetchAll($selectMeta);
    $arrFieldsTypes = $fieldsTypes->toArray();

    $fieldsTypes_id = array();
    $i = -1;
    foreach ($options as $key => $value) {
      $i++;
      if(preg_match("/[0-9]/", $key)) {
        $intermediateKey = explode('_', $key);
        $handledKey = $intermediateKey[1];
      }
      else {
        $handledKey = $key;
      }
      foreach ($arrFieldsTypes as $item) {
        if ($item['field_id'] == $handledKey && $item['type'] == 'text') {
          $select->where("`{$searchTableName}`.{$key} LIKE '%{$value}%'");
        }
        if ($item['field_id'] == $handledKey && $item['type'] == 'select') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['field_id'] == $handledKey && $item['type'] == 'multiselect') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['field_id'] == $handledKey && $item['type'] == 'radio') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['field_id'] == $handledKey && $item['type'] == 'checkbox') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['field_id'] == $handledKey && $item['type'] == 'multi_checkbox') {
          if(is_array($value)) {
            $codition = '';
            $i = -1;
            foreach ($value as $itemValue) {
              $i++;
              if ($i == 0) {
                $codition .= "(`{$searchTableName}`.{$key} LIKE '%{$itemValue}%')";
              }
              else {
                $codition .= " OR (`{$searchTableName}`.{$key} LIKE '%{$itemValue}%')";
              }
            }
            $select->where($codition);
          }
          else {
            $select->where("`{$searchTableName}`.{$key} = '{$value}'");
          }
        }
        if ($item['field_id'] == $handledKey && $item['type'] == 'float') {
          $select->where("(`{$searchTableName}`.{$key} >= {$value['min']}) AND (`{$searchTableName}`.{$key} <= {$value['max']})");
        }
        if ($item['field_id'] == $handledKey && $item['type'] == 'integer') {
          $select->where("(`{$searchTableName}`.{$key} >= {$value['min']}) AND (`{$searchTableName}`.{$key} <= {$value['max']})");
        }
        if ($item['field_id'] == $handledKey && $item['type'] == 'date') {
          $select->where("(`{$searchTableName}`.{$key} >= '{$value['min']}') AND (`{$searchTableName}`.{$key} <= '{$value['max']}')");
        }
        if ($item['type'] == 'gender' && $handledKey == 'gender') {
          //$select->where("`{$searchTableName}`.{$key} = '{$value}'");
          $select->where("`{$valuesTableName}`.field_id = {$item['field_id']}");
          $select->where("`{$valuesTableName}`.value = '{$value}'");
        }
        if ($item['type'] == 'birthdate' && $handledKey == 'birthdate') {
          $currentDateTime = time();
          $minAge = empty($value['min']) ? 0 : 60*60*24*365*$value['min'];
          $maxAge = 60*60*24*365*$value['max'];
          $minDate = $currentDateTime - $minAge;
          $maxDate = $currentDateTime - $maxAge;
          $minValue = date('Y-m-d',$minDate);
          $maxValue = empty($value['max']) ? '0000-00-00' : date('Y-m-d',$maxDate);
          //$select->where("`{$searchTableName}`.{$key} <= '{$minValue}' AND `{$searchTableName}`.{$key} >= '{$maxValue}'");

          $selectValues = $valuesTable->select()
            ->from(array($valuesTableName), array('item_id'))
            ->where("`{$valuesTableName}`.field_id = {$item['field_id']}")
            ->where("STR_TO_DATE(`{$valuesTableName}`.value, '%Y-%m-%d') <= '{$minValue}' AND STR_TO_DATE(`{$valuesTableName}`.value, '%Y-%m-%d') >= '{$maxValue}'")
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

          $values_str = !empty($selectValues) ? implode(',', $selectValues) : 0;
          $select->where("`{$valuesTableName}`.item_id IN( {$values_str} )")->group($valuesTableName . '.item_id');
        }
        if ($item['type'] == 'website' && $handledKey == 'website') {
          $select->where("`{$searchTableName}`.{$key} LIKE '%{$value}%'");
        }
        if ($item['type'] == 'facebook' && $handledKey == 'facebook') {
          $select->where("`{$searchTableName}`.{$key} LIKE '%{$value}%'");
        }
        if ($item['type'] == 'twitter' && $handledKey == 'twitter') {
          $select->where("`{$searchTableName}`.{$key} LIKE '%{$value}%'");
        }
        if ($item['type'] == 'aim' && $handledKey == 'aim') {
          $select->where("`{$searchTableName}`.{$key} LIKE '%{$value}%'");
        }
        if ($item['type'] == 'city' && $handledKey == 'city') {
          $select->where("`{$searchTableName}`.{$key} LIKE '%{$value}%'");
        }
        if ($item['type'] == 'country' && $handledKey == 'country') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['type'] == 'zip_code' && $handledKey == 'zip_code') {
          $select->where("(`{$searchTableName}`.{$key} >= {$value['min']}) AND (`{$searchTableName}`.{$key} <= {$value['max']})");
        }
        if ($item['type'] == 'location' && $handledKey == 'location') {
          $select->where("`{$searchTableName}`.{$key} LIKE '%{$value}%'");
        }
        if ($item['type'] == 'relationship_status' && $handledKey == 'relationship_status') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['type'] == 'looking_for' && $handledKey == 'looking_for') {
          if(is_array($value)) {
            $codition = '';
            $i = -1;
            foreach ($value as $itemValue) {
              $i++;
              if ($i == 0) {
                $codition .= "(`{$searchTableName}`.{$key} LIKE '%{$itemValue}%')";
              }
              else {
                $codition .= " OR (`{$searchTableName}`.{$key} LIKE '%{$itemValue}%')";
              }
            }
            $select->where($codition);
          }
          else {
            $select->where("`{$searchTableName}`.{$key} = '{$value}'");
          }
        }

        if ($item['type'] == 'partner_gender' && $handledKey == 'partner_gender') {
          if(is_array($value)) {
            $codition = '';
            $i = -1;
            foreach ($value as $itemValue) {
              $i++;
              if ($i == 0) {
                $codition .= "(`{$searchTableName}`.{$key} LIKE '%{$itemValue}%')";
              }
              else {
                $codition .= " OR (`{$searchTableName}`.{$key} LIKE '%{$itemValue}%')";
              }
            }
            $select->where($codition);
          }
          else {
            $select->where("`{$searchTableName}`.{$key} = '{$value}'");
          }
        }

        if ($item['type'] == 'education_level' && $handledKey == 'education_level') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }

        if ($item['type'] == 'ethnicity' && $handledKey == 'ethnicity') {
          if(is_array($value)) {
            $codition = '';
            $i = -1;
            foreach ($value as $itemValue) {
              $i++;
              if ($i == 0) {
                $codition .= "(`{$searchTableName}`.{$key} LIKE '%{$itemValue}%')";
              }
              else {
                $codition .= " OR (`{$searchTableName}`.{$key} LIKE '%{$itemValue}%')";
              }
            }
            $select->where($codition);
          }
          else {
            $select->where("`{$searchTableName}`.{$key} = '{$value}'");
          }
        }

        if ($item['type'] == 'income' && $handledKey == 'income') {
          $fixedValue = '';
          if ($value == 0) {
            $fixedValue = '25';
          }
          elseif ($value == 1) {
            $fixedValue = '150';
          }
          else {
            $fixedValue = $value;
          }
          $select->where("`{$searchTableName}`.{$key} = '{$fixedValue}'");
        }

        if ($item['type'] == 'occupation' && $handledKey == 'occupation') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['type'] == 'political_views' && $handledKey == 'political_views') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['type'] == 'religion' && $handledKey == 'religion') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['type'] == 'weight' && $handledKey == 'weight') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['type'] == 'zodiac' && $handledKey == 'zodiac ') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['type'] == 'eye_color' && $handledKey == 'eye_color') {
          $select->where("`{$searchTableName}`.{$key} = '{$value}'");
        }
        if ($item['type'] == 'currency' && $handledKey == 'currency') {
          $select->where("(`{$searchTableName}`.{$key} >= {$value['min']}) AND (`{$searchTableName}`.{$key} <= {$value['max']})");
        }
      }
    }

    $users = $table->fetchAll($select);

    return $users->count();
  }

  public function getTotalRecipients()
  {
    $totalRegisteredRecipients = $this->getTotalRegisteredRecipients();
    $recievers = $this->recievers;

    $totalSubscribedRecipients = 0;
    if ($recievers['subscribers']) {
      $totalSubscribedRecipients = Engine_Api::_()->getDbtable('subscribers', 'updates')->getTotalSubscribedEmails();
    }

    return (int)($totalRegisteredRecipients + $totalSubscribedRecipients);
  }

  public function setRecipients($values, $elements = array(), $postValues = array())
  {
    $recievers['users'] = $values['users'];
    $recievers['subscribers'] = $values['subscribers'];
    $recievers['profile_photo'] = $values['profile_photo'];
    $recievers['last_logged_count'] = $values['last_logged_count'];
    $recievers['last_logged_type'] = $values['last_logged_type'];
    /*$recievers['1_1_5_alias_gender'] = $values['1_1_5_alias_gender'];
    $recievers['1_1_6_alias_birthdate'] = $values['1_1_6_alias_birthdate'];*/

    switch($recievers['users'])
    {
      case 'member_levels':
        $recievers['member_levels'] = $values['member_levels'];
        break;

      case 'networks':
        $recievers['networks'] = $values['networks'];
        break;

      case 'profile_types':
        $recievers['profile_types'] = $values['profile_types'];
        break;

      case 'custom':
        foreach ($elements as $key=>$element) {
          if (array_key_exists($key, $values) && $key != 'submit')
          {
            $recievers[$key] = $values[$key];
          }
        }
        foreach ($postValues as $key=>$element) {
          if (array_key_exists($key, $values) && $key != 'submit')
          {
            $recievers[$key] = $values[$key];
          }
        }

        break;

      default:
        break;
    }

    $this->recievers = $recievers;
  }

  public function setPlannedDate($values)
  {
    if ($this->type == 'schedule'){
      $date = $values['planned_date']['date'];
      $hour= ($values['planned_date']['hour']<10)?'0'.$values['planned_date']['hour']:$values['planned_date']['hour'];
      $minute = ($values['planned_date']['minute']<10)?'0'.$values['planned_date']['minute']:$values['planned_date']['minute'];
      $ampm = $values['planned_date']['ampm'];

      $date_str = $hour . ':' . $minute . ' ' . $ampm;

      $localeObject = Zend_Registry::get('Locale');
      $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
      $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
      $dateLocaleString = strtolower($dateLocaleString);
      $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
      $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('y', 'm', 'd'), $dateLocaleString);

      preg_match('/^(\d+)\/(\d+)\/(\d+)$/', $date, $m);

      array_shift($m);
      $year = $m[stripos($dateLocaleString, 'y')];
      $month = $m[stripos($dateLocaleString, 'm')];
      $day = $m[stripos($dateLocaleString, 'd')];
      $date_str = $month . '/' . $day. '/' . $year . ' ' . $date_str;

      $this->planned_date = date('Y-m-d H:i:s', strtotime($date_str));
    } else {
      $this->planned_date = date('Y-m-d H:i:s', Engine_Api::_()->updates()->getTimestamp());
    }
  }

  public function sendCampaign( $perminute_items = 0 )
  {
    /**
     * @var $settings Core_Api_Settings
     */
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $perminute_items = (int) ( $perminute_items ) ? $perminute_items : $settings->__get('updates.perminut.itemnumber');

    $tasksTbl = Engine_Api::_()->getDbTable('tasks', 'updates');
    $tasksRow = $tasksTbl->getCurrentTask($this->campaign_id, 'campaign');

    $sent = $tasksRow->sent;

    $totalRecipients = $tasksRow->total_recipients;

    if ($this->type == 'schedule') {
      $now = Engine_Api::_()->updates()->getTimestamp();
      $planned_date = strtotime($this->planned_date);

      if ($planned_date > $now) {
        return;
      } else {
        $tasksRow->scheduled = 0;
      }
    }

    $mailChimpMembers = 0;
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $mailService = $settings->__get('updates.mailservice');
    $api = '';
    $list_id = '';

    if ($mailService == 'mailchimp') {
      include_once 'application/modules/Updates/Api/MCAPI.class.php';
      $list_id = $settings->__get('updates.mailchimp.listid');
      $apiKey = $settings->__get('updates.mailchimp.apikey');
      $api = new MCAPI($apiKey);
      $mailChimpResult = $api->listMembers($list_id, 'subscribed', null, 0, 15000);
      $mailChimpMembers = $mailChimpResult['data'];

      if ($sent >= count($mailChimpMembers)) {
        $this->finished = 1;
        $tasksRow->finished = 1;
        $this->save();
        $tasksRow->save();
        return;
      }
    }
    else {
      if ($sent >= $totalRecipients) {
        $this->finished = 1;
        $tasksRow->finished = 1;
        $this->save();
        $tasksRow->save();
        return;
      } else {
        $receivedUsersCount = 0;
        $receivedSubscribersCount = 0;
        $subscribersTbl = Engine_Api::_()->getDbtable('subscribers', 'updates');
        if (!$settings->__get('updates.users.disabled')) {
          $receivedUsersCount = $subscribersTbl->getReceivedUserCount($this->campaign_id, 'campaign');
        }
        if (!$settings->__get('updates.subscribers.disabled')) {
          $receivedSubscribersCount = $subscribersTbl->getReceivedEmailCount($this->campaign_id, 'campaign');
        }
        $totalReceivedRecipients = (int)($receivedUsersCount + $receivedSubscribersCount);
        if ($totalReceivedRecipients >= $totalRecipients) {
          $this->finished = 1;
          $this->sent = $totalReceivedRecipients;
          $tasksRow->sent = $totalReceivedRecipients;
          $tasksRow->finished = 1;
          $tasksRow->save();
          $this->save();
          return;
        }
      }
    }

    /**
     * @var $view Zend_View
     * @var $mail Core_Api_Mail
     * @var $templateTb Updates_Model_DbTable_Templates
     */

    $view = Zend_Registry::get('Zend_View');
    $mail = Engine_Api::_()->getApi('mail', 'core');

    $recipients = $this->getRegisteredRecipients($perminute_items);
    
    if ($perminute_items > $recipients->count() && $this->recievers['subscribers'])
    {
      $limit = (int)($perminute_items - $recipients->count());
      $subscribersTb = Engine_Api::_()->getDbtable('subscribers', 'updates');
      $subscribers = $subscribersTb->getSubscribedEmails($this->campaign_id, $limit, 'campaign');
    }

    $templateTb = Engine_Api::_()->getDbtable('templates', 'updates');
    $template = $templateTb->getTemplate($this->template_id);

    $widgetsVariables = $templateTb->getWidgetsVariables($template->message);

    $widgetsVariablesReplaced = array();
    $i = -1;
    foreach($widgetsVariables['replaces'] as $widget)
    {
      $i++;
      $widget = str_replace(
      array('href="', "href='", 'src="', "src='"),
      array(
           'target="_blank" href="http://'.$_SERVER['HTTP_HOST'],
           "target='_blank' href='http://".$_SERVER['HTTP_HOST'],
           'src="http://' . $_SERVER['HTTP_HOST'],
           "src='http://" . $_SERVER['HTTP_HOST'],
      ), $widget);
      $widgetsVariablesReplaced['replaces'][$i] = $widget;
    }

    $message = str_replace($widgetsVariables['keys'], $widgetsVariablesReplaced['replaces'], $template->message);

    $remove = array("\n", "\r\n", "\r");
    $message = str_replace($remove, ' ', $message);

    $message .= "<img src='http://" . $_SERVER['HTTP_HOST'] . $view->baseUrl() . "/updates/ajax/image/campaigns/".$this->campaign_id."' border='0'/>";

    $translate = '';
    $from = '';
    $swift = '';
    $type = '';
    $opts = array();

    if ($mailService == 'mailchimp')
    {
      $type = 'regular';
      $list_id = $settings->__get('updates.mailchimp.listid');
      $opts['list_id'] = $list_id;
      $opts['subject'] = $settings->__get('updates.mailchimp.subject');
      $opts['from_email'] = $settings->__get('updates.mailchimp.fromemail');
      $opts['from_name'] = $settings->__get('updates.mailchimp.fromname');
      $opts['title'] = $settings->__get('updates.mailchimp.title');
      $opts['tracking'] = array('opens' => true, 'html_clicks' => true, 'text_clicks' => false);
      $opts['authenticate'] = true;
    }
    elseif ($mailService == 'sendgrid')
    {
      include_once 'application/modules/Updates/Api/SendGrid_loader.php';

      // Login credentials
      $username = $settings->__get('updates.sendgrid.username');
      $password = $settings->__get('updates.sendgrid.password');

      // Get admin info
      $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
      $fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');

      $translate = Zend_Registry::get('Zend_Translate');

      $sendgrid = new SendGrid($username, $password);
    }

    // SEND UPDATES TO REGISTERED USERS
    if ($mailService != 'mailchimp' && $sent < $totalRecipients)
    {
      foreach($recipients as $recipient)
      {
        if ($mailService == 'mailchimp') {
          break;
        }
        $standardVariables = $templateTb->getStandardVariables($recipient);

        $params['subject'] = str_replace($standardVariables['keys'], $standardVariables['replaces'], $template->subject);
        $params['message'] = str_replace($standardVariables['keys'], $standardVariables['replaces'], $message);

        $suggestWidgetsVariables = $templateTb->getSuggestWidgetsVariables($params['message'], $recipient);

        if ($suggestWidgetsVariables)
        {
          $params['message'] = str_replace($suggestWidgetsVariables['keys'], $suggestWidgetsVariables['replaces'], $params['message']);
          $remove = array("\n", "\r\n", "\r");
          $params['message'] = str_replace($remove, ' ', $params['message']);
        }

        $params['message'] = Engine_Api::_()->updates()->urlsEncode($params['message'], $this->campaign_id, 'campaign');
        $params['message'] = str_replace('\\','', $params['message']);

        if ($mailService == 'socialengine')
        {
          if ($mail->sendSystemRaw($recipient, 'campaign', $params) instanceof Core_Api_Mail)
          {
            $recipient->updates_campaign_id = $this->campaign_id;
            $recipient->disableHooks(true);
            $recipient->save();
            $recipient->disableHooks(false);
            $sent++;
          }
        }
        elseif ($mailService == 'sendgrid')
        {
          $messageBody = $params['message'];

          if( !empty($recipient->language) ) {
            $recipientLanguage = $recipient->language;
          } else {
            $recipientLanguage = $translate->getLocale();
          }

          // Get subject
//          $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
//          $subject  = (string) $mailForSendGrid->translate($subjectKey,  $recipientLanguage);
          $subject = str_replace($standardVariables['keys'], $standardVariables['replaces'], $template->subject);

          // Create a message (subject)
          include SENDGRID_ROOT_DIR . 'Mail.php';
          $sendgridMail = new SendGrid\Mail();

          $sendgridMail->
            addTo($recipient->email, $recipient->displayname)->
            setFrom($fromAddress)->
            setFromName($fromName)->
            setSubject($subject)->
            setText(strip_tags($messageBody))->
            setHtml($messageBody);

          $result = $sendgrid->
            web->
            send($sendgridMail);

          // send message
          if ($result) {
            $recipient->updates_campaign_id = $this->campaign_id;
            $recipient->save();
            $sent++;
          }
          else {
            echo "Something went wrong - " . $result;
            exit;
          }
          unset($sendgridMail);
        }
        if ($sent >= $totalRecipients) {
          break;
        }
      }
    }

    if ($mailService == 'mailchimp')
    {
      $messageBody = str_replace(array('[displayname]', '[email]', '[notifications]'),
                                         array("*|FNAME|* *|LNAME|*", "*|EMAIL|*", "*|NOTIFMERGE|*"),
                                         $message);
      $content = array('html'=>$messageBody,
            'text' => 'text text text *|UNSUB|*'
      );

      $campaign_id = $api->campaignCreate($type, $opts, $content);

      if ($api->errorCode) {
        echo "Unable to Create New Campaign!\n";
        echo "\tCode=".$api->errorCode."\n";
        echo "\tMsg=".$api->errorMessage."\n";
        exit;
      }

      $api->campaignSendNow($campaign_id);

      if ($api->errorCode) {
        echo "Unable to Send Campaign!\n";
        echo "\tCode=".$api->errorCode."\n";
        echo "\tMsg=".$api->errorMessage."\n";
        exit;
      }

      $sent = count($mailChimpMembers);

      if ($api->errorCode) {
        echo "Unable to load listMembers()!";
        echo "\n\tCode=".$api->errorCode;
        echo "\n\tMsg=".$api->errorMessage."\n";
        exit;
      }
      unset($campaign_id);
    }

    // SEND UPDATES TO SUBSCRIBERS
    if (isset($subscribers) && $subscribers->count() > 0 && $mailService != 'mailchimp' && $sent < $totalRecipients)
    {
      foreach($subscribers as $subscriber)
      {
        $standardVariables = $templateTb->getStandardVariables($subscriber);
        $params['subject'] = str_replace($standardVariables['keys'], $standardVariables['replaces'], $template->subject);
        $params['message'] = str_replace($standardVariables['keys'], $standardVariables['replaces'], $message);

        if ($mailService == 'socialengine')
        {
          if ($mail->sendSystemRaw($subscriber->email_address, 'campaign', $params) instanceof Core_Api_Mail)
          {
            $subscriber->campaign_id = $this->campaign_id;
            $subscriber->save();
            $sent++;
          }
        }
        elseif ($mailService == 'sendgrid')
        {
          $messageBody = $params['message'];

          if( !empty($subscriber->language) ) {
            $recipientLanguage = $subscriber->language;
          } else {
            $recipientLanguage = $translate->getLocale();
          }

          // Get subject
//          $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
//          $subject  = (string) $mailForSendGrid->translate($subjectKey,  $recipientLanguage);
          $subject = str_replace($standardVariables['keys'], $standardVariables['replaces'], $template->subject);

          // Create a message (subject)
          $sendgridMail = new SendGrid\Mail();

          $sendgridMail->
            addTo($subscriber->email_address, $subscriber->name)->
            setFrom($fromAddress)->
            setFromName($fromName)->
            setSubject($subject)->
            setText(strip_tags($messageBody))->
            setHtml($messageBody);

          $result = $sendgrid->
            web->
            send($sendgridMail);

          // send message
          if ($result) {
            //echo 'Message sent out to '.$recipients.' users';
            $subscriber->campaign_id = $this->campaign_id;
            $subscriber->save();
            $sent++;
          }
          else {
            echo "Something went wrong - " . $result;
            exit;
          }
          unset($sendgridMail);
        }
        if ($sent >= $totalRecipients) {
          break;
        }
      }
    }

    $this->sent = $sent;
    $tasksRow->sent = $sent;

    if ($mailService == 'mailchimp') {
      if ($sent >= count($mailChimpMembers)) {
        $this->finished = 1;
        $tasksRow->finished = 1;
      }
    }
    else {
      if ($sent >= $totalRecipients) {
        $this->finished = 1;
        $tasksRow->finished = 1;
      }
    }

    $this->save();
    $tasksRow->save();

    return;
  }
}
