<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Form_Admin_Badge_Create extends Engine_Form
{

  public function init()
  {
    $module_path = Engine_Api::_()->getModuleBootstrap('hebadge')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');


    $this->setTitle('HEBADGE_FORM_ADMIN_BADGE_CREATE_TITLE');
    $this->setDescription('HEBADGE_FORM_ADMIN_BADGE_CREATE_DESCRIPTION');

    $this->addElement('text', 'title', array(
      'label' => 'HEBADGE_FORM_ADMIN_BADGE_LABEL_TITLE', '',
      'required' => true,
      'allowEmpty' => false,
    ));
    $this->addElement('checkbox', 'enabled', array(
      'label' => 'HEBADGE_FORM_ADMIN_BADGE_LABEL_ENABLED',
      'value' => 1
    ));


    $this->addElement('textarea', 'description', array(
      'label' => 'HEBADGE_FORM_ADMIN_BADGE_LABEL_DESCRIPTION',
      'required' => true,
      'allowEmpty' => false,
    ));


    $this->getView()->tinyMce()->setOptions(array('mode' => 'exact', 'elements' => 'body'));


    $this->addElement('TinyMce', 'body', array(
      'disableLoadDefaultDecorators' => true,
      'decorators' => array(
        'ViewHelper'
      ),
      'editorOptions' => array(
        'upload_url' => ''
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
       ),
    ));


    $this->addElement('file', 'photo', array('label' => 'HEBADGE_FORM_ADMIN_BADGE_LABEL_PHOTO', 'description' => 'HEBADGE_FORM_ADMIN_BADGE_DESCRIPTION_PHOTO'));
    $this->addElement('file', 'icon', array('label' => 'HEBADGE_FORM_ADMIN_BADGE_LABEL_ICON', 'description' => 'HEBADGE_FORM_ADMIN_BADGE_DESCRIPTION_ICON'));

    $this->getElement('photo')->addDecorator('HebadgePhoto');
    $this->getElement('icon')->addDecorator('HebadgePhoto');


    $this->addElement('text', 'require', array('order' => 6));
    $this->require->addDecorator('hebadgeRequire', array(
      'items' => Engine_Api::_()->hebadge()->getRequireList()
    ));


    $this->addElement('button', 'submit', array('label' => 'Save Changes', 'type' => 'submit'));


  }


  public function isValidRequire($post)
  {
    $valid = true;

    foreach ($this->getElement('require')->getDecorator('hebadgeRequire')->getData() as $type => $item){
      $item['element']->setChecked($post['require'][$type]);
      if ($post['require'][$type]){
        if (!$item['form']->isValid($post)){
          $valid = false;
        }
      } else {
        // @TODO set populate values
      }
    }
    return $valid;
  }

  public function getValuesRequire()
  {
    $values = array();

    foreach ($this->getElement('require')->getDecorator('hebadgeRequire')->getData() as $type => $item){
      foreach ($item['form']->getValues() as $key => $value){
        if (!$item['element']->isChecked()){
          continue ;
        }
        $new_key = substr($key, 8); // cut require_
        $values[$new_key] = $value;
      }
    }
    return $values;
  }

  public function setValuesRequire($values = array())
  {
    foreach ($this->getElement('require')->getDecorator('hebadgeRequire')->getData() as $type => $item){
      if (!empty($values[$type])){
        $item['element']->setChecked(true);
        if (is_array($values[$type])){
          $item['form']->populate($values[$type]);
        }
      }
    }
  }


}