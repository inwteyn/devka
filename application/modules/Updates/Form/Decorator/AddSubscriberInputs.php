<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AddSubscriberInputs.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Engine_Form_Decorator_AddSubscriberInputs extends Zend_Form_Decorator_Abstract
{
	protected $_placement = null;
	
	public function render($content)
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$content = str_replace('</label>', '</label><br/>', $content);
		return "<div>" 
						. $content 
						. "</div>"
						. "<div style='clear: both;'></div>
							<div>
								<br/>
								<a href='javascript://' id='add_more' style='text-decoration: none; padding: 3px; font-weight: bold;'>+&nbsp;" 
						. $translate->translate('UPDATES_add more') 
						. "</a></div>";
	}
}
