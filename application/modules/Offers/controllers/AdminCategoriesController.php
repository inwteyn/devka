<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminCategoriesController.php 2012-07-25 12:40 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_AdminCategoriesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('offer_admin_main', array(), 'offer_admin_main_categories');

    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'offers')->fetchAll();
  }

  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Offers_Form_Admin_Categories();
    $form->setAction($this->view->url());

    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-categories/form.tpl');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-categories/form.tpl');
      return;
    }

    // Process
    $values = $form->getValues();

    $categoryTable = Engine_Api::_()->getDbtable('categories', 'offers');
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $categoryTable->insert(array(
        'title' => $values['label'],
      ));

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->category_id = $id;
    $this->view->event_id = $id;
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'offers');
    $offersTable = Engine_Api::_()->getDbtable('offers', 'offers');
    $category = $categoryTable->find($id)->current();

    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-categories/delete.tpl');
      return;
    }

    // Process
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $category_id = $category->category_id;
      $offersTable->setDefaultCategory($category_id);

      $category->delete();


      $offersTable->update(array(
        'category_id' => 1,
      ), array(
        'category_id = ?' => $category->getIdentity(),
      ));

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->event_id = $id;
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'offers');
    $category = $categoryTable->find($id)->current();

    // Generate and assign form
    $form = $this->view->form = new Offers_Form_Admin_Categories();
    $form->setAction($this->view->url());
    $form->setField($category);

    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-categories/form.tpl');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-categories/form.tpl');
      return;
    }

    // Ok, we're good to add field
    $values = $form->getValues();

    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $category->title = $values['label'];
      $category->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }
}