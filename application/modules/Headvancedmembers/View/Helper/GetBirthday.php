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
class Headvancedmembers_View_Helper_GetBirthday extends Zend_View_Helper_Abstract
{
  public function getBirthday($user, $viewer = null)
  {

    $profileTypeId = $this->getProfileType($user);
    if (!$profileTypeId)
      return false;
    $fieldsMapsTable = new Fields_Model_DbTable_Meta("user", "maps");
    $fieldsMetaTable = new Fields_Model_DbTable_Meta("user", "meta");

    $select = $fieldsMetaTable->select()
      ->setIntegrityCheck(false)
      ->from(array("meta" => $fieldsMetaTable->info('name')), array("meta.field_id"))
      ->where("type = ?", "birthdate")
      ->joinLeft(array("maps" => $fieldsMapsTable->info('name')), "meta.field_id = maps.child_id", array())
      ->where("option_id = ?", $profileTypeId);

    $result = $fieldsMetaTable->fetchAll($select)->toArray();

    if (!empty($result)) {
      $birthdayFieldId=  $result[0]['field_id'];
    }
    $fieldsValuesTable = new Fields_Model_DbTable_Values("user", "values");
    $myBirthdaySql = $fieldsValuesTable->select()->where("field_id = ?", $birthdayFieldId)->where("item_id = ?", $user->getIdentity());
    $myBirthdayRow = $fieldsValuesTable->fetchAll($myBirthdaySql)->toArray();
    if($myBirthdayRow) {
      return $this->get_age($myBirthdayRow[0]['value']);
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
  public function get_age($birthday)
  {
    list($by, $bm, $bd) = explode('-', $birthday);
    list($cd, $cm, $cy) = explode('-', date('d-m-Y'));
    $cd -= $bd;
    $cm -= $bm;
    $cy -= $by;
    if ($cd < 0) $cm--;
    if ($cm < 0) $cy--;
    return $cy;
  }
}