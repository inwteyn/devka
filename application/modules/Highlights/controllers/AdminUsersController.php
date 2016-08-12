<?php
/***/
class Highlights_AdminUsersController extends Core_Controller_Action_Admin {
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('highlight_admin_main', array(), 'highlight_admin_main_users');
    $table = Engine_Api::_()->getDbtable('highlights', 'highlights');
    $this->view->values = $values = $this->_getAllParams();

    $paginator = $table->getHighlightsPaginator($values);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('highlight.per.page', 10);
    $paginator->setItemCountPerPage($items_per_page);
    $page = 1;
    if ($this->_getParam('page') > 1) {
      $this->view->page = $page = intval($this->_getParam('page'));
    }
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    $this->view->filterForm = $form = new Highlights_Form_Admin_Filter();
    $form->populate($values);
  }
  public function viewAction()
  {
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('user_id');
    $this->view->user = Engine_Api::_()->user()->getUser($id);
  }
  public function deleteAction()
  {
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('highlight_id');
    $this->view->highlight_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $highlight = Engine_Api::_()->getDbtable('highlights', 'highlights')->gethighlight($id);
        // delete highlight
//        $highlight->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-users/delete.tpl');
  }
  public function addAction()
  {
    $db = Engine_Api::_()->getDbTable('highlights', 'highlights')->getAdapter();
    $db->beginTransaction();
    $user_id = intval($this->_getParam('user_id'));
    $value = intval($this->_getParam('value'));
    $translate = Zend_Registry::get('Zend_Translate');
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      $this->_forward('success', 'utility', 'core',
        array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array($translate->translate('HIGHLIGHT_disabled'))
        )
      );
      return;
    }
    try {
      $table = Engine_Api::_()->getDbtable('highlights', 'highlights');
      if ($value == 1) {
        $check = $table->getByUserId($user_id);
        if (empty($check)) {
          $highlight = $table->createRow();
          $values = array(
            'owner_id' => $user_id,
            'date_start' => new Zend_Db_Expr('NOW()'),
            'date_finish' => new Zend_Db_Expr('DATE_ADD(NOW(), INTERVAL ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('highlight.num.days', 10) . ' DAY)'),
          );
          $highlight->setFromArray($values);
          $highlight->save();
        } else {
          $check['date_start'] = new Zend_Db_Expr('NOW()');
          $check['date_finish'] = new Zend_Db_Expr('DATE_ADD(NOW(), INTERVAL ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('highlight.num.days', 10) . ' DAY)');
          $check->save();
        }
      } elseif ($value == 0) {
        $highlight = $table->getByUserId($user_id);
        $highlight->delete();
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_forward('success', 'utility', 'core',
      array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(($value == 1)?$translate->translate('HIGHLIGHT_Added to highlight list'):$translate->translate('HIGHLIGHT_Removed from highlight list'))
      )
    );
  }
  public function editAction()
  {
    $highlight_id = $this->_getParam('highlight_id');
    $this->view->form = $form = new Highlights_Form_Admin_Edit();
    $this->view->highlight = $highlight = Engine_Api::_()->getDbTable('highlights', 'highlights')->getHighlight($highlight_id);
    $form->populate($highlight->toArray());
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $db = Engine_Api::_()->getDbTable('highlights', 'highlights')->getAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
      $highlight['date_finish'] = $values['date_finish'];
      $highlight->save();
      $db->commit();
      $translate = Zend_Registry::get('Zend_Translate');
      $this->_forward('success', 'utility', 'core',
        array(
          'smoothboxClose' => 2000,
          'parentRefresh' => true,
          'messages' => array($translate->translate('HIGHLIGHT_Date finish changed'))
        )
      );
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }
}