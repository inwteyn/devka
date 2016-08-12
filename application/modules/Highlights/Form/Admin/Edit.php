<?php
/***/
class Highlights_Form_Admin_Edit extends Engine_Form {
  public function init()
  {
    parent::init();
    $this->setTitle('HIGHLIGHT_Edit date finish')
      ->setDescription('HIGHLIGHT_Edit date finish of highlight');
    $end = new Engine_Form_Element_CalendarDateTime('date_finish');
    $end->setLabel("Date Finish");
//    $end->setAttrib('background-repeat', 'no-repeat');
    $end->setOptions(array('background-repeat' => 'no-repeat'));
    $this->addElement($end);

    $this->addElement('Button', 'submit', array(
      'label' => 'HIGHLIGHT_Save',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper'
      )
    ));
  }
}