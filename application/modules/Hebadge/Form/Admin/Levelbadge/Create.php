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



class Hebadge_Form_Admin_Levelbadge_Create extends Engine_Form
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

    $this->getElement('photo')->addDecorator('HebadgeLevelPhoto');
    $this->getElement('icon')->addDecorator('HebadgeLevelPhoto');

    $subform = new Hebadge_Form_Admin_Levelbadge_LevelSubform();
    $this->addSubForm($subform, 'levels');

    $this->addElement('button', 'submit', array('label' => 'Save Changes', 'type' => 'submit'));


  }
}