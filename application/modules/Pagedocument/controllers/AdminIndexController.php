<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Pagedocument_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_addons');
  }
  
  public function indexAction()
  {
    $this->view->menu = 'global';
    $this->view->form = $form = new Pagedocument_Form_Admin_Global();

    $settings = Engine_Api::_()->getApi('settings', 'core');


    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $settings->setSetting('pagedocument.api.key', $form->getValue('pagedocument_api_key'));
    $settings->setSetting('pagedocument.secret.key', $form->getValue('pagedocument_secret_key'));
    $settings->setSetting('pagedocument.redirect.uri', $form->getValue('pagedocument_redirect_uri'));

    $settings->setSetting('pagedocument.page', $form->getValue('pagedocument_page'));

    $settings->setSetting('pagedocument.document.width', $form->getValue('pagedocument_document_width'));
    $settings->setSetting('pagedocument.document.height', $form->getValue('pagedocument_document_height'));
    $form->addNotice('Your changes have been saved.');

     $table = Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');

     $url_api = $table->authApi();

     if(isset($url_api)){
        $form->addNotice("<a href='".$url_api."' target='_blank'>Open the following link in your browser for registr api</a>");
     }

      if (!$form->isValid($this->getRequest()->getPost())) {
         return;
      }else{
          if($this->getRequest()->getPost()['pagedocument_auth_api']!=''){
              $table = Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');
              $results  = $table->authApiSave($form->getValue('pagedocument_auth_api'));
              if(isset($results)){
                  $form->addNotice("Api is active");
              }
          }
      }
  }
  
  public function categoriesAction()
  {
    $categories_table = Engine_Api::_()->getDbtable('categories', 'pagedocument');

    $params = $this->_getAllParams();
    if (isset($params['id']) && isset($params['direction'])) {
      $table = $categories_table->getFetched();
      $pos = 0;
      for ($i = 0; $i < count($table); $i++) {
        if ($table[$i]['category_id'] == $params['id']) {
          $pos = $i;
          break;
        }
      }

      if(($pos == 0 && $params['direction'] == 1) || ($pos == count($table) - 1 && $params['direction'] == 0)) {
      }
      else {
        $params['direction'] == 1 ? $pos-- : $pos++;
        $row = $categories_table->findRow($table[$pos]['category_id']);
        $current = $categories_table->findRow($params['id']);

        $tmp = $current->order;
        $current->order = $row->order;
        $row->order = $tmp;
        $row->save();
        $current->save();
      }
    }

    $params['page'] = $this->_getParam('page', 1);
    $params['ipp'] = 30;
    $this->view->cats = $cats = $categories_table->getPaginator($params);
    $this->view->menu = 'categories';
  }

  public function orderAction()
  {
    $this->view->params = $params = $this->_getAllParams();
    $this->view->html = $this->view->render('categories_table.tpl');

    return;
  }

  public function addcategoryAction()
  {
    $form = $this->view->form = new Pagedocument_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

      $values = array();

      if ($this->getRequest()->isPost()) {
          $values = $this->getRequest()->getPost();
          if(trim($values['order'])=='') {
              $values['order'] = 1;
          }
      }

    if ($this->getRequest()->isPost() && $form->isValid($values)) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $table = Engine_Api::_()->getDbtable('categories', 'pagedocument');
        $row = $table->createRow();
        $row->user_id = 1;
        $row->category_name = $values['name'];
        $row->order = $values['order'];
        $row->save();

        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    }

    $this->renderScript('admin-index/form.tpl');
  }

  public function editcategoryAction()
  {
    $form = $this->view->form = new Pagedocument_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));


      $values = array();

    if ($this->getRequest()->isPost()) {
        $values = $this->getRequest()->getPost();
        if(trim($values['order']) == '') {
            $values['order'] = 1;
        }
    }

    if ($this->getRequest()->isPost() && $form->isValid($values)) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $row = Engine_Api::_()->pagedocument()->getCategory($values["id"]);

        $row->category_name = $values['name'];
        $row->order = $values['order'];
        $row->save();
        $db->commit();
      }
      catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    }

    if (!($id = $this->_getParam('id'))) {
      die($this->view->translate('No identifier specified'));
    }

    $category = Engine_Api::_()->pagedocument()->getCategory($id);
    $form->fillForm($category);

    $this->renderScript('admin-index/form.tpl');
  }

  public function deletecategoryAction()
  {
    $form = $this->view->form = new Pagedocument_Form_Admin_DeleteCategory();
    $this->view->category_id = $id = $this->_getParam('id');

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $row = Engine_Api::_()->pagedocument()->getCategory($id);
        $row->delete();

        $documentTable = $this->_helper->api()->getDbtable('pagedocuments', 'pagedocument');
        $select = $documentTable->select()->where('category_id = ?', $id);
        $documents = $documentTable->fetchAll($select);

        foreach ($documents as $document) {
          $document->category_id = 0;
          $document->save();
        }

        $db->commit();
      }
      catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh'=> true));
    }

    $this->renderScript('admin-index/delete.tpl');
  }
}