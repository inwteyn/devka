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
    if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid() ) return;

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

    // Render
    $this->_helper->content
    //->setNoRender()
      ->setEnabled()
    ;
  }

  public function manageAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid() ) return;

    $params = $this->_request->getParams();

    $form = new Pagevideo_Form_Search();
    $form->removeElement('view');
    $form->populate($params);
    $this->view->form = $form;

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params['view'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();
    $params['ipp'] = $settings->getSetting('pagevideo.page', 10);


    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagevideo')->getVideoPaginator($params);

    $formValues = array();

    if( !empty($params['text']) ) {
      $formValues['text'] = $params['text'];
    }

    if( !empty($params['orderby']) ) {
      $formValues['orderby'] = $params['orderby'];
    }

    $this->view->formValues = $formValues;

    $this->_helper->content->setEnabled();
  }

  public function deleteAction()
  {
    $table = Engine_Api::_()->getDbTable('pagevideos', 'pagevideo');
    $video = Engine_Api::_()->getItem('pagevideo', $this->_request->getParam('pagevideo_id'));

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form  = new Pagevideo_Form_Delete();

    if( !$video )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
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

    try {
      Engine_Api::_()->getApi('core', 'pagevideo')->deleteVideo($video);
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'page_videos', true),
      'messages' => Array($this->view->message)
    ));
  }
}
