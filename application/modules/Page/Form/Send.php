<?php
class Page_Form_Send extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $title = $this->getTranslator()->translate("PAGE_Send message for Teams");
    $this->setTitle($title)
      ->setDescription('')
      ->setAttrib('name', 'send');
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '100'))
      ),


       'autofocus' => 'autofocus',
    ));

    $this->addElement('textarea', 'text', array(
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '500'))
      ),
    ));

    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Send',
      'type' => 'submit',
    ));
  }


}