<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecontest_Form_Admin_Create extends Engine_Form
{
    protected $_edit;

    public function __construct($edit = false)
    {
        $this->_edit = $edit;
        parent::__construct();
    }

    public function init()
    {
        $settingsTbl = Engine_Api::_()->getDbtable('settings', 'core');
        $module_path = Engine_Api::_()->getModuleBootstrap('hecontest')->getModulePath();
        $this->addPrefixPath('Engine_Form_Element_', $module_path . '/Form/Element/', 'element');

        // Title
        $this->addElement('Text', 'title', array(
            'label' => 'Contest Title',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(1, 64)),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('TinyMce', 'description', array(
            'label' => $this->getTranslator()->translate('Description'),
            /*'disableLoadDefaultDecorators' => true,
            'required' => true,
            'allowEmpty' => false,
            'decorators' => array(
                'ViewHelper'
            ),
            'filters' => array(
                new Engine_Filter_Censor()
            )*/
        ));

        $this->addElement('TinyMce', 'terms', array(
            'label' => $this->getTranslator()->translate('Terms'),
            /*'disableLoadDefaultDecorators' => true,
            'required' => true,
            'allowEmpty' => false,
            'decorators' => array(
                'ViewHelper'
            ),
            'filters' => array(
                new Engine_Filter_Censor()
            )*/
        ));


        $params = array(
            'mode' => 'exact',
            'elements' => array('description', 'terms'),
            'width' => '500px',
            'height' => '225px',
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














        $this->addElement('Text', 'prize_name', array(
            'label' => 'Prize Name',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(1, 64)),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        if ($this->_edit) {
            $this->addElement('Dummy', 'prize_photo_preview', array(
                'content' => '<img src="">'
            ));
        }

        $this->addElement('File', 'photo', array(
            'label' => 'Prize Photo',
            'accept' => 'image/*',
            'required'=>!$this->_edit
        ));
        if ($this->_edit) {
            $this->addElement('Dummy', 'photo_preview', array(
                'content' => '<img src="">'
            ));
        }

        $this->addElement('File', 'photo_main', array(
            'label' => 'Main photo',
            'accept' => 'image/*',
            'required'=>!$this->_edit
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        if(Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('credit')){
            $this->addElement('Text', 'price_credit', array(
              'label' => 'Paid participation',
              'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(1, 64)),
              ),
              'value' => 0,
              'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
              ),
            ));

        }else{

                $this->addElement('Dummy', 'photo_preview', array(
                  'label' => 'Paid participation',
                  'content' => 'Install <a href="http://www.hire-experts.com/social-engine/credits-plugin" target="_blank">Credits</a> plugin to activate this settings'
                ));

        }

        $isPageEnabled = Engine_Api::_()->hecontest()->isPageEnabled();

        if($isPageEnabled) {
            $this->addElement('Radio', 'sponsor_type', array(
                'label' => 'Sponsor Type',
                'multiOptions' => array(
                    '0' => 'Page',
                    '1' => 'Name'
                ),
                'value' => 1
            ));

            $this->addElement('Hidden', 'sponsor_url');
        }

        $this->addElement('Text', 'sponsor_href', array(
            'label' => 'Sponsor Href',
            'allowEmpty' => true,
            'required' => false,
            'placeholder' => 'http://',
            'validators' => array(
                array('NotEmpty', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'sponsor', array(
            'label' => 'Contest Sponsor',
            'allowEmpty' => false,
            'required' => true,
            'autocomplete' =>'off',
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(1, 64)),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Datepicker', 'date_begin', array(
            'allowEmpty' => false,
            'required' => true,
        ));
        $this->date_begin->setLabel('Start Time');

        $this->addElement('Datepicker', 'date_end', array(
            'allowEmpty' => true,
            'required' => false,
        ));
        $this->date_end->setLabel('End Time');

        $this->addElement('Button', 'submit', array(
            'label' => $this->_isComposer ? 'Create' : 'Save Changes',
            'type' => $this->_isComposer ? 'button' : 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $dgBtns = array('submit');

        if ($this->_edit) {
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
                'href' => '',
                'onclick' => 'parent.Smoothbox.close();',
                'decorators' => array(
                    'ViewHelper'
                )
            ));
            $dgBtns = array('submit', 'cancel');
        }

        $this->addDisplayGroup($dgBtns, 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));

    }

}