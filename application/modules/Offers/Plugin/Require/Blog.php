<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Blog.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Offers_Plugin_Require_Blog extends Offers_Plugin_Require_Abstract
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
    $table = Engine_Api::_()->getDbTable('blogs', 'blog');
    $select = $table->select()
        ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
        ->where('owner_type = ?', $owner->getType())
        ->where('owner_id = ?', $owner->getIdentity())
        ->where('draft = 0');

    if (!empty($new_item_id)){
      $select->where('blog_id != ?', $new_item_id);
    }

    // // print_log($select . '');

    $count = $table->getAdapter()->fetchOne($select);

    // // print_log($count);

    if (!empty($new_item_id)){
      $count++;
    }

    $this->getInfo($owner)->setFromArray(array(
      'blog' => $count
    ))->save();


    return $count;

  }


  public function getNextBadges(Core_Model_Item_Abstract $owner)
  {
    $info = $this->getInfo($owner);
    $count = 0;

    if (isset($info->blog)){
      $count = $info->blog;
    }
    if ($count === null){
      $count = $this->getCount($owner);
    }

    $badges = array();
    $require = array();


    // sort 1
    $count_require_in_badge = array();

    // sort 2
    $count_items_in_require = array();

    foreach ($this->getRequire() as $require){
      if (empty($require->params) || empty($require->params['count'])){
        continue ;
      }
      // $require->params['count']

      if (empty($badges[$require->badge_id])){
        $badges[$require->badge_id] = array();
      }
      $badges[$require->badge_id][] = $require->require_id;

      if (empty($badges[$require->badge_id])){
        $badges[$require->badge_id] = array();
      }
      $badges[$require->badge_id][] = $require->params['count'];

    }

    foreach ($badges as $key => $require_list){
      $count_require_in_badge[$key] = count($require_list);
    }


  }


}