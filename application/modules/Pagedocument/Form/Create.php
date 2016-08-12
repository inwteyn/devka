<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Form_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $this->setTitle('pagedocument_Form Create Form Title')
          ->setDescription('pagedocument_Form Create Form Description')
          ->setAttrib('id', 'page_document_create_form')
          ->setAttrib('class', 'global_form')
          ->setAttrib('name', 'documents_create')
          ->setAttrib('enctype','multipart/form-data');

    $this->addElement('Hidden', 'fancyuploadfileids', array('order' => -1));
    $this->addElement('Hidden', 'page_id', array('order' => -2));

    $this->addElement('Hidden', 'file_size', array('order' => -3));
    $this->addElement('Hidden', 'file_path', array('order' => -4));
    $this->addElement('Hidden', 'file_id', array('order' => -5));

    $this->addElement('Text', 'document_title', array(
      'label' => 'pagedocument_Form Create title',
      'required' => true
    ));

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->addElement('TinyMce', 'document_description', array(
      'label' => 'pagedocument_Form Create description',
      'required' => false,
      'allowEmpty' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags' => ''))),
    ));

    $this->document_description->setAttrib('width', '200px');
    $this->getView()->getHelper('TinyMce')->setOptions(array(
      'mode' => 'exact',
      'elements' => 'document_description'
    ));

    $categories = Engine_Api::_()->pagedocument()->getCategories();
    if (count($categories) != 0) {
      $categories_prepared[0] = "";

      foreach ($categories as $category) {
        $categories_prepared[$category['category_id']] = $category['category_name'];
      }

      $this->addElement('Select', 'category_id', array(
        'label' => 'pagedocument_Form Create category',
        'required' => true,
        'multiOptions' => $categories_prepared,
      ));
    }

    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $filesize = Engine_Api::_()->authorization()->getPermission($user_level, 'page_document', 'filesize');
    $description = Zend_Registry::get('Zend_Translate')->_('pagedocument_Form Create file');
    $description = sprintf($description);
    //   $description = sprintf($description, $filesize);

    // Init file
    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->setDescription($description);
    $fancyUpload->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array('viewScript' => 'fancy_upload_document_2.tpl', 'placement'  => '')
    );

    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Text', 'document_tags',array(
      'label'=>'pagedocument_Form Create tags',
      'autocomplete' => 'off',
      'description' => 'pagedocument_Form Create tags description',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));



    $this->addElement('Button', 'submit', array(
      'label' => 'pagedocument_Form Create submit',
      'type' => 'submit',
    ));
  }
}