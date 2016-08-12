<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 22.08.12
 * Time: 15:30
 * To change this template use File | Settings | File Templates.
 */
class Offers_Form_Contacts extends Engine_Form
{

  protected $page_id;

  public function init()
  {
    $this->setTitle('OFFERS_contacts_offer')
        ->setAttrib('id', 'offer_contacts_form');

    // Country
    $this->addElement('Text', 'country', array(
      'label' => 'OFFERS_country',
      'filters' => array(
        new Engine_Filter_Html(),
      )
    ));

    // State
    $this->addElement('Text', 'state', array(
      'label' => 'OFFERS_state',
      'filters' => array(
        new Engine_Filter_Html(),
      )
    ));

    // City
    $this->addElement('Text', 'city', array(
      'label' => 'OFFERS_city',
      'filters' => array(
        new Engine_Filter_Html(),
      )

    ));

    // Address
    $this->addElement('Text', 'address', array(
      'label' => 'OFFERS_address',
      'filters' => array(
        new Engine_Filter_Html(),
      )
    ));

    // Phone
    $this->addElement('Text', 'phone', array(
      'label' => 'OFFERS_phone',
      'filters' => array(
        new Engine_Filter_Html(),
      )
    ));

    // Website
    $this->addElement('Text', 'website', array(
      'label' => 'OFFERS_website',
      'filters' => array(
        new Engine_Filter_Html(),
      )
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}
