<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: BlogsController.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
class Pageblog_BlogsController extends Core_Controller_Action_Standard
{
  public function browseAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('blog_main');
    $this->view->form = $form = new Touch_Form_Search();

    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->view->canCreate = $this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->checkRequire();

    //Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //Get Params
    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pageblog.page', 10);
    if(!isset($params['page'])){
      $params['page'] = $this->_getParam('page', null);
    }

    $this->view->paginator = Engine_Api::_()->getApi('core', 'pageblog')->getBlogsPaginator($params);

    $formValues = array();

    if( !empty($params['search']) ) $formValues['search'] = $params['search'];
    if( !empty($params['orderby']) ) $formValues['orderby'] = $params['orderby'];
    if( !empty($params['show']) ) $formValues['show'] = $params['show'];
    if( !empty($params['category']) ) $formValues['category'] = $params['category'];

    $this->view->formValues = $formValues;
  }

  public function manageAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    $params = $this->_request->getParams();

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('blog_main');
    $this->view->form = $form = new Touch_Form_Search();

    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->view->canCreate = $this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->checkRequire();

    //Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //Get Params
    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pageblog.page', 10);
    $params['show'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();
    if(!isset($params['page'])){
      $params['page'] = $this->_getParam('page', null);
    }

    $this->view->paginator = Engine_Api::_()->getApi('core', 'pageblog')->getBlogsPaginator($params);

//    $formValues = array();
//
//    if( !empty($params['search']) ) $formValues['search'] = $params['search'];
//    if( !empty($params['orderby']) ) $formValues['orderby'] = $params['orderby'];
//    if( !empty($params['category']) ) $formValues['category'] = $params['category'];

    //    $this->view->formValues = $formValues;
    //
    //    $this->_helper->content->setEnabled();
  }
}
