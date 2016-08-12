<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Abstract.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Plugin_Require_Abstract
{
  public function getName()
  {
    $matches = explode("_", get_class($this));
    return strtolower(array_pop($matches));
  }

  public function getRequire()
  {
    $table = Engine_Api::_()->getDbTable('require', 'offers');
    $select = $table->select()
      ->where('type = ?', $this->getName())
    ;

    return $table->fetchAll($select);
  }

  public function check(Core_Model_Item_Abstract $owner, $new_item_id = null)
  {
  }

  /**
   * @param Core_Model_Item_Abstract $owner
   * @return Engine_Db_Table_Row
   */

  public function getInfo(Core_Model_Item_Abstract $owner)
  {
    $info = Engine_Api::_()->getDbTable('info', 'offers')->getInfo($owner);
    if (!$info){
      return ;
    }
    return $info;
  }

  public function getInfoPage(Core_Model_Item_Abstract $owner, $page_id)
  {
    $info = Engine_Api::_()->getDbTable('infopage', 'offers')->getInfoPage($owner, $page_id);

    if (!$info){
      return ;
    }
    return $info;
  }


}
