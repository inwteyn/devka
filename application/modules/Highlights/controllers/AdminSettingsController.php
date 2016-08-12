<?php
/***/
class Highlights_AdminSettingsController extends Core_Controller_Action_Admin {
  public function indexAction(){
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('highlight_admin_main', array(), 'highlight_admin_main_settings');
    $this->view->form = $form = new Highlights_Form_Admin_Settings();
    $page = 1;
    if ($this->_getParam('page') > 1) {
      $page = $this->_getParam('page');
    }
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('credit')) {
      $this->view->paginator = $logs = Engine_Api::_()->getDbTable('highlights', 'highlights')->getLogsPaginator(array('page' => $page));
    }
    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      // Check license
      $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
      $product_result = $hecoreApi->checkProduct('highlights');

      if (isset($product_result['result']) && !$product_result['result']) {
        $form->addError($product_result['message']);
        $this->view->headScript()->appendScript($product_result['script']);

        return;
      }
      $values = $form->getValues();
      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }
}