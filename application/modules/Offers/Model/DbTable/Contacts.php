<?php

/**
 * SocialEngine
 *
 * @category
 * @package
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Contacts.php 27.08.12 Alex $
 * @author     Alex
 */
class Offers_Model_DbTable_Contacts extends Engine_Db_Table
{
  public function setContacts($offer_id, $values)
  {

    $db = Engine_Db_Table::getDefaultAdapter();
    if (!empty($values['country']) || !empty($values['state']) || !empty($values['city']) || !empty($values['address'])) {
      $location = Engine_Api::_()->offers()->getGeoLocationMarker($values);
    }

    $db->beginTransaction();
    try{
      $contacts = $this->createRow();
      $contacts->offer_id = $offer_id;
      $contacts->setFromArray($values);
      if ($location && !empty($location)) {
        $contacts->lat = $location['lat'];
        $contacts->lng = $location['lng'];
      }
      $contacts->save();
      $db->commit();

    }catch (Exception $e) {
      $db->rollBack();
    }
  }

  public function editContacts($offer_id, $values)
  {
    try{
      $db = $this->getAdapter();
      $location = Engine_Api::_()->offers()->getGeoLocationMarker($values);
      $db->beginTransaction();
      $select = $this->select()->where('offer_id = ?', $offer_id);
      $contacts = $this->fetchRow($select);
      if (!$contacts) {
        $contacts = $this->createRow(array('offer_id' => $offer_id));
      }
      $contacts->setFromArray($values);
      $contacts->lat = $location['lat'];
      $contacts->lng = $location['lng'];
      $contacts->save();
      $db->commit();
    }catch (Exception $e) {
      $db->rollBack();
      return false;
    }
  }

  public function getContacts($offer_id, array $contacts = array())
  {
    try {
      if (!empty($contacts)) {
        if (count($contacts) === 1) {
          return $this->getDefaultAdapter()
            ->fetchOne(
              $this->select()
              ->from($this->info('name'), $contacts)
              ->where('offer_id = ?', $offer_id)
            );
        } else {
          return $this->fetchRow($this->select()->from($this->info('name'), $contacts)->where('offer_id = ?', $offer_id));
        }
      } else {
        $select = $this->select()->where('offer_id = ?', $offer_id);
        $result = $this->fetchRow($select);

        if (!$result) {
          $result = $this->createRow(array('offer_id' => $offer_id));
        }
        return $result;
      }
    } catch (Exception $e) {
      return false;
    }
  }

  public function getPositionMarker($offer_id)
  {
    $select = $this->select()->from($this->info('name'), array('lat', 'lng'))->where('offer_id = ?', $offer_id);
    return $this->getDefaultAdapter()->fetchRow($select);
  }

}
