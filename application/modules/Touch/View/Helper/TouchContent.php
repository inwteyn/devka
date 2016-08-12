<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchContent.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_TouchContent extends Zend_View_Helper_Abstract
{
	protected $_name;
	
  public function touchContent($name)
  {
    // Direct access
    if( func_num_args() == 0 )
    {
      return $this;
    }

    if( func_num_args() > 1 )
    {
      $name = func_get_args();
    }

    $content = Engine_Content::getInstance();

		$table = Engine_Api::_()->getDbtable('pages', 'touch');

		$content->setStorage($table);
		
    return $content->render($name);
  }

  public function renderWidget($name)
  {
    $structure = array(
      'type' => 'widget',
      'name' => $name,
    );

    // Create element (with structure)
    $element = new Engine_Content_Element_Container(array(
      'elements' => array($structure),
      'decorators' => array(
        'Children'
      )
    ));

    return $element->render();
  }
}