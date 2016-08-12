<?php
/***/
class Highlights_Form_Admin_Settings extends Engine_Form {
  public function init()
  {
    parent::init();
    $this->setTitle('HIGHLIGHT_Configure Profile highlight settings')
      ->setDescription('HIGHLIGHT_Configure Profile highlight settings cost and number of days');
    $this->setAttrib('class', '');
    $this->addElement('Text', 'highlight_num_days', array(
      'label' => 'HIGHLIGHT_Number of days',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('highlight.num.days', 10),
      'validators' => array(array('Int', true),
        new Engine_Validate_AtLeast(0),)

    ));
    $this->addElement('Text', 'highlight_cost', array(
      'label' => 'HIGHLIGHT_Cost',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('highlight.cost', 10),
      'validators' => array(array('Int', true),
        new Engine_Validate_AtLeast(0),)
    ));
    $this->addElement('Button', 'submit', array(
      'label' => 'HIGHLIGHT_Save changes',
      'type' => 'submit'
    ));

  }
}