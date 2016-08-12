<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Login.php 24.09.13 14:13 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heloginpopup_Form_Login extends Engine_Form
{
  protected $_mode;

  public function init()
  {
    $tabindex = 10;

    $this->setAttrib('class', 'heloginpopup_login_form global_form')
      ->setTitle('Login to your account');

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'heloginpopup', 'controller' => 'index', 'action' => 'index'), 'default'));

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $api = Engine_Api::_()->heloginpopup();
    $view = Zend_Registry::get('Zend_View');

//    if( $api->hasSocialIntegration() ) {
//      $this->addElement('Dummy', 'connect_with', array(
//        'content' => '<div id="connect_with">' . $view->translate('Connect with:') . '</div>',
//
//        'decorators' => array(
//          'ViewHelper',
//        ),
//      ));
//    }

    // Init facebook login link
    if( 'none' != $settings->getSetting('core_facebook_enable', 'none')
      && $settings->core_facebook_secret ) {
      $this->addElement('Dummy', 'facebook', array(
        'content' => $api->facebookLoginButton(),

        'decorators' => array(
          'ViewHelper',
        ),
      ));
    }



      // Init twitter login link
    if( 'none' != $settings->getSetting('core_twitter_enable', 'none')
      && $settings->core_twitter_secret ) {
      $this->addElement('Dummy', 'twitter', array(
        'content' => $api->twitterLoginButton(),

        'decorators' => array(
          'ViewHelper',
        ),
      ));
    }

    // Init janrain login link
    if( 'none' != $settings->getSetting('core_janrain_enable', 'none') && $settings->core_janrain_key ) {
      $mode = $this->getMode();
      $this->addElement('Dummy', 'janrain', array(
        'content' => $api->janrainLoginButton($mode),

        'decorators' => array(
          'ViewHelper',
        ),
      ));
    }





//    if( $api->hasSocialIntegration() ) {
//      $this->addElement('Dummy', 'or_login', array(
//        'content' => '<div id="or_login">' . $view->translate('or, login using your email address:') . '</div>',
//
//        'decorators' => array(
//          'ViewHelper',
//        ),
//      ));
//    }

    $this->addElement('Text', 'email', array(
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
        'EmailAddress'
      ),
      'tabindex' => $tabindex++,
      'autofocus' => 'autofocus',
      'inputType' => 'email',
      'class' => 'text',

      'decorators' => array(
        'ViewHelper',
      ),
    ));

      $this->addElement('Dummy', 'email_label', array(
          'content' => '<label>' . $view->translate('Email') . '</label>',
          'decorators' => array(
              'ViewHelper',
          )
      ));

    $this->addElement('Password', 'password', array(
      'required' => true,
      'allowEmpty' => false,
      'tabindex' => $tabindex++,
      'filters' => array(
        'StringTrim',
      ),

      'decorators' => array(
        'ViewHelper',
      ),
    ));

      $this->addElement('Dummy', 'password_label', array(
          'content' => '<label>' . $view->translate('Password') . '</label>',
          'decorators' => array(
              'ViewHelper',
          )
      ));

    $this->addElement('Hidden', 'return_url', array(

    ));

    if( $settings->core_spam_login ) {
      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions(array(
        'tabindex' => $tabindex++
      )));
    }

    // Init remember me
    $this->addElement('Checkbox', 'loginremember', array(
      'label' => 'Remember Me',
      'tabindex' => $tabindex++,
    ));

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Sign In',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => $tabindex++,
      'class' => 'submit',

      'decorators' => array(
        'ViewHelper',
      ),
    ));

      $content = Zend_Registry::get('Zend_Translate')->_("<span class='forgot_pass'><a target='_top' href='%s'>Forgot Password?</a></span>");
      $content= sprintf($content, Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth', 'action' => 'forgot'), 'default', true));


      // Init forgot password link
      $this->addElement('Dummy', 'forgot', array(
          'content' => $content,

          'decorators' => array(
              'ViewHelper',
          ),
      ));

      $this->addDisplayGroup(array('email_label', 'email', 'password_label', 'password', 'submit', 'forgot', 'loginremember', 'captcha'), 'left');
      $inputs = $this->getDisplayGroup('left');

      $inputs->setDecorators(array(
          'FormElements',
          array('HtmlTag', array('tag' => 'div', 'class' => 'left'))
      ));

    $content = Zend_Registry::get('Zend_Translate')->_("<div id='dont_have_account'><a target='_top' href='%s'><button type='button'>Sign-up</button></a></div>");
    $content= sprintf($content, Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup', true));


    // Init signup link
    $this->addElement('Dummy', 'signup', array(
      'content' => $content,

      'decorators' => array(
        'ViewHelper',
      ),
    ));
      $this->addElement('Dummy', 'connect', array(
          'content' => '<div class="connect_with">' . $view->translate('Connect with') . '</div>',
          'decorators' => array(
              'ViewHelper',
          )
      ));

      $this->addDisplayGroup(array('connect', 'facebook', 'twitter', 'signup'), 'right');
      $social_group = $this->getDisplayGroup('right');

      $social_group->setDecorators(array(
          'FormElements',
          array('HtmlTag', array('tag' => 'div', 'class' => 'right'))
      ));
      $this->addElement(
          'hidden',
          'dummy',
          array(
              'required' => false,
              'decorators' => array(
                  array(
                      'HtmlTag', array(
                      'tag' => 'i',
                      'id' => 'loginpopup-close',
                      'class' => 'wpClose hei hei-times',
                      'onclick' => 'loginPopup.hidePopup()'
                  )
                  )
              )
          )
      );

      $this->addElement('hidden', 'popup_return_url', array(
      ));
  }

  public function getMode()
  {
    if( null === $this->_mode ) {
      $this->_mode = 'page';
    }
    return $this->_mode;
  }
}