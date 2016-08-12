<?php
/***/
class Highlights_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract {
  public function init()
  {
    parent::init();
    $this->setTitle('HIGHLIGHT_Member Level Settings')
      ->setDescription('HIGHLIGHT_Configure member level settings who can buy highlight profile');
    $this->setAttrib('class', '');
    $this->addElement('Radio', 'buy_highlight', array(
      'label' => 'HIGHLIGHT_Can buy highlight profile',
      'multiOptions' => array(
        '1' => 'HIGHLIGHT_Yes',
        '0' => 'HIGHLIGHT_No'
      )
    ));
  }
}