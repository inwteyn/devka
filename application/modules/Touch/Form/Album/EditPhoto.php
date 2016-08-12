<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: EditPhoto.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 
class Touch_Form_Album_EditPhoto extends Touch_Form_Standard
{
  protected $_isArray = true;

  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements');

    $this->addElement('Text', 'title', array(
      'value' => $this->getView()->translate('Title'),
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
      'decorators' => array('ViewHelper'),

			'class'=> 'filter_default_value',
			'onblur'=>"Touch.blur($(this), 'filter_default_value', '" . $this->getView()->translate('Title') . "')",
			'onfocus'=>"Touch.focus($(this), 'filter_default_value')"
    ));

    $this->addElement('Textarea', 'description', array(
			'value' => $this->getView()->translate('Caption'),
      'rows' => 2,
      'cols' => 120,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'style'=>'margin:5px 0px')),
			),
			'class'=> 'filter_default_value',
			'onblur'=>"Touch.blur($(this), 'filter_default_value', '" . $this->getView()->translate('Caption') . "')",
			'onfocus'=>"Touch.focus($(this), 'filter_default_value')"
    ));

    $this->addElement('Checkbox', 'delete', array(
      'label' => "Delete Photo",
      'decorators' => array(
        'ViewHelper',
        array('Label', array('placement' => 'APPEND')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'photo-delete-wrapper')),
      ),
    ));


    $this->addElement('Hidden', 'photo_id', array(
      'validators' => array(
        'Int',
      )
    ));
  }
}