<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: HelpController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Core_HelpController extends Touch_Controller_Action_Standard
{

  public function contactAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $this->view->form = $form = new Touch_Form_Core_Contact();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Success! Process
    // Mail gets logged into database, so perform try/catch in this Controller
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      // the contact form is emailed to the first SuperAdmin by default
      $users_table = Engine_Api::_()->getDbtable('users', 'user');
      $users_select = $users_table->select()
              ->where('level_id = ?', 1)
              ->where('enabled >= ?', 1);
      $super_admin = $users_table->fetchRow($users_select);
      $adminEmail = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact');
      if( !$adminEmail ) {
        $adminEmail = $super_admin->email;
      }

      $viewer = Engine_Api::_()->user()->getViewer();

      $values = $form->getValues();

      // Check for error report
      $error_report = '';
      $name = $this->_getParam('name');
      $loc = $this->_getParam('loc');
      $time = $this->_getParam('time');
      if( $name && $loc && $time ) {
        $error_report .= "\r\n";
        $error_report .= "\r\n";
        $error_report .= "-------------------------------------";
        $error_report .= "\r\n";
        $error_report .= $this->view->translate('The following information about an error was included with this message:');
        $error_report .= "\r\n";
        $error_report .= $this->view->translate('Exception: ') . base64_decode(urldecode($name));
        $error_report .= "\r\n";
        $error_report .= $this->view->translate('Location: ') . base64_decode(urldecode($loc));
        $error_report .= "\r\n";
        $error_report .= $this->view->translate('Time: ') . date('c', base64_decode(urldecode($time)));
        $error_report .= "\r\n";
      }

      // Make params
      $mail_settings = array(
        'host' => $_SERVER['HTTP_HOST'],
        'email' => $adminEmail,
        'date' => time(),
        'recipient_title' => $super_admin->getTitle(),
        'recipient_link' => $super_admin->getHref(),
        'recipient_photo' => $super_admin->getPhotoUrl('thumb.icon'),
        'sender_title' => $values['name'],
        'sender_email' => $values['email'],
        'message' => $values['body'],
        'error_report' => $error_report,
      );

      if( $viewer && $viewer->getIdentity() ) {
        $mail_settings['sender_title'] .= ' (' . $viewer->getTitle() . ')';
        $mail_settings['sender_email'] .= ' (' . $viewer->email . ')';
        $mail_settings['sender_link'] = $viewer->getHref();
      }

      // send email
      Engine_Api::_()->getApi('mail', 'core')->sendSystem(
        $adminEmail,
        'core_contact',
        $mail_settings
      );

      // if the above did not throw an exception, it succeeded
      $db->commit();
			$this->_forward('success', 'utility', 'touch', array(
				'messages' => array($translate->_('Thank you for contacting us!')),
				'parentRedirect' => $this->view->url(array('action' => 'home'), 'user_general'),
			));

    } catch( Zend_Mail_Transport_Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  public function termsAction()
  {
    // to change, edit language variable "_CORE_TERMS_OF_SERVICE"
  }

  public function privacyAction()
  {
    // to change, edit language variable "_CORE_PRIVACY_STATEMENT"
  }

}