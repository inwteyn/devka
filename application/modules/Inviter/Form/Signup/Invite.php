<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Invite.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Inviter_Form_Signup_Invite extends Engine_Form
{
  protected $_errors;
  protected $_success;
  public $_skip_form;

  public $_facebookKey;
  public $_twitterKey;
  public $_linkedinKey;
  public $_gmailKey;

  public $_yahooKey;
  public $_hotmailKey;
  public $_lastfmKey;
  public $_foursquareKey;
  public $_mailruKey;
  public $_orkutKey;

  public $_fb_settings;
  public $_providers;
  public $_count;
  public $_sign_up;

  public function init()
  {
    $this->setTitle('INVITER_Invite Friends');
    $this->setDescription('INVITER_FORM_SIGNUP_INVITE_DESCRIPTION')
      ->setAttrib('id', 'invite_friends');

    $this->_fb_settings = Engine_Api::_()->inviter()->getFacebookSettings(false, false, true);
    $this->_providers = Engine_Api::_()->inviter()->getIntegratedProviders(true);
    $this->_count = count($this->_providers);
    $this->_sign_up = true;

    $this->addElement('text', 'empty', array(
      'style' => 'display: none',
    ));

    $path = Engine_Api::_()->getModuleBootstrap('inviter')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');

    $this->addElement('Hidden', 'inviterStep', array(
      'order' => 4,
      'value' => 'getContacts',
    ));

    $this->addElement('Hidden', 'nextStep', array(
      'order' => 5,
    ));

    $this->addElement('Hidden', 'skip', array(
      'order' => 6
    ));

    $this->addElement('Button', 'done', array(
      'label' => 'INVITER_Import Contacts',
      'type' => 'submit',
      'order' => 7,
      'onclick' => 'return provider.signup_submit_form(this);',
      'decorators' => array(
        'ViewHelper',
      )));

    $this->addElement('hidden', 'provider_box');

    $this->done->addDecorator('FormSignupSkipInviter');
    $this->addDefaultDecorators($this->done);

    $this->addDisplayGroupPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
    $this->addDisplayGroup(array_keys($this->getElements()), 'from_elements');

    $this->from_elements->addDecorator('DefaultProviders', array('default_providers' => 12));

  }

  /**
   * Validate the form
   *
   * @param  array $data
   * @return boolean
   */
  public function isValid($data)
  {
    if (!is_array($data)) {
      // require_once 'Zend/Form/Exception.php';
      throw new Zend_Form_Exception(__CLASS__ . '::' . __METHOD__ . ' expects an array');
    }

    $translator = $this->getTranslator();
    $valid = true;

    if ($this->isArray()) {
      $data = $this->_dissolveArrayValue($data, $this->getElementsBelongTo());
    }

    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
    $provider = (isset($data['provider_box']) && $data['provider_box']) ? $data['provider_box'] : null;

    foreach ($this->getElements() as $key => $element) {
      $element->setTranslator($translator);

      if ($providerApi->checkIntegratedProvider($provider) && in_array($key, array('email_box', 'password_box'))) {
        continue;
      }

      if (!isset($data[$key])) {
        $valid = $element->isValid(null, $data) && $valid;
      } else {
        $valid = $element->isValid($data[$key], $data) && $valid;
      }
    }

    $this->_errorsExist = !$valid;

    // If manually flagged as an error, return invalid status
    if ($this->_errorsForced) {
      return false;
    }

    return $valid;
  }
}