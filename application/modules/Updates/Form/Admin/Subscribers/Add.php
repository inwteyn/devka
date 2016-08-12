<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Add.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Form_Admin_Subscribers_Add extends Engine_Form
{
  public function init()
  {
  	$path = Engine_Api::_()->getModuleBootstrap('updates')->getModulePath();
		$this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
		
    $this
      ->clearDecorators()
      ->setTitle('UPDATES_Add Subscriber')
      ->setDescription('UPDATES_ADMIN_ADD_SUBSCRIBERS_DESCRIPTION');

    $name = new Zend_Form_Element_Text('name1');
    $name
      ->setLabel('UPDATES_Name')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div', 'style' => 'float: left;', 'id' => 'name_div'));

    $email = new Zend_Form_Element_Text('email_address1');
    $email
      ->setLabel('UPDATES_Email')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div', 'style' => 'float: left; padding-left: 10px;', 'id' => 'email_div'));

    $this->addElements(array(
    	$name,
    	$email,
    ));
    
    $this->addDisplayGroup(array('name1', 'email_address1'), 'inputs');
    $input_group = $this->getDisplayGroup('inputs');
    $input_group->addDecorator('AddSubscriberInputs');
    
          
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'UPDATES_Add Subscriber(s)',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'UPDATES_cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');



    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}