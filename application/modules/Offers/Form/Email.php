<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Email.php 2012-09-28 14:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_Form_Email extends Engine_Form
{
  public function init()
  {
    $this->setTitle('OFFERS_EMAIL_TITLE')
      ->setDescription('OFFERS_EMAIL_DESCRIPTION')
      ->setAttrib('class', 'global_form_popup')
      ->setMethod('POST');

    $this->addElement('Text', 'email_address', array(
      'label' => 'OFFERS_Email Address',
      'style' => 'width: 234px',
      'required' => true,
      //'allowEmpty' => false,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'OFFERS_BTN_Send Offer',
      'type' => 'submit',
      'class' => '',
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