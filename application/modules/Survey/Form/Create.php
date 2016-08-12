<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Form_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $module_path = Engine_Api::_()->getModuleBootstrap('survey')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $this->setTitle('Create survey')
      ->setDescription('Create new survey description')
      ->setAttrib('name', 'surveyzes_create');

    $this->addElement('Text', 'title', array(
      'label' => 'survey_Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
    )));

    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;

    // init to
    $this->addElement('Text', 'tags',array(
      'label'=>'survey_Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'survey_Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));

    $this->tags->getDecorator("Description")->setOption("placement", "append");

    // prepare categories
    $categories = Engine_Api::_()->survey()->getCategories();

    if (count($categories) != 0) {
      $categories_prepared[0] = "";

      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }

      // category field
      $this->addElement('Select', 'category_id', array(
        'label' => 'survey_Category',
        'multiOptions' => $categories_prepared
      ));
    }

    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'survey', 'auth_html');

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
    $this->description->addDecorator('SurveyDescription', array('label' => $translate->_('survey_Survey Description')));

    $this->addElement('File', 'photo', array(
      'label' => 'survey_Upload a Picture',
      'description' => 'This is very important! It will make your survey more popular!',
      'validators' => array(
        array('Extension', false, 'jpg,jpeg,png,gif')
      ),
    ));

    $this->addElement('Checkbox', 'search', array(
      'label' => "Show this survey in search results",
      'value' => 1
    ));

    // View
    $availableLabels = array(
      'everyone' => 'Everyone',
      'owner_network' => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member' => 'Friends Only',
      'owner' => 'Just Me'
    );

    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('survey', $user, 'auth_view');
    $options = array_intersect_key($availableLabels, array_flip($options));

    $this->addElement('Select', 'auth_view', array(
      'label' => 'Privacy',
      'description' => 'Who may see this survey?',
      'multiOptions' => $options,
      'value' => 'everyone',
    ));

    $this->auth_view->getDecorator('Description')->setOption('placement', 'append');

    $options =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('survey', $user, 'auth_comment');
    $options = array_intersect_key($availableLabels, array_flip($options));

    // Comment
    $this->addElement('Select', 'auth_comment', array(
      'label' => 'Comment Privacy',
      'description' => 'Who may post comments on this survey?',
      'multiOptions' => $options,
      'value' => 'everyone',
    ));

    $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Button', 'submit', array(
      'label' => 'Create Survey',
      'type' => 'submit',
    ));
  }
}