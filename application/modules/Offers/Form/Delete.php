<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Delete.php 2012-06-07 11:40 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_Form_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('OFFERS_offer_delete')
      ->setDescription('OFFERS_offer_desc_delete')
      ->setAttrib('class', 'global_form_popup');

    $this->addElement('Button', 'submit', array(
      'label' => 'OFFERS_offer_btn_delete',
      'type' => 'submit',
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
    $this->getDisplayGroup('buttons');
  }
}