<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Import.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Form_Admin_Subscribers_Import extends Engine_Form
{
  public function init()
  {
  	$path = Engine_Api::_()->getModuleBootstrap('updates')->getModulePath();
		$this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
		
		$this
      ->clearDecorators()
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'ImportCSV')
      ->setTitle('UPDATES_Imort Subscriber(s) from CSV file')
      ->setDescription('UPDATES_ADMIN_IMPORT_SUBSCRIBERS_DESCRIPTION');

		$this->addElement('File', 'csvfile', array(
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'validators' => array(
        array('Count', false, 1),
        // array('Size', false, 612000),
        array('Extension', false, 'csv'),
      ),
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'UPDATES_Import Subscriber(s)',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'UPDATES_cancel',
      'link' => true,
      'prependText' => ' or ',
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
  
  function uploadcsv() 
  {
  	$emailForm = new Updates_Form_Widgets_Subscribe();
  	$email_box = $emailForm->getElement('updates_email_box');
    
  	$contacts = array();
      
    $uploaded_file = $_FILES['csvfile']['tmp_name'];

    if(is_uploaded_file($uploaded_file)) {
      $fh = fopen($uploaded_file, "r");
      while( ($row = fgetcsv($fh, 1024, ',')) != false ) {
        foreach($row as $value) {
          $value = strtolower(trim($value));
          $value = explode('<',str_replace('>','', $value));
          if($email_box->isValid(trim($value[1]))) 
          {
            $contacts[trim($value[1])] = trim($value[0]);
          }
        }
      }
      fclose($fh);
    }
    
    if(empty($contacts)) {
      return array();
    }
    
    return $contacts;
  }
}