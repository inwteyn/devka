<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Contact.php 2011-07-13 16:01 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagecontact_Form_Contact extends Engine_Form
{
	 private $page_id;

  public function __construct($page_id)
  {
    $this->page_id = $page_id;

    parent::__construct();
  }

	public function init()
  {
  	parent::init();
  	
    $this
      ->setTitle('Send Message')
      ->setAttrib('id', 'page_edit_form_contact')
      ->setAttrib('class', 'global_form');

		$topicsTbl = Engine_Api::_()->getDbTable('topics', 'pagecontact');
		$topics = $topicsTbl->getTopics($this->page_id);

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		$options[0] = '';
		foreach($topics as $topic)
		{
			$options[$topic['topic_id']] = $topic['topic_name'];
		}

    $this->addElement('Select', 'topic', array(
      'label' => 'PAGECONTACT_Topic',
			'class' => 'topic_class',
      'multiOptions' => $options,
    ));

    $this->getElement('topic')->getDecorator('label')->setOption('class','element_label_class topic_label_class');

	  $this->addElement('Hidden', 'visitor', array(
				'value' => 0,
				'order' => 3,
		));

	  if ($viewer_id == 0)
	  {
		  $this->addElement('Text', 'sender_name', array(
				'label' => 'PAGECONTACT_Full name',
				'class' => 'subject_class',
				'allowEmpty' => false,
				'size' => 37,
				'validators' => array(
					array('NotEmpty', true),
					array('StringLength', false, array(1, 64)),
				),
				'filters' => array(
					'StripTags',
					new Engine_Filter_Censor(),
					new Engine_Filter_EnableLinks(),
				),
      ));

      $this->getElement('sender_name')->getDecorator('label')->setOption('class','element_label_class sender_name_label_class');

		  $this->addElement('Text', 'sender_email', array(
				'label' => 'PAGECONTACT_Email',
				'class' => 'subject_class',
				'allowEmpty' => false,
				'size' => 37,
				'validators' => array(
					array('NotEmpty', true),
					array('StringLength', false, array(1, 64)),
				),
				'filters' => array(
					'StripTags',
					new Engine_Filter_Censor(),
					new Engine_Filter_EnableLinks(),
				),
      ));

      $this->getElement('sender_email')->getDecorator('label')->setOption('class','element_label_class sender_email_label_class');

			$this->addElement('Hidden', 'visitor', array(
				'value' => 1,
				'order' => 3,
			));
	  }

    $this->addElement('Text', 'subject', array(
      'label' => 'PAGECONTACT_Subject',
			'class' => 'subject_class',
      'allowEmpty' => false,
			'size' => 37,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $this->getElement('subject')->getDecorator('label')->setOption('class','element_label_class subject_label_class');

    $this->addElement('Textarea', 'message', array(
      'label' => 'PAGECONTACT_Message',
      'maxlength' => '512',
			'class' => 'message_class',
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags' => 'a'))
      ),
    ));

    $this->getElement('message')->getDecorator('label')->setOption('class','element_label_class message_label_class');

    $this->addElement('Hidden', 'page_id', array(
			'value' => $this->page_id,
			'order' => 7,
		));


		$this->addElement('Button', 'send', array(
      'label' => 'Send',
			'type' => 'button',
			'class' => 'btn_send_class'
    ));

  }
}
