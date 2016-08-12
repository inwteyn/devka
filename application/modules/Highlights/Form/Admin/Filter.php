<?php
/***/
class Highlights_Form_Admin_Filter extends Engine_Form {
  public function init() {
    parent::init();
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
    ;
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET');
    $displayname = new Zend_Form_Element_Text('displayname');
    $displayname
      ->setLabel('HIGHLIGHT_Display Name')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));


    $email = new Zend_Form_Element_Text('email');
    $email
      ->setLabel('HIGHLIGHT_Email')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
    $levelMultiOptions = array(0 => ' ');
    foreach ($levels as $key => $value) {
      $levelMultiOptions[$key] = $value;
    }

    $level_id = new Zend_Form_Element_Select('level_id');
    $level_id
      ->setLabel('HIGHLIGHT_Level')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions($levelMultiOptions);

    $highlight = new Zend_Form_Element_Checkbox('highlight');
    $highlight
      ->setLabel('HIGHLIGHT_Highlighted')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
      ->setLabel('HIGHLIGHT_Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement('Hidden', 'order', array(
      'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 10002,
    ));

    $this->addElement('Hidden', 'user_id', array(
      'order' => 10003,
    ));


    $this->addElements(array(
      $highlight,
      $displayname,
      $email,
      $level_id,
      $submit,
    ));

    // Set default action without URL-specified params
    $params = array();
    foreach (array_keys($this->getValues()) as $key) {
      $params[$key] = null;
    }
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($params));
  }
}