<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminContact.php 2011-07-13 16:01 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Pagecontact_Form_AdminContact extends Engine_Form
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
			->setTitle('PAGECONTACT_Contact Us')
      ->setDescription('PAGECONTACT_Edit your Page contacts');

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

		$this->addElement('Hidden', 'edit', array('value' => 'contact', 'order' => 0));

		$descriptionTbl = Engine_Api::_()->getDbTable('descriptions', 'pagecontact');
		$description = $descriptionTbl->getDescription($this->page_id);

		$topicsTbl = Engine_Api::_()->getDbTable('topics', 'pagecontact');
		$topics = $topicsTbl->getTopics($this->page_id);

		if ($topics->count())	{
			$topics_data = $topics->toArray();
		} else {
			$topic_data[0]['topic_name'] = '';
			$topic_data[0]['emails'] = '';
			$topic_data[0]['topic_id'] = '';
		}

		$this->addElement('TinyMce', 'description', array(
			'label' => 'PAGECONTACT_Description',
			'value' => $description,
			'required' => true,
			'allowEmpty' => false,
			'order' => -1000001,
    ));

    $params = array(
      'mode' => 'exact',
      'elements' => 'description',
      'width' => '500px',
      'height' => '225px'
    );

    $this->getView()->getHelper('TinyMce')->setOptions($params);

		$i = 0;
		do {
			$subformAdmin = new Zend_Form_SubForm(array(
				'name' => 'extra_' . $i,
				'order' => -1000000 + $i,
				'decorators' => array(
					'FormElements',
				)
			));

			Engine_Form::enableForm($subformAdmin);

			$subformAdmin->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'subForm_'.$i, 'class' => 'subForm_class'));

			$subformAdmin->addElement('Text', 'topic_name', array(
				'label' => 'PAGECONTACT_Topic',
				'value' => isset($topics_data[$i]['topic_name']) ? $topics_data[$i]['topic_name'] : '',
				'allowEmpty' => false,
				'required' => true,
        'class' => 'topic_name_class',
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

			$subformAdmin->getElement('topic_name')->getDecorator('label')->setOption('class','label_topic_name');

			if ($i == 0)
			{
				$subformAdmin->addElement('Button', 'add_more', array(
					'label' => 'PAGECONTACT_Add more',
					'type' => 'button',
					'id' => 'button_addmore_id',
          'class' => 'button_class',
				));
			} else {
				$subformAdmin->addElement('Button', 'delete', array(
					'label' => 'Delete',
					'type' => 'button',
					'id' => 'button_delete_id_' . $i,
          'class' => 'button_class',
					'onClick' => 'deleteTopic('. $i . ')',
				));
			} 

			$subformAdmin->addElement('Textarea', 'emails', array(
				'label' => 'PAGECONTACT_Emails',
				'value' => isset($topics_data[$i]['emails']) ? $topics_data[$i]['emails'] : '',
				'rows' => '1',
				'class' => 'textarea_class',
				'description' => 'Separate emails with commas.',
				'allowEmpty' => false,
				'required' => true,
				'validators' => array(
					array('NotEmpty', true),
					array('StringLength', false, array(1, 200)),
				),
				'filters' => array(
					new Engine_Filter_Censor(),
					new Engine_Filter_Html(array('AllowedTags' => 'a'))
				),
			));

			$subformAdmin->emails->getDecorator("Description")->setOption("placement", "append");

			if (!isset($topics_data[$i]['topic_id'])) {
        $topics_data[$i]['topic_id'] = 0;
      }
			$subformAdmin->addElement('Hidden', 'topic_id', array('value' => $topics_data[$i]['topic_id']));

			$this->addSubForm($subformAdmin, $subformAdmin->getName());

			$i++;
		} while ($topics->count() > $i);

		$this->addElement('Button', 'submit', array(
			'label' => 'Save',
			'type' => 'submit',
		));
	}
}
