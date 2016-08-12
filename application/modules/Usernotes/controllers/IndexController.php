<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->view->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('save', 'json')->initContext('json');
    $ajaxContext->addActionContext('delete', 'json')->initContext('json');
  }

  public function indexAction()
  {
    $this->view->enabled = $this->_helper->requireAuth()->setAuthParams('usernotes', null, 'enabled')->checkRequire();

    if (!$this->view->enabled) {
      return $this->setNoRender();
    }

    $this->view->urls_js = Zend_Json::encode(array(
      'save_note' => $this->view->url(array('module' => 'usernotes','action' => 'save'), 'default'),
      'delete_note' => $this->view->url(array('module' => 'usernotes','action' => 'delete'), 'default'),
    ));

    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $this->view->page = $page = $this->_getParam('page', 1);
    $this->view->paginator = Engine_Api::_()->usernotes()->getNotesPaginator(array('owner_id' => $this->view->user_id, 'page' => $page));
  }


  public function saveAction()
  {
    $this->view->enabled = $this->_helper->requireAuth()->setAuthParams('usernotes', null, 'enabled')->checkRequire();

    if (!$this->view->enabled) {
      return;
    }
    
    if (!$this->_helper->requireAuth()->setAuthParams('usernotes', null, 'enabled')->isValid()){
      return;
    }

    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->disableLayout(true);
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $form = new Usernotes_Form_Index_Create();

    if ($form->isValid($this->getRequest()->getPost()))
    {
      $db = Engine_Api::_()->getDbTable('usernote', 'usernotes')->getAdapter();
      $db->beginTransaction();

      try
      {
        $usernote_id = $form->save();

        if (empty($usernote_id)) {
          return;
        }

        $row = Engine_Api::_()->usernotes()->getUsernote($usernote_id);

        $db->commit();
        $this->view->usernote = array(
          'usernote_id' => $row['usernote_id'],
          'owner_id' => $row['owner_id'],
          'user_id' => $row['user_id'],
          'note_info' => $this->view->timestamp($row->creation_date),
          'note' => $row['note'],
          'note_br' => nl2br($row['note'])
        );
        
        $this->view->result = array('error'=>'0', 'message'=>Zend_Registry::get('Zend_Translate')->_('Your note saved!'));
      }
      catch (Exception $e)
      {
        $db->rollback();
        $this->view->result = array('error'=>'1', 'message'=>Zend_Registry::get('Zend_Translate')->_('Some error'));
        throw $e;
      }
    }

    return;
  }

  public function deleteAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    
    if (!$this->_helper->requireAuth()->setAuthParams('usernotes', null, 'enabled')->isValid()) {
      return;
    }

    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->disableLayout(true);
    }

    if (!$this->getRequest()->isGet()) {
      return;
    }

    $usernote_id  = $this->getRequest()->getParam('usernote_id');
    $usernote = Engine_Api::_()->getApi('core', 'usernotes')->getUsernote($usernote_id);

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if ($usernote && $viewer_id == $usernote->owner_id)
    {
      $db = Engine_Api::_()->getDbTable('usernote', 'usernotes')->getAdapter();
      $db->beginTransaction();
      
      try {
        Engine_Api::_()->getApi('core', 'usernotes')->deleteUsernote($usernote_id);
        $db->commit();
        
        $this->view->result = array('error'=>'0', 'message'=>Zend_Registry::get('Zend_Translate')->_('Your note has been deleted.'));
      }
      catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    }
    else
    {
      $this->view->result = array('error'=>'1', 'message'=>Zend_Registry::get('Zend_Translate')->_('Failed. Please check and try again later.'));
    }
  }
}