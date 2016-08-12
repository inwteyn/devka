<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagedocument
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagedocument
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Form_Edit extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $this->setTitle('pagedocument_Form Edit Form Title')
      ->setDescription('pagedocument_Form Edit Form Description')
      ->setAttrib('id', 'page_document_edit_form')
      ->setAttrib('class', 'global_form')
      ->setAttrib('name', 'documents_create')
      ->setAttrib('enctype','multipart/form-data');

    $this->addElement('Hidden', 'page_id', array('order' => -2));
    $this->addElement('Hidden', 'document_id', array('order' => -1));

    $this->addElement('Text', 'document_title', array(
      'label' => 'pagedocument_Form Edit title',
      'required' => true,
    ));

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->addElement('TinyMce', 'document_description', array(
      'label' => 'pagedocument_Form Edit description',
      'required' => false,
      'allowEmpty' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags' => ''))),
    ));

    $this->document_description->setAttrib('width', '200px');
    $this->getView()->getHelper('TinyMce')->setOptions(array('mode' => 'exact', 'elements' => 'document_description'));

    $categories = Engine_Api::_()->pagedocument()->getCategories();

    if (count($categories) != 0) {
      $categories_prepared[0]= "";
      foreach ($categories as $category) {
        $categories_prepared[$category['category_id']] = $category['category_name'];
      }

      $this->addElement('Select', 'category_id', array(
        'label' => 'pagedocument_Form Edit category',
        'required' => true,
        'multiOptions' => $categories_prepared,
      ));
    }

    $this->addElement('Text', 'document_tags', array(
      'label'=>'pagedocument_Form Edit tags',
      'autocomplete' => 'off',
      'description' => 'pagedocument_Form Edit tags description',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));

    $this->document_tags->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Button', 'submit', array(
      'label' => 'pagedocument_Form Edit submit',
      'type' => 'submit',
    ));
  }
}