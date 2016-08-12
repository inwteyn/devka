<?php

class Hecontest_Form_Admin_Action extends Engine_Form
{
    // 0 - delete
    // 1 - activate
    // 2 - deactivate
    public $_mode = 0;
    public $_submitText = '';

    public function __construct($mode = 0)
    {
        $this->_mode = (int) $mode;
        parent::__construct();
    }

    public function init()
    {
        switch ($this->_mode) {
            case 1:
                $this->setTitle('Activate Contest');
                $this->setDescription('Are you sure you want to activate this contest?');
                $this->_submitText = 'Activate';
                break;
            case 2:
                $this->setTitle('Deactivate Contest');
                $this->setDescription('Are you sure you want to deactivate this contest?');
                $this->_submitText = 'Deactivate';
                break;
            default:
                $this->setTitle('Delete Contest');
                $this->setDescription('Are you sure you want to delete this contest?');
                $this->_submitText = 'Delete';
                break;
        }


        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => $this->_submitText,
            'decorators' => array('ViewHelper')
        ));

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
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');

        // Set default action
        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    }
}
