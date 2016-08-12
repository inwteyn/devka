<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_Classified_Create extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Post New Listing')
      ->setDescription('Compose your new classified listing below, then click "Post Listing" to publish the listing.')
      ->setAttrib('name', 'classifieds_create');

    $this->setAction($this->getView()->url());

    $this->addElement('Text', 'title', array(
      'label' => 'Listing Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      ),
    ));

    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

    // init to
    $this->addElement('Text', 'tags', array(
      'label' => 'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    $classifiedApi = Engine_Api::_()->getApi('core', 'classified');
    $classifiedCatsTbl = Engine_Api::_()->getDbTable('categories', 'classified');

    // prepare categories
    if (method_exists($classifiedApi, 'getCategories')) {
      $categories = $classifiedApi->getCategories();
      foreach ($categories as $category){
        $categories_prepared[$category->category_id]= $category->category_name;
      }
    } else {
      $categories = $classifiedCatsTbl->getCategoriesAssoc();
      $categories_prepared = $categories;
    }

    if( count($categories) != 0 ) {
      $categories_prepared[0] = "";
      // category field
      $this->addElement('Select', 'category_id', array(
        'label' => 'Category',
        'multiOptions' => $categories_prepared
      ));
    }

    $this->addElement('Textarea', 'body', array(
      'label' => 'Description',
      'filters' => array(
        'StripTags',
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
    ));

   $allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'classified', 'photo');
    if( $allowed_upload ) {

      if (!isset($_FILES['photo'])){
        // ignore Zend_Validate_File_Upload::INI_SIZE
        $_FILES['photo'] = array(
          'name' => '',
          'type' => '',
          'tmp_name' => '',
          'error' => 4,
          'size' => 0
        );
      }

      $this->addElement('File', 'photo', array(
        'label' => 'Main Photo'
      ));
      $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
      $this->addElement('Hidden', 'photo_id', array());
    }

    // Add subforms
    if( !$this->_item ) {
      $customFields = new Classified_Form_Custom_Fields();
    } else {
      $customFields = new Classified_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Classified_Form_Create' ) {
      $customFields->setIsCreation(true);
    }

    $this->addSubForms(array(
      'fields' => $customFields
    ));

    // Privacy
    $availableLabels = array(
      'everyone'            => 'Everyone',
      'registered'          => 'All Registered Members',
      'owner_network'       => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member'        => 'Friends Only',
      'owner'               => 'Just Me',
    );

    // View
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('classified', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this classified listing?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('classified', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this classified listing?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Post Listing',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }

}