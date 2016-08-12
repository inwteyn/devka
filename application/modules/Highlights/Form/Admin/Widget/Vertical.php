<?php
/***/
class Highlights_Form_Admin_Widget_Vertical extends Engine_Form {
  public function init(){
    parent::init();
    $this->setTitle('HIGHLIGHT_Number of users')
    ->setDescription('HIGHLIGHT_Number of users in vertical widget');
    $this->addElement('Text', 'num_users', array(
      'Label' => 'HIGHLIGHT_Number of users',
    ));
  }
}