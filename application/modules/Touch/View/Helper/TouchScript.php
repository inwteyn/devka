<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchScript.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_TouchScript extends Zend_View_Helper_Abstract
{
  public function touchScript($path, $attributes = array())
  {
    $attribs = '';
    foreach($attributes as $attr_name => $attr_val){
      $attribs .= $attr_name.'="'.$attr_val.'" ';
    }
		$script = '<script '.$attribs.' type="text/javascript" src="' .$path . '"></script>'."\n";

		return $script;
	}
}