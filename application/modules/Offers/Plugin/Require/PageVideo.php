<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Video.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Offers_Plugin_Require_PageVideo extends Offers_Plugin_Require_Abstract
{


  public function check(Core_Model_Item_Abstract $owner, $new_item_id = null, $page_id)
  {
    $count = $this->getCount($owner, $new_item_id, $page_id);

    foreach ($this->getRequire() as $require){
      if (empty($require->params) || empty($require->params['count'])){
        continue ;
      }
      if ( $count >= $require->params['count'] ){
        $require->complete($owner, $new_item_id->getIdentity(), $page_id);
      }
    }

  }

  public function getCount(Core_Model_Item_Abstract $owner, $new_item_id = null, $page_id)
  {
    $table = Engine_Api::_()->getDbTable('pagevideos', 'pagevideo');
    $select = $table->select()
        ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
        ->where('user_id = ?', $owner->getIdentity())
        ->where('page_id = ?', $page_id);

    if (!empty($new_item_id)){
      $select->where('pagevideo_id != ?', $new_item_id->getIdentity());
    }
    
    // print_log($select . '');

    $count = $table->getAdapter()->fetchOne($select);

    // print_log($count);

    if (!empty($new_item_id)){
      $count++;
    }

    $this->getInfoPage($owner, $page_id)->setFromArray(array(
      'pagevideo' => $count
    ))->save();


    return $count;

  }

}