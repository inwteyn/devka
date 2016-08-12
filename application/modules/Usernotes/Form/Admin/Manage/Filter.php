<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Filter.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_Form_Admin_Manage_Filter extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clr'));

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ));

    $username = new Zend_Form_Element_Text('username');
    $username
      ->setLabel('Username')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $email = new Zend_Form_Element_Text('email');
    $email
      ->setLabel('Email')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $levelMultiOptions = array(0 => ' ');

    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    foreach( $levels as $row )
    {
      $levelMultiOptions[$row->level_id] = $row->getTitle();
    }

    $level_id = new Zend_Form_Element_Select('level_id');
    $level_id
      ->setLabel('Level')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions($levelMultiOptions);

    $enabled = new Zend_Form_Element_Select('with_usernote');
    $enabled
      ->setLabel('Usernote')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '-1' => '',
        '1' => 'With Usernote',
        '0' => 'Without Usernote',
      ))
      ->setValue('-1');

    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement('Hidden', 'order', array('order' => 10001,));

    $this->addElement('Hidden', 'order_direction', array('order' => 10002,));

    
    $this->addElements(array(
      $username,
      $email,
      $level_id,
      $enabled,
      $submit,
    ));
  }
}