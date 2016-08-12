<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Audios.php 09.09.11 17:03 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Model_DbTable_Addons extends Engine_Db_Table
{
  protected $_rowClass = 'Store_Model_Addon';

  public function getAvailableAddon()
  {
    $addon = $this->getAvailableAddons(1);
    return $addon;
  }

  public function getAvailableAddons($limit = 0)
  {
    $select = $this->select()->from(array('a' => $this->info('name')))
      ->setIntegrityCheck(false)
      ->joinInner(array('m' => $this->getTablePrefix() . 'core_modules'), 'a.name=m.name')
      ->where('m.enabled=?', 1)
      ->order('a.name')
    ;

    if($limit) {
      $select->limit($limit);
    }

    return ($limit == 1) ? $this->fetchRow($select) : $this->fetchAll($select);
  }

}
