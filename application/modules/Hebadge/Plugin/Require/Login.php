<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Login.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Plugin_Require_Login extends Hebadge_Plugin_Require_Abstract
{


  public function check(Core_Model_Item_Abstract $owner,  $new_item_id = NULL)
  {
    $count = $this->getCount($owner);

    foreach ($this->getRequire() as $require){
      if (empty($require->params) || empty($require->params['count'])){
        continue ;
      }
      if ( $count >= $require->params['count'] ){
        $require->complete($owner);
      }
    }

  }

  public function getCount(Core_Model_Item_Abstract $owner)
  {
    $info = Engine_Api::_()->getDbTable('info', 'hebadge')->getInfo($owner);
    if (!$info){
      return ;
    }

    // print_log($info->login);

    return $info->login;

  }

}