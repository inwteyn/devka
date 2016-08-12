<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Contact.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_Core_Contact extends Touch_Form_Standard
{
  public function init()
  {
    $this->setTitle('Contact Us')
      ->setDescription('_CORE_CONTACT_DESCRIPTION')
      #->setDescription('Thank you for trying out the SE4 Preview! We are using this live demonstration to find and resolve bugs more quickly as we continue to develop SE4. Please note that much of the functionality and front-end layout is still rough. The basic skin you see here will be one of several default skin choices available with SE4 when the final version is released. Our team sincerely appreciates your participation and feedback. Your message will be entered into our internal bug tracking system, so please do not expect a direct reply - although we may contact you with further questions if necessary. Once again, thanks!')
      ->setAction($_SERVER['REQUEST_URI'])
      ;
    
    $this->addElement('Text', 'name', array(
      'label' => 'Name',
      'required' => true,
      'notEmpty' => true,
    ));
    
    $this->addElement('Text', 'email', array(
      'label' => 'Email Address',
      'required' => true,
      'notEmpty' => true,
      'validators' => array(
        'EmailAddress'
      )
    ));

    $this->addElement('Textarea', 'body', array(
      'label' => 'Message',
      'required' => true,
      'notEmpty' => true,
    ));

    $show_captcha = Engine_Api::_()->getApi('settings', 'core')->core_spam_contact;
    if ($show_captcha && ($show_captcha > 1 || !Engine_Api::_()->user()->getViewer()->getIdentity() ) ) {
      $this->addElement('captcha', 'captcha', array(
        'label' => 'Human Verification',
        'description' => 'Please type the characters you see in the image.',
        'captcha' => 'image',
        'required' => true,
        'captchaOptions' => array(
          'wordLen' => 6,
          'fontSize' => '30',
          'timeout' => 300,
          'imgDir' => APPLICATION_PATH . '/public/temporary/',
          'imgUrl' => $this->getView()->baseUrl().'/public/temporary',
          'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
        )));
    }

    $this->addElement('Button', 'submit', array(
      'label' => 'Send Message',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}