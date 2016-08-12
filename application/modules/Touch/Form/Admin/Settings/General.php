<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 16.05.12
 * Time: 18:36
 * To change this template use File | Settings | File Templates.
 */
class Touch_Form_Admin_Settings_General extends Engine_Form
{
  public function init(){
    $this->addElement('Checkbox', 'set_default', array(
      'label' => 'Yes',
      'description' => 'TOUCH_Set Touch mode as default'
    ));

    $this->addElement('Checkbox', 'integrations_only', array(
      'label' => 'Yes',
      'description' => 'TOUCH_Display only integrated Pages'
    ));

    $this->addElement('Checkbox', 'include_tablets', array(
      'label' => 'Yes',
      'description' => 'TOUCH_Touch mode as default for tablets'
    ));
    $this->addElement('Button', 'done', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

  }
}
