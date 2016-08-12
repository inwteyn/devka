<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 02.04.12
 * Time: 12:01
 * To change this template use File | Settings | File Templates.
 */
class Touch_Form_Admin_Settings_AppIconSet extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'EditHomeScreen');

    $this->addElement('Image', 'original', array(
      'label' => 'TOUCH_Original Image',
      'ignore' => true,
      'decorators' => array(
        array('ViewScript',
          array(
            'viewScript' => 'admin/_formAppIconSet.tpl',
            'class'      => 'form element',
          )
        )
      )
    ));
    Engine_Form::addDefaultDecorators($this->original);

    $this->addElement('Image', 'preview', array(
      'label' => 'TOUCH_Homescreen Preview',
      'ignore' => true,
      'decorators' => array(
        array('ViewScript',
          array(
            'viewScript' => 'admin/_formAppIconPreview.tpl',
            'class'      => 'form element',
          )
        )
      )
    ));
    Engine_Form::addDefaultDecorators($this->preview);
    $this->addElement('File', 'Filedata', array(
      'label' => 'TOUCH_Choose New Image',
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'validators' => array(
        array('Count', false, 1),
        // array('Size', false, 612000),
        array('Extension', false, 'jpg,jpeg,png,gif'),
      ),
      'onchange'=>'javascript:uploadHomeScreenPhoto();'
    ));
    $this->addElement('Checkbox', 'enable', array(
      'label' => 'Enable',
      'description' => 'TOUCH_Enable Homescreen?',
      'value' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('touch.homescreen.enabled', false)
    ));

    $this->addElement('Hidden', 'coordinates', array(
      'filters' => array(
        'HtmlEntities',
      )
    ));

    $this->addElement('Button', 'done', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      ),
    ));
  }
}
