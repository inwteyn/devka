<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Settings.php 06.09.12 17:48 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Admin_Credits_Settings extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings');

    // Elements
    $this->addElement('Radio', 'credits_on_pages', array(
      'label' => 'OFFERS_Credits on Offers',
      'description' => 'OFFERS_Do you want to let page owners use a credits for offers?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}