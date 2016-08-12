<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2012-07-03 11:40 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Admin_Manage_PhotoEdit extends Engine_Form
{
  protected $_isArray = true;

  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements');

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class'=>'offers_editphotos_title_input')),
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'offers_editphotos_title')),
      ),
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Caption',
      'rows' => 2,
      'cols' => 120,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class'=>'offers_editphotos_caption_input')),
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class'=>'offers_editphotos_caption_label')),
      ),
    ));

    $this->addElement('Checkbox', 'delete', array(
      'label' => "Delete Photo",
      'decorators' => array(
        'ViewHelper',
        array('Label', array('placement' => 'APPEND')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'photo-delete-wrapper')),
      ),
    ));
  }
}