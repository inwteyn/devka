<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: 2Checkout.php 21.09.12 14:34 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Admin_Gateway_2Checkout extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: 2Checkout');

    $description = $this->getTranslator()->translate('PAYMENT_FORM_ADMIN_GATEWAY_2CHECKOUT_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://www.2checkout.com/va/acct/list_usernames',
      'https://www.2checkout.com/va/notifications/',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'offers',
          'controller' => 'ipn',
          'action' => '2Checkout'
        ), 'default', true),
      'https://www.2checkout.com/va/acct/detail_company_info',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'offers',
          'controller' => 'subscription',
          'action' => 'return'
        ), 'default', true) . '?state=return',
      'https://www.2checkout.com/2co/signup',
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
  }
}
