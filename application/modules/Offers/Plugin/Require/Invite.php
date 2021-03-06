<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Invite.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Offers_Plugin_Require_Invite extends Offers_Plugin_Require_Abstract
{


  public function check(Core_Model_Item_Abstract $owner, $new_item_id = null)
  {
    $count = $this->getCount($owner, $new_item_id);
    foreach ($this->getRequire() as $require){
      if (empty($require->params) || empty($require->params['count'])){
        continue ;
      }
      if ( $count >= $require->params['count'] ){
        $require->complete($owner);
      }
    }

  }

  public function getCount(Core_Model_Item_Abstract $owner, $new_item_id = null)
  {
    $table = Engine_Api::_()->getDbTable('invites', 'invite');
    $select = $table->select()
        ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
        ->where('user_id = ?', $owner->getIdentity());

    if (!empty($new_item_id)){
      $select->where('id != ?', $new_item_id);
    }
    
    // // print_log($select . '');

    $count = $table->getAdapter()->fetchOne($select);

    // print_log($count);

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('inviter')){

      $table = Engine_Api::_()->getDbTable('invites', 'inviter');
      $select = $table->select()
          ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
          ->where('user_id = ?', $owner->getIdentity());

      if (!empty($new_item_id)){
        $select->where('id != ?', $new_item_id);
      }

      // // print_log($select . '');

      $count += $table->getAdapter()->fetchOne($select);

    }

    // print_log($count);


    if (!empty($new_item_id)){
      $count++;
    }

    $this->getInfo($owner)->setFromArray(array(
      'invite' => $count
    ))->save();


    return $count;

  }

}