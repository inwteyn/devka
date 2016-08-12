<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Headvancedmembers_View_Helper_GetGender extends Zend_View_Helper_Abstract
{
    public function getGender($user, $viewer = null)
  {
      $profileTypeId = $this->getProfileType($user);

    if (!$profileTypeId)
      return false;
    $fieldsMapsTable = new Fields_Model_DbTable_Meta("user", "maps");
    $fieldsMetaTable = new Fields_Model_DbTable_Meta("user", "meta");

    $select = $fieldsMetaTable->select()
      ->setIntegrityCheck(false)
      ->from(array("meta" => $fieldsMetaTable->info('name')), array("meta.field_id"))
      ->where("type = ?", "gender")
      ->joinLeft(array("maps" => $fieldsMapsTable->info('name')), "meta.field_id = maps.child_id", array())
      ->where("option_id = ?", $profileTypeId);

    $result = $fieldsMetaTable->fetchAll($select)->toArray();

    if (!empty($result)) {
      $birthdayFieldId=  $result[0]['field_id'];
    }

    $fieldsValuesTable = new Fields_Model_DbTable_Values("user", "values");
    $myBirthdaySql = $fieldsValuesTable->select()->where("field_id = ?", $birthdayFieldId)->where("item_id = ?", $user->getIdentity());
    $myBirthdayRow = $fieldsValuesTable->fetchAll($myBirthdaySql)->toArray();
    if(!empty($myBirthdayRow)){
      $fieldsValuesTable = new Fields_Model_DbTable_Options("user", "options");
      $myBirthdaySql = $fieldsValuesTable->select()->where("field_id = ?", $myBirthdayRow[0]['field_id'])->where("option_id = ?", $myBirthdayRow[0]['value']);
      $myBirthdayRow1 = $fieldsValuesTable->fetchAll($myBirthdaySql)->toArray();
      if($myBirthdayRow1) {
         return ($myBirthdayRow1[0]['label']);
      }
    }

    return false;
  }
  public function getProfileType($user)
  {
    $fieldsSearchTable = new Fields_Model_DbTable_Search("user", "search");
    $profileTypeSql = $fieldsSearchTable->select()->where("item_id = ?", $user->getIdentity());
    $profileTypeRow = $fieldsSearchTable->fetchRow($profileTypeSql);

    if(!$profileTypeRow || !is_object($profileTypeRow))
      return "";
    $profileType = $profileTypeRow->toArray();
    return $profileType['profile_type'];
  }
}