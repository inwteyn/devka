<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 29.02.12
 * Time: 17:35
 * To change this template use File | Settings | File Templates.
 */
class Touch_View_Helper_TouchGetItem  extends Zend_View_Helper_Abstract
{
  public function touchGetItem($item_name, $item_id){
    return Engine_Api::_()->getItem($item_name, $item_id);
  }
}
