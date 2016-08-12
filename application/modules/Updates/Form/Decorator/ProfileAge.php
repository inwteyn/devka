<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: UpdatesTimeSelects.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Engine_Form_Decorator_ProfileAge extends Zend_Form_Decorator_Abstract
{
	protected $_placement = null;
	
	public function render($content)
	{
    $content .='&nbsp;' . str_replace('agemin', 'agemax', $content);
		return $content;
	}
}
