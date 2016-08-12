<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SearchController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Core_SearchController extends Touch_Controller_Action_Standard
{
  public function indexAction()
  {
    $searchApi = Engine_Api::_()->getApi('search', 'core');
    //$viewer = $this->_helper->api()->user()->getViewer();

    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if( !$require_check ) {
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    // Prepare form
    $this->view->form = $form = new Touch_Form_Search();

    // Check form validity?
    $values = array();
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    }
    
    $this->view->search = $search = (string) @$values['search'];
    $this->view->page = $page = (int) $this->_getParam('page');

    $this->view->paginator = Zend_Paginator::factory(array());

    if( $search )
    {
      $this->view->paginator = $paginator = $searchApi->getPaginator($search, null);
      $paginator->setCurrentPageNumber($page);
      $paginator->setItemCountPerPage(5);
    }

  }
}