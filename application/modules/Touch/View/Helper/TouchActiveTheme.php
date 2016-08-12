<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchActiveTheme.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_TouchActiveTheme extends Zend_View_Helper_Abstract
{
  public function touchActiveTheme()
  {
		$table = Engine_Api::_()->getDbtable('themes', 'touch');

		if (null === ($theme = $table->fetchRow($table->select()->where('active=?', 1)->limit(1))))
		{
			$theme = $table->fetchRow($table->select()->where('name=?', 'default')->limit(1));
		}

		return $theme->name;
	}
}
