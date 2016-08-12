<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Require.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Model_Require extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = array();

  public function complete(Core_Model_Item_Abstract $owner)
  {
    $table = Engine_Api::_()->getDbTable('complete', 'hebadge');

    $select = $table->select()
        ->where('require_id = ?', $this->getIdentity())
        ->where('object_type = ?', $owner->getType())
        ->where('object_id = ?', $owner->getIdentity());

    $complete = $table->fetchRow($select);

    if (!$complete){
      
      $complete = $table->createRow();
      $complete->setFromArray(array(
        'require_id' => $this->getIdentity(),
        'badge_id' => $this->badge_id,
        'type' => $this->type,
        'object_type' => $owner->getType(),
        'object_id' => $owner->getIdentity(),
        'creation_date' => date('Y-m-d H:i:s')
      ));
      $complete->save();

      $complete->checkIsComplete($owner);

    }

  }

  public function delete()
  {
    parent::delete();

    foreach (Engine_Api::_()->getDbTable('complete', 'hebadge')->fetchAll(array('require_id = ?' => $this->getIdentity())) as $item){
      $item->delete();
    }

  }
  

}