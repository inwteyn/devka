<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminFieldsController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_moduleName = 'Store';

  protected $_fieldType = 'store_product';

  protected $_requireProfileType = true;

  public function init()
  {
    $this->view->activeMenu = 'store_admin_main_settings';

    parent::init();
  }


  public function fieldCreateAction()
  {
    if ($this->_requireProfileType || $this->_getParam('option_id')) {
      $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    } else {
      $option = null;
    }

    // Check type param and get form class
    $cfType = $this->_getParam('type');
    $adminFormClass = null;
    if (!empty($cfType)) {
      $adminFormClass = Engine_Api::_()->fields()->getFieldInfo($cfType, 'adminFormClass');
    }
    if (empty($adminFormClass) || !@class_exists($adminFormClass)) {
      $adminFormClass = 'Store_Form_Admin_Field';
    }
    $adminFormClass = 'Store_Form_Admin_Field';
    // Create form
    $this->view->form = $form = new $adminFormClass();

    // Create alt form
    $this->view->formAlt = $formAlt = new Fields_Form_Admin_Map();
    $formAlt->setAction($this->view->url(array('action' => 'map-create')));

    // Get field data for auto-suggestion
    $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType);
    $fieldList = array();
    $fieldData = array();
    foreach (Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType) as $field) {
      if ($field->type == 'profile_type') continue;

      // Ignore fields in the same category as we have selected
      foreach ($fieldMaps as $map) {
        if ((!$option || !$map->option_id || $option->option_id == $map->option_id) && $field->field_id == $map->child_id) {
          continue 2;
        }
      }

      // Add
      $fieldList[] = $field;
      $fieldData[$field->field_id] = $field->label;
    }
    $this->view->fieldList = $fieldList;
    $this->view->fieldData = $fieldData;

    if (count($fieldData) < 1) {
      $this->view->formAlt = null;
    } else {
      $formAlt->getElement('field_id')->setMultiOptions($fieldData);
    }

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $form->populate($this->_getAllParams());
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
      'option_id' => (is_object($option) ? $option->option_id : '0'),
    ), $form->getValues()));

    // Should get linked in field creation
    //$fieldMap = Engine_Api::_()->fields()->createMap($field, $option);

    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->option = is_object($option) ? $option->toArray() : array('option_id' => '0');
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach ($maps as $map) {
      $html[$map->getKey()] = $this->view->storeAdminFieldMeta($map);
    }
    $this->view->htmlArr = $html;

    $url = Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
          array(
            'module' => 'store',
            'controller' => 'fields',
            'action' => 'index'
          ),
          'admin_default', true
        ) . '?option_id=' . $this->_getParam('option_id');
    $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => $url,
      //'messages' => Array(Zend_Registry::get('Zend_Translate')->_('Audio has been deleted.'))
    ));
  }

  public function fieldEditAction()
  {
    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

    // Check type param and get form class
    $cfType = $this->_getParam('type', $field->type);
    $adminFormClass = null;
    if (!empty($cfType)) {
      $adminFormClass = Engine_Api::_()->fields()->getFieldInfo($cfType, 'adminFormClass');
    }
    if (empty($adminFormClass) || !@class_exists($adminFormClass)) {
      $adminFormClass = 'Store_Form_Admin_Field';
    }

    $adminFormClass = 'Store_Form_Admin_Field';

    // Create form
    $this->view->form = $form = new $adminFormClass();
    $form->setTitle('Edit Sub Category');

    // Get sync notice
    $linkCount = count(Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)
      ->getRowsMatching('child_id', $field->field_id));
    if ($linkCount >= 2) {
      $form->addNotice($this->view->translate(array(
        'This question is synced. Changes you make here will be applied in %1$s other place.',
        'This question is synced. Changes you make here will be applied in %1$s other places.',
        $linkCount - 1), $this->view->locale()->toNumber($linkCount - 1)));
    }

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $form->populate($field->toArray());
      $form->populate($this->_getAllParams());
      if (is_array($field->config)) {
        $form->populate($field->config);
      }
      $this->view->search = $field->search;
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    Engine_Api::_()->fields()->editField($this->_fieldType, $field, $form->getValues());

    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach ($maps as $map) {
      $html[$map->getKey()] = $this->view->storeAdminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }


  public function optionCreateAction()
  {
    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);
    $label = $this->_getParam('label');

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Create new option
    $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
      'label' => $label,
    ));

    $this->view->status = true;
    $this->view->option = $option->toArray();
    $this->view->field = $field->toArray();

    // Re-render all maps that have this options's field as a parent or child
    $maps = array_merge(
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id),
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->storeAdminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  public function optionEditAction()
  {
    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    $field = Engine_Api::_()->fields()->getField($option->field_id, $this->_fieldType);

    // Create form
    $this->view->form = $form = new Fields_Form_Admin_Option();
    $form->submit->setLabel('Edit Choice');

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $form->populate($option->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    Engine_Api::_()->fields()->editOption($this->_fieldType, $option, $form->getValues());

    // Process
    $this->view->status = true;
    $this->view->form = null;
    $this->view->option = $option->toArray();
    $this->view->field = $field->toArray();

    // Re-render all maps that have this options's field as a parent or child
    $maps = array_merge(
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id),
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->storeAdminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }
}