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

    //Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //Get Params
    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pageblog.page', 10);

    $this->view->paginator = Engine_Api::_()->getApi('core', 'pageblog')->getBlogsPaginator($params);

    $formValues = array();

    if( !empty($params['search']) ) $formValues['search'] = $params['search'];
    if( !empty($params['orderby']) ) $formValues['orderby'] = $params['orderby'];
    if( !empty($params['show']) ) $formValues['show'] = $params['show'];
    if( !empty($params['category']) ) $formValues['category'] = $params['category'];

    $this->view->formValues = $formValues;

    $this->_helper->content->setEnabled();
  }

  public function manageAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    $params = $this->_request->getParams();

    $form = new Pageblog_Form_Search();
    $form->removeElement('show');
    $form->populate($params);
    $this->view->form = $form;

    //Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //Get Params
    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pageblog.page', 10);
    $params['show'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();

    $this->view->paginator = Engine_Api::_()->getApi('core', 'pageblog')->getBlogsPaginator($params);

    $formValues = array();

    if( !empty($params['search']) ) $formValues['search'] = $params['search'];
    if( !empty($params['orderby']) ) $formValues['orderby'] = $params['orderby'];
    if( !empty($params['category']) ) $formValues['category'] = $params['category'];

    $this->view->formValues = $formValues;

    $this->_helper->content->setEnabled();
  }

  public function deleteAction()
  {
    $table = Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
    $blog = Engine_Api::_()->getItem('pageblog', $this->_request->getParam('pageblog_id'));

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form  = new Pageblog_Form_Delete();

    if( !$blog )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Blog doesn't exists or not authorized to delete");
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
			  $blog->delete();

        $db->commit();
      }catch(Exception $e) {
        $db->rollBack();
        throw $e;
      }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Blog has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'page_blogs', true),
      'messages' => Array($this->view->message)
    ));
  }
}
