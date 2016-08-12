<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 26.05.2015
 * Time: 14:42
 */

class Wall_Model_Comment extends Core_Model_Comment{

  public function getAuthorizationItem()
  {
    return $this;
  }
  public function isLike($viwer){

    return $this->getLike($this,$viwer);
  }
  public function getLike($resource, Core_Model_Item_Abstract $poster)
  {
    $table = Engine_Api::_()->getDbTable('likes','core');
    $select = $table->select()
      ->where('poster_type = ?', 'user')
      ->where('poster_id = ?', $poster->getIdentity())
      ->where('resource_type = ?','activity_comment')
      ->where('resource_id = ?', $resource->getIdentity())
      ->limit(1);

    return $table->fetchRow($select);
  }
}