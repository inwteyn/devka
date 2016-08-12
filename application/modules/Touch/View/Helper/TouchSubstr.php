<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchSubstr.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_TouchSubstr extends Zend_View_Helper_Abstract
{
	public function touchSubstr($str, $count = 50)
	{
		$count_tmp = (int) ($count - 1);
		return Engine_String::substr($str, 0, $count) . ((Engine_String::strlen($str) > $count_tmp)? '...':'');
	}
}
