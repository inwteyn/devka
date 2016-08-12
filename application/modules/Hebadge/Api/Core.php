<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Hebadge_Api_Core extends Core_Api_Abstract
{

  public function getRequireList()
  {
    $enable_modules = Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames();

    $data = array();
    foreach (Zend_Registry::get('Engine_Manifest') as $item) {
      if (empty($item['hebadge'])) {
        continue;
      }
      foreach ($item['hebadge'] as $type => $item2) {
        if (!empty($item2['module']) && !in_array($item2['module'], $enable_modules)) {
          continue;
        }
        $data[$type] = $item2;
      }
    }
    return $data;
  }

  public function getRequire($type)
  {
    if (!$type) {
      return;
    }
    $requires = $this->getRequireList();
    if (!array_key_exists($type, $requires)) {
      return;
    }
    return $requires[$type];
  }


  public function getRequireClass($type)
  {
    $require = $this->getRequire($type);
    if (!$require) {
      return;
    }
    return Engine_Api::_()->loadClass(@$require['plugin']);
  }


  public function setItemsType($items)
  {
    $types = array();
    foreach ($items as $item) {

      $type = $item['type'];
      $id = $item['id'];

      if (!isset($types[$type])) {
        $types[$type] = array();
      }
      $types[$type][] = $id;

    }
    return $types;
  }


  public function setItemsGuid($items)
  {
    $new_items = array();
    foreach ($items as $item) {

      $guid = $item['type'] . '_' . $item['id'];

      if (!isset($new_items[$guid])) {
        $new_items[$guid] = array();
      }
      $new_items[$guid] = $item;

    }
    return $new_items;
  }


  public function guidsToItems($guids)
  {
    $items = array();
    if (!empty($guids)) {
      foreach ($guids as $guid) {
        $parts = explode('_', $guid);
        if (count($parts) == 2) {
          $items[] = array(
            'type' => $parts[0],
            'id' => $parts[1]
          );
        }
      }
    }
    return $items;
  }


  public function getItems($items)
  {
    $item_array = array();

    foreach ($this->setItemsType($items) as $type => $ids) {

      if (!Engine_Api::_()->hasItemType($type)) {
        continue;
      }
      $table = Engine_Api::_()->getItemTable($type);

      $matches = $table->info('primary');
      $primary = array_shift($matches);
      if (!$primary) {
        continue;
      }

      foreach ($this->getTableItems($table, $ids) as $item) {
        if (!isset($item_array[$type])) {
          $item_array[$type] = array();
        }
        $item_array[$type][$item->{$primary}] = $item;
      }

    }

    $ready_items = array();
    foreach ($items as $item) {

      $type = $item['type'];
      $id = $item['id'];

      if (!isset($item_array[$type]) || !isset($item_array[$type][$id])) {
        continue;
      }
      $ready_items[] = $item_array[$type][$id];
    }

    return $ready_items;

  }


  public function getTableItems(Zend_Db_Table_Abstract $table, $ids)
  {
    try {
      $matches = $table->info('primary');
      $primary = array_shift($matches);
      if (!$primary) {
        return array();
      }
      if (empty($ids)) {
        return array();
      }
      $select = $table->select()
        ->where("$primary IN (?)", $ids);

      return $table->fetchAll($select);

    } catch (Exception $e) {
      throw $e;
      return;
    }
  }
  
}