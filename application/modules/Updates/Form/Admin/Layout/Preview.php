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

class Updates_Form_Admin_Layout_Preview extends Engine_Form
{
	protected $_module_name;

	public function setParams($name)
	{
		$this->_module_name = $name;
	}

  public function init()
  {
		$settings = Engine_Api::_()->getApi('settings', 'core');
 	  $this->addElement('hidden', 'bgcolor', array(
 	  		  'required' => true,
      		'allowEmpty' => false,
 	  			'trim' => true,
			 		'id' =>'bgcolor',
			 		'order'=>1,
			 		'value'=>$settings->__get('updates.background.color')
 	  	));

		$this->addElement('hidden', 'fncolor', array(
 	  		  'required' => true,
      		'allowEmpty' => false,
 	  			'trim' => true,
			 		'id' =>'fncolor',
			 		'order'=>2,
			 		'value'=>$settings->__get('updates.font.color')
 	  			)
 	  	);

 	  $this->addElement('hidden', 'tlcolor', array(
				'required' => true,
				'allowEmpty' => false,
				'trim' => true,
				'id' =>'tlcolor',
			 	'order'=>3,
			 	'value'=>$settings->__get('updates.titles.color')
				)
	  );

 	  $this->addElement('hidden', 'lkcolor', array(
				'required' => true,
				'allowEmpty' => false,
				'trim' => true,
				'id' =>'lkcolor',
			 	'order'=>4,
			 	'value'=>$settings->__get('updates.links.color')
				)
	  );

		$this->addElement('hidden', 'blacklist', array(
				'trim' => true,
				'id' =>'blacklist',
			 	'order'=>5,
			)
	  );

    $this->addElement('hidden', 'remove', array(
				'trim' => true,
				'id' =>'remove',
			 	'order'=>6,
			)
	  );

		$this->addElement('Button', 'button', array(
          'label' => 'UPDATES_Send test email',
			 		'onclick'=>"Smoothbox.open(new Element('a', {'href':'admin/updates/layout/testemail'}));",
          'type' => 'button',
          'decorators' => array('ViewHelper'),
					'style'=>'background-color: #619dbe',
          'ignore' => true,)
    );

 	  $this->addElement('Button', 'submit', array(
          'label' => 'Save changes',
			 		'onclick'=>'$savechanges = true;',
          'type' => 'submit',
			 		'disabled' => true,
          'decorators' => array('ViewHelper'),
          'ignore' => true,)
    );

    $this->addElement('Cancel', 'cancel', array(
           'label' => 'cancel',
           'link' => true,
           'prependText' => ' or ',
           'href'=>'admin/updates/layout',
           'ignore' => true,
           'decorators' => array('ViewHelper'),)
    );

		$this->addDisplayGroup(array('button', 'submit', 'cancel',),'buttons');
  }
}