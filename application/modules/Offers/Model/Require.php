<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Require.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Model_Require extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = array();

  public function complete(Core_Model_Item_Abstract $owner, $item_id = 0, $page_id = 0)
  {
    try{
      $table = Engine_Api::_()->getDbTable('complete', 'offers');
      $select = $table->select()
        ->where('require_id = ?', $this->getIdentity())
        ->where('object_type = ?', $owner->getType())
        ->where('object_id = ?', $owner->getIdentity())
        ->where('offer_id = ?', $this->offer_id)
      ;

      if ($page_id) {
        $select->where('page_id = ?', $page_id);
      }


      $complete = $table->fetchRow($select);

      if (!$complete) {
        $complete = $table->createRow();
        $complete->setFromArray(array(
          'require_id' => $this->getIdentity(),
          'offer_id' => $this->offer_id,
          'type' => $this->type,
          'object_type' => $owner->getType(),
          'object_id' => $owner->getIdentity(),
          'creation_date' => date('Y-m-d H:i:s'),
          'page_id' => $page_id,
          'item_id' => $item_id,
        ));
        $complete->save();
      }
    } catch(Exception $e){print_log($e->__toString());}
  }

  public function delete()
  {
    parent::delete();

    foreach (Engine_Api::_()->getDbTable('complete', 'offers')->fetchAll(array('require_id = ?' => $this->getIdentity())) as $item) {
      $item->delete();
    }
  }
}