<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AlbumsController.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
class Pagealbum_AlbumsController extends Core_Controller_Action_Standard
{
  public function browseAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid() ) return;
    $form = $this->view->form_filter = new Touch_Form_Search();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $form->getElement('search')->setValue($this->_getParam('search'));
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('album_main');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = $this->_request->getParams();

    $params['ipp'] = $settings->getSetting('pagealbum.page', 10);

    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagealbum')->getAlbumPaginator($params);
  }

  public function manageAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    $form = $this->view->form_filter = new Touch_Form_Search();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $form->getElement('search')->setValue($this->_getParam('search'));
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('album_main');

    // Get Settings
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params = $this->_request->getParams();

    $params['view'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();
    $params['ipp'] = $settings->getSetting('pagealbum.page', 10);

    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagealbum')->getAlbumPaginator($params);
  }
}
