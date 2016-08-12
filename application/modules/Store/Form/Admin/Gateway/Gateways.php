<?php
/**
 * SocialEngine
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PayPal.php 8221 2011-01-15 00:24:02Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Store_Form_Admin_Gateway_Gateways extends Engine_Form
{
  public $_options = array();

  public function __construct($options = array())
  {
    $this->_options = $options;

    /*if ($mode == 'client_store' && $gateway && $gateway->getTitle() == '2Checkout') {
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }*/

    parent::__construct($options);
  }

  public function init()
  {
    parent::init();

    $module_path = Engine_Api::_()->getModuleBootstrap('store')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $translate = $this->getTranslator();

    $description = $this->getTranslator()->translate('PAYMENT_FORM_ADMIN_GATEWAY_PAYPAL_DESCRIPTION');
    if ($this->_isTestMode) {
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
        'module' => 'store',
        'controller' => 'ipn',
        'action' => 'PayPal'
      ), 'default', true)
    ));
    $this->setAttrib('class', '');

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    // PayPal Email
    $mode = Engine_Api::_()->store()->getPaymentMode();
    $required = ($mode == 'client_store') ? true : false;

    $this->addElement('Text', 'pp_email', array(
      'placeholder' => 'PayPal Email Address',
      'description' => 'Please fill this PayPal Email field, it is required for Client-Store mode.',
      'required' => $required,
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
      'value' => $this->_options['pp']['email']
    ));
    $this->pp_email->getDecorator("Description")->setOption("placement", "append");

    // PayPal Api username
    $this->addElement('Text', 'pp_username', array(
      'placeholder' => 'API Username',
      'required' => $required,
      'filters' => array(
        new Zend_Filter_StringTrim()
      ),
      'value' => $this->_options['pp']['config']['username']
    ));

    // PayPal Api password
    $this->addElement('Text', 'pp_password', array(
      'placeholder' => 'API Password',
      'required' => $required,
      'filters' => array(
        new Zend_Filter_StringTrim()
      ),
      'value' => $this->_options['pp']['config']['password']
    ));

    // PayPal Api signature
    $this->addElement('Text', 'pp_signature', array(
      'placeholder' => 'API Signature',
      'required' => $required,
      'filters' => array(
        new Zend_Filter_StringTrim()
      ),
      'value' => $this->_options['pp']['config']['signature']
    ));
    // Enabled?
    $e = new Engine_Form_Element_Checkbox('pp_enabled');
    $e->setLabel('STORE_Gateway Enabled')
      ->setAttrib('onclick', 'editGateway(2, "enabled", this);')
      ->setCheckedValue($this->_options['pp']['enabled'])
      ->checked = $this->_options['pp']['enabled'];

    $this->addElement($e);

    // Test Mode?
    $t = new Engine_Form_Element_Checkbox('pp_test_mode');
    $t->setLabel('STORE_Gateway Test Mode')
      ->setAttrib('onclick', 'editGateway(2, "test_mode", this);')
      ->setCheckedValue($this->_options['pp']['test_mode'])
      ->checked = $this->_options['pp']['test_mode'];
    $this->addElement($t);

    $this->addElement('Dummy', 'paypal', array('content' => '<div class="admin-loader admin-loader-animation store-gateway-loader"></div>'));
    $this->addElement('Button', 'save_pp',
      array(
        'label' => $translate->_('STORE_Save Gateway'),
        'class' => 'gateway-action-button',
        'onclick' => "save_data(2, new Array('pp_email', 'pp_username', 'pp_password', 'pp_signature'), this)"
      ));
    $this->addElement('Button', 'clear_pp',
      array(
        'label' => $translate->_('STORE_Clear Gateway'),
        'class' => 'gateway-action-button gateway-action-button-right',
        'onclick' => "save_data(2, new Array('pp_email', 'pp_username', 'pp_password', 'pp_signature'), this, 1)"
      )
    );
    $this->getElement('save_pp')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_pp')->setDecorators(array('ViewHelper'));

    $this->addDisplayGroup(
      array('pp_email', 'pp_username', 'pp_password', 'pp_signature', 'pp_enabled', 'pp_test_mode', 'save_pp', 'clear_pp', 'paypal'),
      'paypal_form',
      array('class' => 'gateway-fieldset')
    );

    $this->getDisplayGroup('paypal_form')->addDecorator(
      'GatewayDescription',
      array('label' => $translate->_('STORE_Paypal Settings'), 'description' => $description, 'id' => 2)
    );


    ///////////////////////////////////////////////////////// 2Checkout

    $this->addElement('Text', '2c_username', array(
      'placeholder' => 'API Username',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
      'required' => 1
    ));

    $this->addElement('Text', '2c_password', array(
      'placeholder' => 'API Password',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
      'required' => 1
    ));

    // Enabled?
    $e = new Engine_Form_Element_Checkbox('2c_enabled');
    $e->setLabel('STORE_Gateway Enabled')
      ->setAttrib('onclick', 'editGateway(1, "enabled", this);')
      ->setCheckedValue($this->_options['2c']['enabled'])
      ->checked = $this->_options['2c']['enabled'];

    $this->addElement($e);

    // Test Mode?
    $t = new Engine_Form_Element_Checkbox('2c_test_mode');
    $t->setLabel('STORE_Gateway Test Mode')
      ->setAttrib('onclick', 'editGateway(1, "test_mode", this);')
      ->setCheckedValue($this->_options['2c']['test_mode'])
      ->checked = $this->_options['2c']['test_mode'];
    $this->addElement($t);

    $this->addElement('Dummy', '2checkout', array('content' => '<div class="admin-loader admin-loader-animation store-gateway-loader"></div>'));
    $this->addElement('Button', 'save_2c',
      array(
        'label' => $translate->_('STORE_Save Gateway'),
        'class' => 'gateway-action-button',
        'onclick' => "save_data(1, new Array('2c_username', '2c_password'), this)"
      ));
    $this->addElement('Button', 'clear_2c',
      array(
        'label' => $translate->_('STORE_Clear Gateway'),
        'class' => 'gateway-action-button gateway-action-button-right',
        'onclick' => "save_data(1, new Array('2c_username', '2c_password'), this, 1)"
      )
    );
    $this->getElement('save_2c')->setDecorators(array('ViewHelper'));
    $this->getElement('clear_2c')->setDecorators(array('ViewHelper'));

    $this->addDisplayGroup(
      array('2c_username', '2c_password', '2c_enabled', '2c_test_mode', 'save_2c', 'clear_2c', '2checkout'),
      '2c_form',
      array('class' => 'gateway-fieldset')
    );


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
    $this->getDisplayGroup('2c_form')->addDecorator(
      'GatewayDescription',
      array('label' => $translate->_('STORE_2Checkout Settings'), 'description' => $description, 'id' => 1)
    );
  }
}
