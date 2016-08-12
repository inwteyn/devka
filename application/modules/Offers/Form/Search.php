<?php

class Offers_Form_Search extends Engine_Form
{
  private $params = array();

  public function __construct($params)
  {
    $this->params = $params;
    parent::__construct();
  }

  public function init()
  {
    $this->addAttribs(array('id' => 'offers_filter_form', 'class' => 'global_form_box'));
    $this->loadDefaultDecorators();

    $this->addElement('text', 'search_title_offer', array(
      'label' => 'Search',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Select', 'category_id', array(
      'label' => 'Category:',
      'multiOptions' => array(
        '' => 'All Categories'
      ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'dd')),
        array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
      ),
    ));

    $my_offers_filter = isset($params['my_offers_filter']) ? $params['my_offers_filter'] : false;

    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
      'style' => 'margin: 10px auto;',
      'onClick' => "offers_manager.formSearch('{$this->params['filter']}', '{$my_offers_filter}')",
      'decorators' => array('ViewHelper')
    ));
  }
}