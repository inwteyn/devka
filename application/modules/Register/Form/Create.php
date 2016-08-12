<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Register
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Create.php 04.12.12 15:46 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Register
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Register_Form_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Set count of users');

    $this->addElement('Text', 'count', array(
      'label' => 'set Count, default is 10, min is 1, max is 10'
    ));
    // Init submit
    $this->addElement('button', 'submit', array(
      'type' => 'submit',
      'label' => 'Create Random Users',
    ));
  }
}