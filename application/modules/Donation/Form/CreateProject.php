<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 19.07.12
 * Time: 15:56
 * To change this template use File | Settings | File Templates.
 */
class Donation_Form_CreateProject extends Engine_Form
{
  protected $_id;
  protected $_request;


  public function setId($id)
  {
    $this->_id = $id;
    return $this;
  }

  public function getId()
  {
    return $this->_id;
  }

  public function setRequest($request)
  {
    $this->_request = $request;
    return $this;
  }

  public function getRequest()
  {
    return $this->_request;
  }

  public function getPage()
  {
    if ($this->hasPage()) {
      return Engine_Api::_()->getItem('page', $this->getId());
    }
    return '';
  }

  public function hasPage()
  {
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page') &&
      isset($this->_id) &&
      !empty($this->_id) &&
      $this->_id != null &&
      Engine_Api::_()->getItem('page', $this->_id)
    ) {
      return true;
    }
    return false;
  }

  public function init()
  {
    $view = Zend_Registry::get('Zend_View');
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $title = $this->_request->getActionName() == 'create' ? 'Create Project' : 'Edit Project';
    if ($this->hasPage()) {
      //$title .= '<a href="' . $this->getPage()->getHref() . '">' . $this->getPage()->getTitle() . "</a>";
    }
    $this->setTitle($title)
      ->setDescription('Compose your new donation entry below, then click "Post Donation Entry" to publish the entry to your donation entries.')
      ->setAttrib('name', 'donations_create')
      ->setAttrib('class', 'global_form group_form_upload')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('enctype', 'multipart/form-data');

    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
      'autofocus' => 'autofocus',
    ));

    // Element: short description
    $this->addElement('Textarea', 'short_desc', array(
      'label' => 'Short Description',
      'maxlength' => '10000',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 10000)),
      ),
    ));

    // Element: description (HTML)
    $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
      'allowEmpty' => false,
      'required' => true,
    ));
    $params = array(
      'mode' => 'exact',
      'elements' => 'description',
      'width' => '500px',
      'height' => '250px',
      'theme_advanced_buttons1' => array(
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|',
        'bullist', 'numlist', '|',
        'undo', 'redo', '|',
        'sub', 'sup', '|',
        'forecolor', 'forecolorpicker', 'backcolor', 'backcolorpicker', '|'
      ),
      'theme_advanced_buttons2' => array(
        'newdocument', 'code', 'image', 'media', 'preview', 'fullscreen', '|',
        'link', 'unlink', 'anchor', 'charmap', 'cleanup', 'hr', 'removeformat', 'blockquote', 'separator', 'outdent', 'indent', '|',
        'selectall', 'advimage'),
      'theme_advanced_buttons3' => array('formatselect', 'fontselect', 'fontsizeselect', 'styleselectchar', '|', 'table', '|'),
    );
    $this->getView()->getHelper('TinyMce')->setOptions($params);

    // Element: category
    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
    ));

    // Element: Profile Photo
    $this->addElement('File', 'photo', array(
      'label' => 'Profile Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    // expiry_date
    $expiry_date = new Engine_Form_Element_CalendarDateTime('expiry_date');
    $expiry_date->setLabel("DONATION_expiry_date")
      ->setAllowEmpty(true)
      ->setDescription('DONATION_EXPIRY_DATE_DESC');
    $this->addElement($expiry_date);
    $expiry_date->getDecorator('Description')->setOption('escape', false);

    // Element: target
    $this->addElement('Text', 'target_sum', array(
      'label' => 'DONATION_target_sum',
      'allowEmpty' => false,
      'validators' => array(
        array('Float', true),
      ),
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
      ),
    ));

    // Element: min amount
    $this->addElement('Text', 'min_amount', array(
      'label' => 'Minimal Amount',
      'allowEmpty' => false,
      'value' => $settings->getSetting('donation.minimum.amount', 0.15),
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast($settings->getSetting('donation.minimum.amount', 0.15)),
      ),
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        //new Zend_Validate_Callback('is_numeric')
      ),
    ));

    // Element : canSelect
    $this->addElement('Radio', 'can_choose_amount', array(
      'label' => 'Do you allow member to select the donation amount himself?',
      'multiOptions' => array(
        '0' => 'Yes, allow members to select the donation amount',
        '1' => $view->translate("No, select from predefined list"),
      ),
      'value' => '1',
      "onClick" => "switchSelectAmount()",
    ));

    $this->addElement('Text', 'predefine_list', array(
      'label' => 'List of predefined donation amounts',
      'value' => '5,10,20,50,100'
    ));

    // Element: anonymous
    $this->addElement('Checkbox', 'allow_anonymous', array(
      'Label' => 'Allow anonymous donations? If donor select anonymous donation then his name and photo are hidden from public.',
      'value' => True
    ));

    // Element: Country
    $this->addElement('Text', 'country', array(
      'label' => 'Country',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    // Element: State
    $this->addElement('Text', 'state', array(
      'label' => 'State',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    // Element: City
    $this->addElement('Text', 'city', array(
      'label' => 'City',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    // Element: Street
    $this->addElement('Text', 'street', array(
      'label' => 'Street',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    // Element: Phone
    $this->addElement('Text', 'phone', array(
      'label' => 'Phone',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StringTrim',
      ),
    ));

    if ($this->hasPage()) {
      $page = $this->getPage();
      $this->phone->setValue($page->phone);
      $this->street->setValue($page->street);
      $this->city->setValue($page->city);
      $this->state->setValue($page->state);
      $this->country->setValue($page->country);
    }

    // Element : Fancy Upload
    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->setLabel('Photos')
      ->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
      'viewScript' => '_FancyUpload.tpl',
      'placement' => '',
    ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Hidden', 'fancyuploadfileids');


    // Buttons
    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    if ($this->_request->getActionName() == 'create') {
      $this->submit->setLabel('Create Project');
    } else {
      $this->submit->setLabel('Save Changes');
    }

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
