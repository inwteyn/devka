<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 20.05.11
 * Time: 18:03
 * To change this template use File | Settings | File Templates.
 */
 
class Touch_Form_ItemCreate extends Engine_Form
{
public function init()
  {
    $this
      ->setTitle('Create Menu Item')
      ->setAttrib('class', 'global_form_popup')
      ;

    $this->addElement('Text', 'label', array(
      'label' => 'Label',
      'required' => true,
      'allowEmpty' => false,
    ));


    $this->addElement('Radio', 'uri_type', array(
      'label' => 'Url type',
      'multiOptions' => array(
        '1' => 'Outer page',
        '0' => 'Touch page',
      ),
      'value' => '1',
    ));

    $this->addElement('Text', 'uri', array(
      'label' => 'URL',
      'required' => true,
      'allowEmpty' => false,
      'style' => 'width: 300px',
    ));

    $this->addElement('Text', 'icon', array(
      'label' => 'Icon',
      'description' => 'Note: Not all menus support icons.',
      'style' => 'width: 500px',
    ));

    $this->addElement('Checkbox', 'target', array(
      'label' => 'Open in a new window?',
      'checkedValue' => '_blank',
      'uncheckedValue' => '',
    ));

    $this->addElement('Checkbox', 'enabled', array(
      'label' => 'Enabled?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
      'value' => '1',
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Menu Item',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
