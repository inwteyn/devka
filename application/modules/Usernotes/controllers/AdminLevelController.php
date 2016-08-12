<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminLevelController.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('usernotes_admin_main', array(), 'usernotes_admin_main_level');

    // Get level id
    if (null !== ($id = $this->_getParam('id'))) {
      if ($id == 5) {
        $id = 1;
      }
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    $this->view->form = $form = new Usernotes_Form_Admin_Level();
    $form->level_id->setValue($id);

    $form->getElement('level_id')->removeMultiOption(5);

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

      // Check post
    if (!$this->getRequest()->isPost()) {
      $form->populate($permissionsTable->getAllowed('usernotes', $id, array_keys($form->getValues())));
      return;
    }

    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('usernotes');

    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);

      return;
    }

    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      $permissionsTable->setAllowed('usernotes', $id, $values);
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
  }
}