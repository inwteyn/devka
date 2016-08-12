<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Suggest.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Offers_Plugin_Require_Suggest extends Offers_Plugin_Require_Abstract
{


  public function check(Core_Model_Item_Abstract $owner, $new_item_id = null, $page_id = 0)
  {
    $count = $this->getCount($owner, $new_item_id, $page_id);

    foreach ($this->getRequire() as $require){
      if (empty($require->params) || empty($require->params['count'])){
        continue ;
      }
      if ( $count >= $require->params['count'] ){
        $require->complete($owner, $page_id, $new_item_id);
      }
    }

  }

  public function getCount(Core_Model_Item_Abstract $owner, $new_item_id = null, $page_id = 0)
  {
    $table = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $select = $table->select()
        ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
        ->where('from_id = ?', $owner->getIdentity());

    if ($page_id > 0) {
      $select->where('object_id = ?', $page_id)
             ->where('object_type = \'page\'');
    }

    if (!empty($new_item_id)){
      $select->where('suggest_id != ?', $new_item_id);
    }
    
    // print_log($select . '');

    $count = $table->getAdapter()->fetchOne($select);

    // print_log($count);

    if (!empty($new_item_id)){
      $count++;
    }

    $this->getInfo($owner)->setFromArray(array(
      'suggest' => $count
    ))->save();

    if ($page_id > 0) {
      $this->getInfoPage($owner, $page_id)->setFromArray(array(
        'suggest' => $count
      ))->save();
    }


    return $count;

  }

}