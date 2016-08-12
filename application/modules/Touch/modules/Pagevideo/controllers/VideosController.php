<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: VideosController.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
class Pagevideo_VideosController extends Core_Controller_Action_Standard
{
  public function browseAction()
  {
    // Get navigation
    $this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'touch')
      ->getNavigation('video_main', array(), 'video_main_browse');

    $this->view->form = $form = new Touch_Form_Search();

    if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid() ) return;

    $form->getElement('search')->setValue($this->_getParam('search'));
    // Get setting
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pagevideo.page', 10);

    //Paginator
    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagevideo')->getVideoPaginator($params);

    $formValues = array();

    if( !empty($params['text']) ) {
      $formValues['text'] = $params['text'];
    }

    if( !empty($params['orderby']) ) {
      $formValues['orderby'] = $params['orderby'];
    }

    if( !empty($params['view']) ) {
      $formValues['view'] = $params['view'];
    }

    if( !empty($params['category']) ) {
      $formValues['category'] = $params['category'];
    }

    $this->view->formValues = $formValues;

  }

  public function manageAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;
    // Get navigation
    $this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'touch')
      ->getNavigation('video_main', array(), 'video_main_browse');

    if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid() ) return;

    $params = $this->_request->getParams();
    $this->view->form = $form = new Touch_Form_Search();
    $form->getElement('search')->setValue($this->_getParam('search'));

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params['view'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();
    $params['ipp'] = $settings->getSetting('pagevideo.page', 10);


    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagevideo')->getVideoPaginator($params);
  }
}
