<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_IndexController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
    if (Engine_Api::_()->touch()->siteMode() !== 'touch'){
      	$this->_redirect($this->view->url(array(), 'default', true), array('prependBase' => false));
    }
		$content = Engine_Content::getInstance();
		$table = Engine_Api::_()->getDbtable('pages', 'touch');
		$content->setStorage($table);

		if ( $this->_getParam('format') == 'json')
		{
			$this->view->html = $content->render('touch_index_index');
			$this->view->status = 1;

		} else if ( $this->_getParam('format') == 'html') {
			$this->view->html = $content->render('touch_index_index');
			$this->view->status = 1;

    } else {

			$this->_helper->content->setContent($content);

			// Render
    	$this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
		}
	}
	
  public function touchModeSwitchAction()
  {
		$mode = $this->_getParam('mode','standard');

		if ($mode === 'touch' || $mode === 'standard' || $mode === 'mobile'|| $mode === 'simulator')
		{
			$session = new Zend_Session_Namespace('standard-mobile-mode');
			$session->__set('mode', $mode);
      //print_die($session->__get('mode'));
		}

		$return_url = urldecode($this->_getParam('return_url'));
		$this->_redirect($return_url, array('prependBase'=>0));
  }
}