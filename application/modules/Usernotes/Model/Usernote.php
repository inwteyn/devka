<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Usernote.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_Model_Usernote extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'user';

  protected $_parent_is_owner = false;
}