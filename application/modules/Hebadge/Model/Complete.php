<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Complete.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Model_Complete extends Engine_Db_Table_Row
{

  public function checkIsComplete(Core_Model_Item_Abstract $owner)
  {
    $badge = Engine_Api::_()->getItem('hebadge_badge', $this->badge_id);
    if (!$badge){
      return;
    }
    if (!$badge->enabled){
      return ;
    }

    $table = Engine_Api::_()->getDbTable('require', 'hebadge');
    $select = $table->select()
        ->from(array('r' => $table->info('name')), new Zend_Db_Expr('r.*, IF(ISNULL(c.require_id), 0,1) AS complete'))
        ->joinLeft(array('c' => $this->getTable()->info('name')), 'c.require_id = r.require_id AND c.object_type = "' . $owner->getType() . '" AND c.object_id = ' . $owner->getIdentity(), array())
        ->where('r.badge_id = ?', $badge->getIdentity())
        ->where('r.type IN (?)', array_keys(Engine_Api::_()->hebadge()->getRequireList())); // check is active


    $is_complete = true;
    foreach ($table->fetchAll($select) as $item){
      if (!$item->complete){
        $is_complete = false;
      }
    }

    if ($is_complete){

      $badge->addMember($owner);

      if (!Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hebadge.user_approved', 0)){
        $badge->setApprovedMember($owner);
      }

      if (!Engine_Api::_()->getDbTable('notifications', 'activity')->getNotificationByObjectAndType($owner, $badge, 'hebadge_new')){
        Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($owner, $owner, $badge, 'hebadge_new');
      }




    }


  }


}