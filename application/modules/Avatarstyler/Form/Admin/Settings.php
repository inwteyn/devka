<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Settings.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Avatarstyler_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Avatarstyler_SETTINGS_FORM_TITLE');
    $this->setDescription('Avatarstyler_SETTINGS_FORM_DESCRIPTION');
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $currentPhoto = $settings->getSetting('avatarstyler.current.photo.id', 0);


//    ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
      $this->addElement('Select', 'usage', array(
      'label' => 'Avatarstyler_usage',
      'description' => 'AVATARSTYLER_USAGE_ELEMENT_DESCRIPTION',
      'multiOptions' => array(
        'allow' => 'Avatarstyler_allow',
        'disallow' => 'Avatarstyler_deny',
      ),
      'value' => $settings->getSetting('avatarstyler.usage', 'allow')
    ));
    $this->setDefaults(array(
      'usage' => $settings->getSetting('avatarstyler.usage', 'allow')
    ));
    $this->usage->getDecorator('Description')->setOptions(array('placement' => 'append'));

//      ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//      $this->addElement('Image', 'current', array(
//          'label' => 'Available avatars',
//          'ignore' => false,
//          'onclick' => 'return false;',
//      ));

      $this->addElement('File', 'Filedata', array(
          'label' => 'Add   more  photo',
          'destination' => APPLICATION_PATH.'/public/temporary/',
          'multiFile' => 0,
          'validators' => array(
              array('Count', false, 1),
              array('Extension', false, 'jpg,jpeg,png,gif'),
          )

      ));
      $this->addElement('Button', 'execute', array(
          'label' => 'Save Settings',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array(
              'ViewHelper'),
      ));
//    if($curren//tPhoto) {
//      $this->addElement('Cancel', 'remove//', array(
//        'label' => 'remov//e photo',
//        'link'// => true,
//        'prependText' =//> ' or ',
//        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemb//le(array(
//            'module' => 'avata//rstyler',
//            'controller' =>// 'index',
//            'action' => 'remov//e-photo',
//          ), 'admin_defa//ult', 1),
//        'onclick'// => null,
//        'decorators' //=> array(
//          'Vi//ewHelper'
// //       ),
////      ));
//      $this->addDisplayGroup(array('execute', 'remove'), 'b//uttons');
//    }
//
  }
}