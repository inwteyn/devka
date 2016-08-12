<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CreateQuestion.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Form_CreateQuestion extends Engine_Form
{
  public $_error = array();
  
  public function init()
  {
    $this->setTitle('Create survey question')
      ->setDescription('Create survey question description')
      ->setAttrib('name', 'survey_create_question');
      
    $this->addElement('Text', 'text', array(
      'label' => 'survey_Question',
      'allowEmpty' => false,
      'required' => true,
      'order' => 1,
      'filters' => array(
      new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '255'))
      )));
    
    $this->addElement('File', 'photo', array(
      'label' => 'survey_Upload a Picture',
      'order' => 900,
      'description' => 'This is very important! It will make your survey more popular!',
      'validators' => array(
        array('Extension', false, 'jpg,jpeg,png,gif')
      ),
    ));
        
    $this->addElement('Hidden', 'survey_id', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 901
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Survey Question',
      'type' => 'submit',
      'order' => 903
    ));
  }
}