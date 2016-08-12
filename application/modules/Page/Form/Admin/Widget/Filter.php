<?php
/***/
class Page_Form_Admin_Widget_Filter extends Engine_Form {
  public function init()
  {
    parent::init();
    $this->setTitle('Filter')
      ->setDescription('Filter pages by categories');
    $this->addElement('select', 'filter', array(
      'label' => 'Category',
      'multiOptions' => $this->getPageTypes()
    ));
  }

  public function getPageTypes()
  {
    $multiOptions = array('' => Zend_Registry::get('Zend_Translate')->_("All"));
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias('page', 'profile_type');
    if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) ) return;

    $options = $profileTypeFields['profile_type']->getOptions();
    if( count($options) <= 1 ) {
      return;
    }

    foreach( $options as $option ) {
      $multiOptions[$option->option_id] = $option->label;
    }
    return $multiOptions;
  }
}