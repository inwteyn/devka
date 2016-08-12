<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: PayPal.php 21.09.12 14:29 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Admin_Gateway_PayPal extends Payment_Form_Admin_Gateway_Abstract
{
  protected $_isTestMode = false;

  public function __construct($options = array())
  {
    if( array_key_exists('isTestMode', $options) ){
      $this->_isTestMode = (bool) $options['isTestMode'];
    }

    return parent::__construct( $options );
  }

  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: PayPal');

    $description = $this->getTranslator()->translate('PAYMENT_FORM_ADMIN_GATEWAY_PAYPAL_DESCRIPTION');

    if( $this->_isTestMode ){
      $signature = 'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature';
      $ipn = 'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-ipn-notify';
    } else {
      $signature = 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature';
      $ipn = 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-ipn-notify';
    }

    $description = vsprintf($description, array(
      $signature,
      $ipn,
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'offers',
          'controller' => 'ipn',
          'action' => 'PayPal'
        ), 'default', true),
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);


    // Elements
    $this->addElement('Text', 'username', array(
      'label' => 'API Username',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'password', array(
      'label' => 'API Password',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'signature', array(
      'label' => 'API Signature',
      //'description' => 'You only need to fill in either Signature or ' .
      //    'Certificate, depending on what type of API account you create.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    /*
    $this->addElement('Textarea', 'certificate', array(
      'label' => 'API Certificate',
      'description' => 'You only need to fill in either Signature or ' .
          'Certificate, depending on what type of API account you create.',
    ));
     *
     */
  }
}