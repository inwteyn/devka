<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Upload.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_Classified_Photo_Upload extends Touch_Form_Standard
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Add New Photos')
      ->setDescription('Choose photos on your computer to add to this album. (2MB maximum)')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('class', 'global_form group_form_upload')
      ->setAttrib('name', 'albums_create')
      ->setAttrib('enctype','multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    $this->setAction($this->getView()->url());

    if (!isset($_FILES['file'])){
      // ignore Zend_Validate_File_Upload::INI_SIZE
      $_FILES['file'] = array(
        'name' => '',
        'type' => '',
        'tmp_name' => '',
        'error' => 4,
        'size' => 0
      );
    }

    // Init file
    $this->addElement('File', 'file', array(
			'label' => 'Photo',
		));
		$this->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');

		$this->addElement('hidden', 'photos');
    
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Photos',
      'type' => 'submit',
    ));
  }
}