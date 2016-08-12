<?php
/**/
class Offers_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract {
  public function init(){
    parent::init();
    $this->setTitle('OFFERS_level')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');
    $this->addElement('Radio', 'accept', array(
      'label' => 'Accept Offers',
      'description' => 'Do you want to let members to accept offers?',
      'multiOptions' => array(
        1 => 'Yes, allow this member level to accept offers',
        0 => 'No, do not allow this member level to accept offers',
      ),
      'value' => 1
    ));
  }
}