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

    // Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pagemusic.page', 10);

    //Get paginator
    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagemusic')->getMusicPaginator($params);

    $formValues = array();

    if( !empty($params['search']) && $params['search'] ) {
      $formValues['search'] = $params['search'];
    }


    if( !empty($params['show']) && $params['show'] ) {
      $formValues['show'] = $params['show'];
    }

    if( !empty($params['sort']) && $params['sort'] ) {
      $formValues['sort'] = $params['sort'];
    }

    $this->view->formValues = $formValues;

    $this->_helper->content->setEnabled();
  }

  public function manageAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    $params = $this->_request->getParams();

    $form = new Pagemusic_Form_Search();
    $form->removeElement('show');
    $form->populate($params);
    $this->view->form = $form;

    // Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params['ipp'] = $settings->getSetting('pagemusic.page', 10);
    $params['show'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();

    //Get paginator
    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagemusic')->getMusicPaginator($params);

    $formValues = array();

    if( $params['search'] ) {
      $formValues['search'] = $params['search'];
    }


    if( $params['show'] ) {
      $formValues['show'] = $params['show'];
    }

    if( $params['sort'] ) {
      $formValues['sort'] = $params['sort'];
    }

    $this->view->formValues = $formValues;

    $this->_helper->content->setEnabled();
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
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form  = new Pagemusic_Form_Delete();

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
