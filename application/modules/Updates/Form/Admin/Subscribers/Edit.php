<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Form_Admin_Subscribers_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('id', 'admin_subscribers_edit')
      ->setTitle('UPDATES_Edit Subscriber')
      ->setDescription('UPDATES_You can change the details of this subscriber\'s account here.');

    // init email
    $this->addElement('Text', 'name', array(
      'label' => 'UPDATES_Name',
    	'size' => 50,
    ));

    // init username
    $this->addElement('Text', 'email_address', array(
      'label' => 'UPDATES_Email Adress',
    	'size' => 50,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'UPDATES_Save Changes',
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
    $button_group->addDecorator('DivDivDivWrapper');



    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}