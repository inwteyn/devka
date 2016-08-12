<?php
/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 15.04.2015
 * Time: 11:47
 */

class Timeline_Form_Profile_EditPagePhoto extends Engine_Form
{
    public function init()
    {
        $this->setTitle('Change Photo')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setDescription('Timeline_Edit your Page photo.')
            ->setAttrib('name', 'EditPagePhoto');

        $this->addElement('Image', 'current', array(
            'label' => 'Current Photo',
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                'viewScript' => '_formEditImagePage.tpl',
                'class'      => 'form element',
                'testing' => 'testing'
            )))
        ));

        Engine_Form::addDefaultDecorators($this->current);

        $this->addElement('File', 'Filedata', array(
            'label' => 'Choose New Photo',
            'destination' => APPLICATION_PATH.'/public/temporary/',
            'multiFile' => 1,
            'validators' => array(
                array('Count', false, 1),
                // array('Size', false, 612000),
                array('Extension', false, 'jpg,jpeg,png,gif'),
            ),
            'onchange'=>'javascript:uploadSignupPhoto();'
        ));

        $this->addElement('Hidden', 'coordinates', array(
            'filters' => array(
                'HtmlEntities',
            )
        ));

        $this->addElement('Button', 'done', array(
            'label' => 'Save Photo',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper'
            ),
            'onclick' => 'window.parent.location.reload()'
        ));


        $request = Zend_Controller_Front::getInstance()->getRequest();

        $this->addElement('Cancel', 'remove', array(
            'label' => 'delete photo',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'action' => 'remove-page-photo',
                'id' => $request->getParam('id')
            )),
            'onclick' => null,
            'class' => 'smoothbox',
            'decorators' => array(
                'ViewHelper'
            ),
        ));

        $this->addDisplayGroup(array('done', 'remove'), 'buttons');

    }
}
