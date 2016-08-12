<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 28.02.12
 * Time: 10:41
 * To change this template use File | Settings | File Templates.
 */

class Pagealbum_Form_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Album')
      ->setDescription('Are you sure you want to delete this page album?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
    ;
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Album',
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