<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Headvancedmembers_View_Helper_User extends Zend_View_Helper_Abstract
{
  public function user($identity)
  {
    $user = Engine_Api::_()->user()->getUser($identity);
    if( !$user )
    {
      throw new Zend_View_Exception('User does not exist');
    }
    return $user;
  }
}