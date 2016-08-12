<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 17.02.12
 * Time: 15:28
 * To change this template use File | Settings | File Templates.
 */
class Touch_Plugin_User extends Zend_Controller_Plugin_Abstract
{
  public function onUserCreateAfter($event)
  {
    if(Engine_Api::_()->touch()->isTouchMode())
      Engine_Api::_()->getDbTable('statistics', 'core')->increment('touch.user.creations');
  }
}
