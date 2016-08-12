<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Category.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Pagedocument_Form_Admin_Category extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('pagedocument_Form Categories Form Title')
      ->setDescription('pagedocument_Form Categories Form Description')
      ->setMethod('post')
      ->setAttrib('class', 'global_form_box');

    $this->addElement(new Zend_Form_Element_Hidden('id'));

    $this->addElement(new Zend_Form_Element_Text('name'));
    $this->name->setLabel('pagedocument_Form Categories name')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text');

    $this->addElement(new Zend_Dojo_Form_Element_NumberTextBox('order'));
    $this->order->setLabel('pagedocument_Form Categories order')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text');

    $this->addElement('Button', 'submit', array(
      'label' => 'pagedocument_Form Categories submit',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
  
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'pagedocument_Form Categories cancel',
      'link' => true,
      'prependText' => ' or ',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array('ViewHelper')
    ));
  }

  public function fillForm($category)
  {
    $this->id->setValue($category->category_id);
    $this->name->setValue($category->category_name);
    $this->order->setValue($category->order);
    $this->submit->setLabel('pagedocument_Form Categories edit');
  }
}
