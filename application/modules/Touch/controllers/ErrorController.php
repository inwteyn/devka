<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ErrorController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_ErrorController extends Core_Controller_Action_Standard
{
  public function errorAction()
  {
    $error = $this->_getParam('error_handler');
    $this->view->error_code = $error_code = Engine_Api::getErrorCode(true);

    // Handle missing pages
    switch( $error->type ) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        return $this->_forward('notfound');
        break;

      default:
        break;
    }
    // Log this message
    if( isset($error->exception) &&
        Zend_Registry::isRegistered('Zend_Log') &&
        ($log = Zend_Registry::get('Zend_Log')) instanceof Zend_Log ) {
      // Only log if in production or the exception is not an instance of Engine_Exception
      $e = $error->exception;
      if( 'production' === APPLICATION_ENV || !($e instanceof Engine_Exception) ) {
        $output = '';
        $output .= PHP_EOL . 'Error Code: ' . $error_code . PHP_EOL;
        $output .= $e->__toString();
        $log->log($output, Zend_Log::CRIT);
      }
    }
    
    $this->view->status = false;
    $this->view->errorName = get_class($error->exception);

    if( APPLICATION_ENV != 'production' ) {
      $this->view->error = $error->exception->__toString();
    } else {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('An error has occurred');
    }
  }

  public function notfoundAction()
  {
    Zend_Controller_Front::getInstance()->getResponse()->clearAllHeaders();
    if($this->_getParam('customization')){
      $table = Engine_Api::_()->getDbTable('modules', 'core');
      $this->view->customization = $this->_getParam('customization');
      $this->view->originalmodule = $table->fetchRow($table->select()->where('name = ?', $this->_getParam('original')));
      $this->view->custommodule = $table->fetchRow($table->select()->where('name = ?', $this->_getParam('custom')));
    }
    $this->view->status = false;
    $this->view->error = Zend_Registry::get('Zend_Translate')->_('!!!!!!!!!!!!!!The requested resource could not be found.');
  }

	public function notsupportAction(){
		$moduleName = $this->_getParam('module-name');
		$module = Engine_Api::_()->getDbtable('modules', 'core')->getModule($moduleName);
		$moduleTitle = (is_object($module) ? $module->title : $moduleName);
		$this->view->error = $error = Zend_Registry::get('Zend_Translate')->_("TOUCH_Touch plugin does not support %s plugin.", array($moduleTitle));
		$this->view->status = false;
		$this->view->moduleName = is_object($module)? $module->title: $moduleName;
	}

  public function requiresubjectAction()
  {
    return $this->_forward('notfound');
    Zend_Controller_Front::getInstance()->getResponse()->clearAllHeaders();
    $this->view->status = false;
    $this->view->error = Zend_Registry::get('Zend_Translate')->_('!!!!!!!!RA!!!!!!!!The requested resource could not be found.');
  }

  public function requireauthAction()
  {
    Zend_Controller_Front::getInstance()->getResponse()->clearAllHeaders();
    // 403 error -- authorization failed
    if( !$this->_helper->requireUser()->isValid() ) return;

    $this->view->status = false;
    $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are not authorized to access this resource.');
  }

  public function requireuserAction()
  {
    Zend_Controller_Front::getInstance()->getResponse()->clearAllHeaders();

    $this->view->status = false;
    $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are not authorized to access this resource.');

    // Show the login form for them :P
    $this->view->form = $form = new Touch_Form_Login();
    $form->addError('Please sign in to continue..');
    $form->return_url->setValue(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }

  public function requireadminAction()
  {
    Zend_Controller_Front::getInstance()->getResponse()->clearAllHeaders();
    // Should probably make this do something else later
    return $this->_forward('notfound');
  }
}