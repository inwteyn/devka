<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SettingsController.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_SettingsController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$this->_helper->requireUser();
	}
	
  public function indexAction()
  {
  	if(
  			!$this->_helper->api()->user()->getViewer()->getIdentity()
  			||
  		 	!$this->_helper->requireAuth()->setAuthParams('updates', null, 'use')->isValid()
  		)
  		{
  			return;
  		}

    $user = Engine_Api::_()->user()->getViewer();
    Engine_Api::_()->core()->setSubject($user);

    $this->view->navigation = $navigation = $this->_helper->api()
      ->getApi('menus', 'core')
      ->getNavigation('user_settings', array());

  	$this->view->form = $form = new Updates_Form_Subscribe();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
 			$user->updates_subscribed = $values['subscribe'];
 			
      if ($user->save())
      {
      	$form->addNotice('UPDATES_Changes have been successfully saved.');
      }
      else
      {
      	$form->addError('UPDATES_An error has been occurred while subscribing!!!');
      }

    }
    
    $form->populate(array('subscribe'=>$user->updates_subscribed));
  }
}