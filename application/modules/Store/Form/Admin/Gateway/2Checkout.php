<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: 2Checkout.php 8221 2011-01-15 00:24:02Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Store_Form_Admin_Gateway_2Checkout extends Store_Form_Admin_Gateway_Abstract
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
          'module' => 'store',
          'controller' => 'ipn',
          'action' => '2Checkout'
        ), 'default', true),
      'https://www.2checkout.com/va/acct/detail_company_info',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'store',
          'controller' => 'transaction',
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
      'required' => 1
    ));

    $this->addElement('Text', 'password', array(
      'label' => 'API Password',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
      'required' => 1
    ));

    // Test Mode?
    $this->addElement('Checkbox', 'test_mode', array(
      'label' => 'Gateway Test Mode',
      'required' => 0,
      'value' => $this->_isTestMode
    ));
  }
}