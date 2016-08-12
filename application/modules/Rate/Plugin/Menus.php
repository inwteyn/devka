<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Menus.php 7244 2010-09-01 01:49:53Z michael $
 * @author     michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */

class Rate_Plugin_Menus
{
  public function canReviewManage()
  {
      $return = false;
      if((Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page'))){
          $return = true;
      }
      if((Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store'))){
          $return = true;
      }
    return $return;
  }

  public function canOfferReviewManage()
  {
    return (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('offers')) ? true : false;
  }
}