<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2010-07-02 19:25 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Survey_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('survey_These settings affect all members in your community.');
    
    $this->addElement('Text', 'surveys_min_result_count', array(
      'label' => 'survey_Choices',
      'description' => 'ADMIN_QUIZ_GLOBAL_FORM_MIN_ANSWER',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('surveys.min.result.count', 2),
    ));

    $this->addElement('Text', 'surveys_min_question_count', array(
      'label' => 'survey_Questions',
      'description' => 'ADMIN_QUIZ_GLOBAL_FORM_MIN_QUESTION',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('surveys.min.question.count', 1),
    ));

    $this->addElement('Text', 'surveys_items_onpage', array(
      'label' => 'Surveys on page',
      'description' => 'ADMIN_QUIZ_GLOBAL_FORM_MIN_ON_PAGE',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('surveys.items.onpage', 10),
    ));

    $this->addElement('Checkbox', 'surveys_approve', array(
      'label' => 'Auto-approve new submitted surveys',
      'description' =>  'ADMIN_QUIZ_GLOBAL_FORM_APPROVE',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('surveys.approve', 1),
    ));
  
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}