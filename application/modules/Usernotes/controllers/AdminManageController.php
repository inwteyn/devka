<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminManageController.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('usernotes_admin_main', array(), 'usernotes_admin_main_manage');

    $this->view->formFilter = $formFilter = new Usernotes_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    $this->view->urls_js =  Zend_Json::encode(array(
      'save_note'=>$this->view->url(array('module' => 'usernotes','action' => 'save'), 'default'),
      'delete_note'=>$this->view->url(array('module' => 'usernotes','action' => 'delete'), 'default'),
    ));

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select();

    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
          unset($values[$key]);
      }
    }

    $values = array_merge(array('order' => 'user_id', 'order_direction' => 'DESC',), $values);

    $select->setIntegrityCheck(false)->from(array('u'=>'engine4_users'));
    $select->joinLeft(array('n'=>'engine4_usernotes_usernote'), "`u`.`user_id` = `n`.`user_id`", array('note', 'usernote_id'));

    $this->view->assign($values);

    // Set up select info
    $order = (!empty($values['order']) ? 'u.' . $values['order'] : 'u.user_id') . ' '
      . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC');
    $select->order($order);

    if (!empty($values['username']))
    {
      $select->where('u.username LIKE ?', '%' . $values['username'] . '%');
    }

    if (!empty($values['email']))
    {
      $select->where('u.email LIKE ?', '%' . $values['email'] . '%');
    }

    if (!empty($values['level_id']))
    {
      $select->where('u.level_id = ?', $values['level_id'] );
    }

    if (isset($values['with_usernote']) && $values['with_usernote'] != -1)
    {
      if ($values['with_usernote']==0) {
        $select->where('ISNULL(n.note)');
      } elseif ($values['with_usernote']==1) {
        $select->where('NOT ISNULL(n.note)');
      }
    }

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );

    $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;

    $user_id = 1;

    $this->view->create_form = $create_form = new Usernotes_Form_Admin_Create();
    $create_form->getElement('user_id')->setValue($user_id);
  }

  public function saveAction()
  {
    $this->view->user_id = $user_id = $this->_getParam('user_id', null);
    $this->view->create_form = $create_form = new Usernotes_Form_Admin_Create();
    $create_form->getElement('user_id')->setValue($user_id);
    $usernote_id = $this->_getParam('usernote_id', null);

    if ($this->getRequest()->isPost())
    {
      if ($create_form->isValid($this->getRequest()->getPost()))
      {
        $create_form->save();
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'format'=> 'smoothbox',
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Note saved'))
      ));
    }
    else
    {
      if (isset($usernote_id)) {
        $usernote = Engine_Api::_()->usernotes()->getUsernote($usernote_id);

        $create_form->getElement('user_id')->setValue($usernote->user_id);
        $create_form->getElement('note')->setValue($usernote->note);
      }
    }

    return;
  }

}