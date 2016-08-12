<?php

class Offers_Form_Popular extends Engine_Form
{
  private $params = array();

  public function __construct()
  {
    parent::__construct();
  }

  public function init()
  {
    $this->addElement('text', 'popular_count', array(
      'label' => 'Specify Number of purchases:',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));


     $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'style' => 'margin: 10px auto;',
    ));
  }
}