<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Compose.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_Messages_Compose extends Touch_Form_Standard
{
  public function init()
  {
		$view = $this->getView();

    $this->setTitle('Compose Message');
    $this->setAttrib('id', 'messages_compose');

    // init to
    $this->addElement('Cancel', 'to',array(
			'label' => '0 ' . $this->getView()->translate('contacts'),
			'prependText' => $this->getView()->translate('Send to') . ': ',
			'link' => true,
			'onclick' => 'selectedContacts()',
			'decorators' => array('ViewHelper'),
			'style' => 'font-weight: bold',
		));

/*    $this->addElement('Cancel', 'add',array(
			'label' => $this->getView()->translate('add'),
			'prependText' => ' - ',
			'link' => true,
			'onclick' => 'addContacts()',
			'decorators' => array('ViewHelper'),
		));*/


    /*$this->addDisplayGroup(array('to', 'add'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
		$button_group->addDecorator('DivDivDivWrapper');*/

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'required' => true,
      'allowEmpty' => false,
      'order' => 2,
      'validators' => array(
        'NotEmpty'
      ),
      'filters' => array(
        'HtmlEntities'
      ),
			'decorators' => array('ViewHelper'),
    ));

    // init title
    $this->addElement('Text', 'title', array(
      'order' => 3,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
			'class'=>'filter_default_value',
			'onBlur'=>'Touch.blur($(this), false, \'' . $view->translate('Subject') . '\')',
			'onFocus' => 'Touch.focus($(this))',
			'value'=>$view->translate('Subject'),
    ));
    $this->getElement('title')->removeDecorator('label');

    // init body
    $this->addElement('Textarea', 'body', array(
      'order' => 4,
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
			'class'=>'filter_default_value',
			'onBlur'=>'Touch.blur($(this), false, \'' . $view->translate('Message') . '\')',
			'onFocus' => 'Touch.focus($(this))',
			'value'=>$view->translate('Message'),
			'style'=>'height: 40px;'
    ));

		$this->getElement('body')->removeDecorator('label');
		
    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Send Message',
      'order' => 5,
      'type' => 'submit',
      'ignore' => true
    ));

		$this->getElement('submit')->removeDecorator('DivDivDivWrapper');
  }
}