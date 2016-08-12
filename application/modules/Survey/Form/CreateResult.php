<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CreateResult.php 2010-07-02 19:47 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Form_CreateResult extends Engine_Form
{
  public $_error = array();
  
  public function init()
  {
    $module_path = Engine_Api::_()->getModuleBootstrap('survey')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $this->setTitle('Create survey result')
      ->setDescription('survey_Create Result Form Description')
      ->setAttrib('name', 'survey_create_result');
      
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;
    
    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'survey', 'auth_html');
    
    $this->addElement('Hidden', 'survey_id', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 900
    ));
    
    $this->addElement('Text', 'title', array(
      'label' => 'survey_Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
      new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '100'))
      )));

    $this->addElement('TinyMce', 'description', array(
      'disableLoadDefaultDecorators' => true,
      'required' => true,
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper'
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags' => $allowed_html))),
    ));

    $translate = Zend_Registry::get('Zend_Translate');
    $this->description->addDecorator('SurveyDescription', array('label' => $translate->_('survey_Result Description')));
    
    $this->addElement('File', 'photo', array(
      'label' => 'survey_Upload a Picture',
      'class' => 'resultPhoto',
      'description' => 'This is very important! It will make your survey more popular!',
      'validators' => array(
        array('Extension', false, 'jpg,jpeg,png,gif')
      ),
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Survey Result',
      'type' => 'submit',
    ));
  }
}