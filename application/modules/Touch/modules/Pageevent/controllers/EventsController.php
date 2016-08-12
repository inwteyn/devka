<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: EventsController.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pageevent_EventsController extends Core_Controller_Action_Standard
{
  public function browseAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid() ) return;

    $filter = $this->_getParam('filter', 'future');
    if( $filter != 'past' && $filter != 'future' ) $filter = 'future';
    $this->view->filter = $filter;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('event_main');

		foreach ($navigation->getPages() as $page)
    {
      if( ($page->label == "Upcoming Events" && $filter == "future") || ($page->route == "event_past" && $filter == "past")) {
			$page->active = true;
      }
    }

    // Create form
    $this->view->formFilter = $formFilter = new Touch_Form_Search();
    $defaultValues = $formFilter->getValues();

    // Populate form data
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pageevent.page', 10);
    $params['filter'] = $filter;
    $this->view->paginator = Engine_Api::_()->getApi('core','pageevent')->getEventsPaginator($params);

    $this->view->filter = $filter;
  }

  public function manageAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('event_main');

    $params = $this->_request->getParams();

    // Create form
    $this->view->formFilter = $formFilter = new Touch_Form_Search();
    $defaultValues = $formFilter->getValues();

    // Populate form data
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    if( empty($params['view']) ) {
      $params['view'] = 2;
    }

    $params['owner'] = Engine_Api::_()->user()->getViewer();

    $settings = Engine_Api::_()->getApi('settings', 'core');


    $params['ipp'] = $settings->getSetting('pageevent.page', 10);

    $this->view->paginator = Engine_Api::_()->getApi('core','pageevent')->getEventsPaginator($params);

  }

  public function deleteAction()
  {
    $table = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
    $event = Engine_Api::_()->getItem('pageevent', $this->_request->getParam('pageevent_id'));

    // In smoothbox
    //$this->_helper->layout->setLayout('default-simple');

    $this->view->form  = new Pageevent_Form_Delete();

    if( !$event )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Event doesn't exists or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try{
      $event->delete();

      $db->commit();
    }catch(Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Event has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'messages' => Array($this->view->message),
      //'layout' => 'default-simple',
      'parentRefresh' => true,
    ));
  }

  public function leaveAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    // In smoothbox
   /// $this->_helper->layout->setLayout('default-simple');

    // Form
    $this->view->form = $form = new Pageevent_Form_Leave();

    $pageevent = Engine_Api::_()->getItem('pageevent', $this->_request->getParam('pageevent_id'));
    $viewer = Engine_Api::_()->user()->getViewer();

    if( $pageevent->isOwner($viewer) ) return;


    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $db = Engine_Api::_()->getItemTable('pageevent')->getAdapter();

      $db->beginTransaction();
      try {
        $pageevent->membership()->removeMember($viewer);
        $db->commit();
      }catch(Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Event left')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }
}
