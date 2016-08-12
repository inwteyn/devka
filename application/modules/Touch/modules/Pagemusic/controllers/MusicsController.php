<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
class Pagemusic_MusicsController extends Core_Controller_Action_Standard
{
  public function browseAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid() ) return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('music_main');
    // Get browse params
    $this->view->formFilter = $form = new Music_Form_Search();
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);
    $form->getElement('search')->setValue($this->_getParam('search'));

    // Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pagemusic.page', 10);

    //Get paginator
    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagemusic')->getMusicPaginator($params);

  }

  public function manageAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    $params = $this->_request->getParams();

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('music_main');
    // Get browse params
    $this->view->formFilter = $form = new Music_Form_Search();
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }

    $this->view->formValues = array_filter($values);
    $form->getElement('search')->setValue($this->_getParam('search'));

    // Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params['ipp'] = $settings->getSetting('pagemusic.page', 10);
    $params['show'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();

    //Get paginator
    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagemusic')->getMusicPaginator($params);
  }

  public function deleteAction()
  {
    $playlist_id = $this->_getParam('playlist_id');
    $playlist = Engine_Api::_()->getItem('playlist', $playlist_id);

    if( !$playlist )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Playlist doesn't exists or not authorized to delete");
      return;
    }

    // In smoothbox
    //$this->_helper->layout->setLayout('default-simple');

    $this->view->form  = new Pageblog_Form_Delete();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = Engine_Api::_()->getDbTable('playlists', 'pagemusic')->getAdapter();

    $db->beginTransaction();

    try {
      $playlist->delete();
      $db->commit();
    }
    catch(Exception $e){
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Playlist has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'page_musics', true),
      'messages' => Array($this->view->message)
    ));
  }
}
