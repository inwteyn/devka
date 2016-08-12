<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Form.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_Form_Form extends Engine_Form
{

  public  $_page;

  public function __construct($subject){
    $this->_page = $subject;

    parent::__construct($subject );
	}

  public function init()
  {

    $module_path = Engine_Api::_()->getModuleBootstrap('pageevent')->getModulePath();
    $this->addPrefixPath('Engine_Form_Element_', $module_path . '/Form/Element/', 'element');

    $user = Engine_Api::_()->user()->getViewer();

    $this
        ->setTitle('PAGEEVENT_CREATE_TITLE')
        ->setDescription('PAGEEVENT_CREATE_DESCRIPTION')
        ->setAttrib('onsubmit', 'return Pageevent.formSubmit(this);')
        ->setAttrib('id', 'pageevent-form');

    $this->addElement('Text', 'title', array(
      'label' => 'PAGEEVENT_FORM_TITLE',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'PAGEEVENT_FORM_DESCRIPTION',
      'maxlength' => '512',
      'class' => 'mceNoEditor',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Datepicker', 'starttime', array(
      'allowEmpty' => false,
      'required' => true,
    ));
    $this->starttime->setLabel('PAGEEVENT_FORM_STARTTIME');

    $this->addElement('Datepicker', 'endtime', array(
      'allowEmpty' => false,
      'required' => true,
    ));
    $this->endtime->setLabel('PAGEEVENT_FORM_ENDTIME');

    $fancyUpload2 = new Engine_Form_Element_FancyUpload('event_photo');
    $fancyUpload2->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                  'viewScript' => 'fancy_upload_photo.tpl',
                  'placement'  => '',
                  ));

    Engine_Form::addDefaultDecorators($fancyUpload2);
    $this->addElement($fancyUpload2);

    $this->addElement('Text', 'location', array(
      'label' => 'PAGEEVENT_FORM_LOCATION',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Checkbox', 'approval', array(
      'label' => 'PAGEEVENT_FORM_APPROVAL',
    ));

    $this->addElement('Checkbox', 'invite', array(
      'label' => 'PAGEEVENT_FORM_INVITE',
      'value' => True
    ));

    $availableLabels = array(
      'everyone' => 'Everyone',
      'registered' => 'Registered Members',
      'likes' => 'Likes, Admins and Owner',
      'team' => 'Admins and Owner Only',
    );


    $view_options = array();
    $view_role = array();

    if (Engine_Api::_()->getApi('settings', 'core')->__get('page.package.enabled') && $this->_page instanceof Page_Model_Page)
        {
          /**
           * @var $page Page_Model_Package
           */
          $package = $this->_page->getPackage();

          $view_options = $package->auth_view;
        }
    else {
      $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_view');
    }

    $view_options = array_intersect_key($availableLabels, array_flip($view_options));
    foreach ($view_options as $role => $value) {
      $role_str = $role;
    	if( $role === 'team' ) {
    		$role = $this->_page->getTeamList();
    	}

    	elseif( $role === 'likes' ) {
    		$role = $this->_page->getLikesList();
    	}

      if (1 == Engine_Api::_()->authorization()->isAllowed($this->_page, $role, 'view')) {
        $view_role[$role_str] = $value;
      }
    }

    if (!empty($view_role)) {
      $this->addElement('Radio', 'privacy', array(
        'label' => 'View Privacy',
        'description' => 'Who can see this event?',
        'multiOptions' => $view_role,
        'value' => key($view_role),
      ));
    }

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'buton',
      'onclick' => "Pageevent.formSubmit(this.getParent('form'));",
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),));


    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
      'onclick' => 'return Pageevent.formCancel();'
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));

    $this->addElement('Hidden', 'id', array('value' => 0));

  }
}
