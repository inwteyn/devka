<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Follow.php 2012-09-17 17:40 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_Form_Follow extends Engine_Form
{
  private $follow_status;

  public function __construct($follow_status)
  {
    $this->follow_status = $follow_status;
    parent::__construct();
  }
  public function init()
  {
    $description = 'OFFERS_FOLLOW_DESCRIPTION';
    $type_button = 'submit';
    $class = '';
    if ($this->follow_status == 'active') {
      $description = 'OFFERS_FOLLOW_DESCRIPTION_FOLLOWED';
      $type_button = 'button';
      $class = 'disabled_button';
    }

    $this->setTitle('OFFERS_FOLLOW_TITLE')
      ->setDescription($description)
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');

    $this->addElement('Button', 'submit', array(
      'label' => 'OFFERS_FOLLOW_BTN_Follow',
      'type' => $type_button,
      'class' => $class,
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'parentText' => 'or',
      'href' => '',
      'onClick' => 'parent.Smoothbox.close()',
      'decorators' => array('ViewHelper')
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}