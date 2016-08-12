<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Module.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Form_Admin_Layout_Testemail extends Engine_Form
{
	protected $_module_name;

	public function setParams($name)
	{
		$this->_module_name = $name;
	}

  public function init()
  {
		$this->setTitle('UPDATES_Send to test email address');
		$this->setDescription('UPDATES_FORM_ADMIN_UPLAYOUT_TESTEMAIL_DESCRIPTION');
		$mail = Engine_Api::_()->getApi('settings', 'core')->core_mail;

		$this->addElement('text', 'test_email', array(
			'label'=>'Email:',
			'size'=>50,
			'autocomplete'=>'on',
			'required' => true,
			'trim' => true,
			'value' => $mail['from'],
			'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
			'id' =>'test_email',
		));

    $this->addElement('hidden', 'subject', array(
      'type'=>'hidden',
      'required'=>false,
      'trim'=>true,
    ));

    $this->addElement('Textarea', 'message', array(
      'style'=>'display:none',
      'required'=>false,
      'trim'=>true,
      'decorators' => array('ViewHelper'),
    ));


 	  $this->addElement('Button', 'submit', array(
			'label' => 'Send email',
			'onclick'=>'$savechanges = true;',
			'type' => 'submit',
			'decorators' => array('ViewHelper'),
			'ignore' => true,)
    );

    $this->addElement('Cancel', 'cancel', array(
			 'label' => 'cancel',
			 'link' => true,
			 'prependText' => ' or ',
       'href' => '',
       'onclick' => 'parent.Smoothbox.close();',
			 'ignore' => true,
			 'decorators' => array('ViewHelper'),)
    );

		$this->addDisplayGroup(array('submit', 'cancel',),'buttons');
  }
}