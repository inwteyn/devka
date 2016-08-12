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

class Updates_Form_Admin_Layout_Widget extends Engine_Form
{
	protected $_content;
	
	public function setParams($content)
	{
		$this->_content = $content;
	}
	
  public function init()
  {

		 $this->addElement('Text', 'title', array(
			 'label' => 'Title',
			 'required' => true,
			 'allowEmpty' => false,
			 'trim' => true,
			 'value' => $this->_content->params['title'],
			 'style' => 'margin-bottom: 10px',
		 )
 	  );

 	  if ($this->_content->name == 'htmlblock')
 	  {
      $this->addElement('TinyMce', 'html', array(
        'label'=>'HTML:',
        'required' => true,
        'allowEmpty' => false,
        'decorators'=>array('ViewHelper'),
        'filters' => array(new Engine_Filter_Censor(),),
      ));
 	  }
 	  elseif (isset($this->_content->params['count']))
 	  {
 	  	$this->addElement('Text', 'count', array(
				'label' => 'UPDATES_Per update item number',
				'required' => true,
				'allowEmpty' => false,
				'value'=>$this->_content->params['count'],
				'trim' => true,)
 	  	);
 	  }

		if (isset($this->_content->params['select']))
 	  {

			if($this->_content->params['select']){
				$table = Engine_Api::_()->getDbtable('listingtypes', 'sitereview');
				$types = $table->fetchAll($table->select());

				foreach ($types as $type)
				{
					$types_echo[$type->listingtype_id]= $type->title_singular;
				}

				$this->addElement('Select', 'select', array(
					'label' => 'Listing type',
					'multiOptions' => $types_echo,
					'ignore' => true
				));
			}

 	  }

		if (isset($this->_content->title)){
			$this->addElement('Hidden', 'name', array(
				'value'=>$this->_content->name
			));
		} else {
			$this->addElement('Hidden', 'content_id', array(
				'value'=>$this->_content->id
			));
		}
		
 	  $this->addElement('Button', 'submit', array(
				'label' => 'Save',
				'type' => 'submit',
				'decorators' => array('ViewHelper'),
				'ignore' => true,)
    );
       
    $this->addElement('Cancel', 'cancel', array(
			 'label' => 'cancel',
			 'link' => true,
			 'prependText' => ' or ',
			 'onclick' => 'parent.Smoothbox.close();',
			 'ignore' => true,
			 'decorators' => array('ViewHelper'),)
    );
       
		$this->addDisplayGroup(array('submit', 'cancel',),'buttons');
  }
}