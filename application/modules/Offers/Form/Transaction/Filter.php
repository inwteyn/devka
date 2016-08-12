<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Filter.php 22.09.12 12:57 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Transaction_Filter extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;

    $this
      ->setAttribs(array(
        'id' => 'search_form',
        'class' => 'offers_filter_form inner',
      ))
      ->setMethod('GET')
      ;

    // Element: query
    $this->addElement('Text', 'offer_title', array(
      'label' => 'Offer',
    ));

    // Element: query
    $this->addElement('Text', 'query', array(
      'label' => 'Member|TransactionID',
    ));

    // Element: gateway_id
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $multiOptions = array('' => '');
    foreach( $gatewaysTable->fetchAll() as $gateway ) {
      if ($gateway->title != 'Testing') {
        $multiOptions[$gateway->gateway_id] = $gateway->title;
      }
    }
    $multiOptions[999] = 'Credits';
    $this->addElement('Select', 'gateway_id', array(
      'label' => 'Gateway',
      'multiOptions' => $multiOptions,
    ));


    // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 10004,
    ));


    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 10005,
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Search',
      'type' => 'submit',
      'style' => 'padding: 2px'
    ));
  }
}