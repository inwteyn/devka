<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_Search extends Touch_Form_Standard
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    parent::init();
    $path = Engine_Api::_()->getModuleBootstrap('touch')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
		
    $this->addElement('Text', 'search', array(
			'decorators' => array('ViewHelper'),
			'style'=>'width: 100%;',
    ));
		$this->search->addDecorator('SearchGroup', array('search'=>1));
		
		$this->addElement('Button', 'submit', array(
			'label' => 'Search',
			'type' => 'submit',
			'decorators' => array(
				'ViewHelper',
			),
		));
		$this->submit->addDecorator('SearchGroup', array('submit'=>1));

		$this->addDisplayGroup(array('search', 'submit'), 'elements');
		$this->elements->addDecorator('SearchGroup', array('group'=>1));
  }
}