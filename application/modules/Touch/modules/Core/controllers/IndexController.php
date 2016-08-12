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

class Core_IndexController extends Touch_Controller_Action_Standard
{
  public function indexAction()
  {
    if( Engine_Api::_()->user()->getViewer()->getIdentity() )
    {
			if ( $this->_getParam('format') == 'json'){

				return $this->_helper->touchRedirector->gotoRoute(array('action' => 'home', 'format'=>'json'), 'user_general', true);

			} else if ( $this->_getParam('format') == 'html'){
				return $this->_helper->touchRedirector->gotoRoute(array('action' => 'home', 'format'=>'html'), 'user_general', true);

			} else {

				return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);

			}
    }
		

    // check public settings
    if( !Engine_Api::_()->getApi('settings', 'core')->core_general_portal &&
        !$this->_helper->requireUser()->isValid() ) {
      return;
    }

		$content = Engine_Content::getInstance();
		$table = Engine_Api::_()->getDbtable('pages', 'touch');
		$content->setStorage($table);

		// Render

		if ( $this->_getParam('format') == 'json')
		{

			$this->view->html = $content->render('core_index_index');
			$this->view->status = 1;

		} else {

			$this->_helper->content->setContent($content);

    	$this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
		}
  }
}