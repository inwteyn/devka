<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminLevelController.php 2010-07-02 19:25 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Survey_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('survey_admin_main', array(), 'survey_admin_main_level');

        // Get level id
    if( null !== ($id = $this->_getParam('level_id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;


    // Make form
    $this->view->form = $form = new Survey_Form_Admin_Level(array('public' => $id == 5));
    $form->level_id->setValue($id);

    if ($level == Engine_Api::_()->getItemTable('authorization_level')->getPublicLevel()) {
      foreach (array('delete', 'create', 'take', 'auth_view', 'auth_comment', 'auth_html') as $field)
        $form->removeElement($field);
    }    

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    // Check post
    if (!$this->getRequest()->isPost()) {
      $form->populate($permissionsTable->getAllowed('survey', $id, array_keys($form->getValues())));
      return;
    }

    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('survey');

    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);

      return;
    }

    // Process
    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try
    {
      // Set permissions
      $permissionsTable->setAllowed('survey', $id, $values);

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
}