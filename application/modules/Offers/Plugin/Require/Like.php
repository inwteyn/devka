<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Like.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Plugin_Require_Like extends Offers_Plugin_Require_Abstract
{
  public function check(Core_Model_Item_Abstract $owner, $new_item_id = null, $page_id = null)
  {
    $count = $this->getCount($owner, $new_item_id, $page_id);

    foreach ($this->getRequire() as $require) {
      if (empty($require->params) || empty($require->params['count'])) {
        continue;
      }
      if ($count >= $require->params['count']) {
        $require->complete($owner, $page_id, $new_item_id);
      }
    }
  }

  public function getCount(Core_Model_Item_Abstract $owner, $new_item_id = null, $page_id = null)
  {
    $table = Engine_Api::_()->getDbTable('likes', 'core');
    $select = $table->select()
      ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
      ->where('poster_type = ?', $owner->getType())
      ->where('poster_id = ?', $owner->getIdentity())
      ->where('resource_type = ?', 'user');

    if ($page_id > 0) {
      $select->where('resource_id = ?', $page_id);
    }

    if (!empty($new_item_id)) {
      $select->where('like_id != ?', $new_item_id);
    }

    $count = $table->getAdapter()->fetchOne($select);

    if (!empty($new_item_id)) {
      $count++;
    }

    $this->getInfo($owner)->setFromArray(array(
      'like' => $count
    ))->save();

    if ($page_id > 0) {
      $this->getInfoPage($owner, $page_id)->setFromArray(array(
        'like' => $count
      ))->save();
    }

    return $count;
  }
}