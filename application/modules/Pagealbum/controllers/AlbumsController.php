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

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = $this->_request->getParams();

    $params['ipp'] = $settings->getSetting('pagealbum.page', 10);

    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagealbum')->getAlbumPaginator($params);

    $searchParams = array();

    if( !empty($params['search']) )
      $searchParams['search'] = $params['search'];

    if( !empty($params['sort']) )
      $searchParams['sort'] = $params['sort'];

    if( !empty($params['view']) )
      $searchParams['view'] = $params['view'];

    if( !empty($params['category_id']) )
      $searchParams['category_id'] = $params['category_id'];

    $this->view->searchParams = $searchParams;
    // Render
    $this->_helper->content
    //->setNoRender()
      ->setEnabled()
    ;
  }

  public function manageAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    $form = new Pagealbum_Form_Search();
    $form->removeElement('view');
    $form->setMethod('get');
    $params = $this->_request->getParams();
    $form->populate($params);
    $this->view->form = $form;

    // Get Settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params['view'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();
    $params['ipp'] = $settings->getSetting('pagealbum.page', 10);

    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagealbum')->getAlbumPaginator($params);

    $searchParams = array();

    if( !empty($params['search']) )
      $searchParams['search'] = $params['search'];

    if( !empty($params['sort']) )
      $searchParams['sort'] = $params['sort'];

    if( !empty($params['category_id']) )
      $searchParams['category_id'] = $params['category_id'];

    $this->view->searchParams = $searchParams;

    $this->_helper->content
      ->setEnabled();

  }

  public function deleteAction()
  {
    $table = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
    $album = Engine_Api::_()->getItem('pagealbum', $this->_request->getParam('pagealbum_id'));

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form  = new Pagealbum_Form_Delete();

    if( !$album )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Album doesn't exists or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $select = $album->getCollectiblesSelect();

    $photo_id = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum')->getAdapter()->fetchOne($select);

    $db = $table->getAdapter();
    $db->beginTransaction();

    try{
      if (!empty($photo_id)){
        $attachmentTable = Engine_Api::_()->getDbtable('attachments', 'activity');
        $name = $attachmentTable->info('name');
        $select = $attachmentTable->select()
          ->setIntegrityCheck(false)
          ->from($name, array('action_id'))
          ->where('type = ?', "pagealbumphoto")
          ->where('id = ?', $photo_id);

        $action_id = (int)$attachmentTable->getAdapter()->fetchOne($select);
        $where = array('action_id = ?' => $action_id);

        $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $actionsTable->delete($where);

        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $streamTable->delete($where);

        $attachmentTable->delete($where);

        $where = array('resource_id = ?' => $action_id);

        $commentTable = Engine_Api::_()->getDbtable('comments', 'activity');
        $commentTable->delete($where);

        $likeTable = Engine_Api::_()->getDbtable('likes', 'activity');
        $likeTable->delete($where);
      }

      $album->delete();

      $db->commit();
    }
    catch (Exception $e){
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Album has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'page_albums', true),
      'messages' => Array($this->view->message)
    ));

  }
}
