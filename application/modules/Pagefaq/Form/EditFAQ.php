<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: EditFAQ.php 2011-09-28 15:28 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagefaq_Form_EditFAQ extends Engine_Form
{
	public function init()
	{
		$this
			->setAttrib('id', 'page_edit_form_faq')
			->setAttrib('class', 'page_edit_form form-faq');

	  $subForm = new Zend_Form_SubForm(array(
				'name' => 'subForm',
				'order' => 1,
				'decorators' => array(
					'FormElements',
				)
		));

    Engine_Form::enableForm($subForm);

		$subForm->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'subForm_faq_id', 'class' => 'subForm_faq_class'));

		$subForm->addElement('TextArea', 'question_faq', array(
			'label' => 'PAGEFAQ_Question',
      'style' => 'resize: vertical;',
      'class' => 'question_textarea_class',
		));

		$subForm->addElement('TextArea', 'answer_faq', array(
			'label' => 'PAGEFAQ_Answer',
			'rows' => 5,
			'cols' => 10,
			'class' => 'answer_textarea_class',
		));

    $subForm->getElement('answer_faq')->getDecorator('label')->setOption('class','answer_label_class');

    $subForm->addElement('Button', 'save_faq', array(
      'label' => 'Save',
      'type'  => 'submit',
      'class' => 'save_fag_class',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $subForm->addElement('Cancel', 'cancel_faq', array(
      'label' => 'cancel',
      'link' => false,
      'prependText' => ' or ',
      'class' => 'cancel_faq_class',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $subForm->addElement('hidden', 'faq_id', array('class' => 'faq_id_class'));

    $subForm->addDisplayGroup(array('save_faq', 'cancel_faq'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));

    $this->addSubForm($subForm, $subForm->getName());
  }
}

