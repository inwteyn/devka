<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_Page_Create extends Fields_Form_Standard
{
  protected $_parent_type;
  protected $_parent_id;

  public function setParent_type($value)
  {
    $this->_parent_type = $value;
  }

  public function setParent_id($value)
  {
    $this->_parent_id = $value;
  }

	public function init()
  {
  	parent::init();

    $this
    	->setTitle('PAGE_CREATE_TITLE')
    	->setDescription('PAGE_CREATE_DESC')
      ->setMethod('post')
      ->setAttrib('class', 'global_form pages_layoutbox_create_form')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'page_create', true));

    $subform = new Zend_Form_SubForm(array(
      'name' => 'extra',
      'order' => -1000000,
      'decorators' => array(
        'FormElements',
      )
    ));

    Engine_Form::enableForm($subform);

		$subform->addElement('Text', 'title', array(
      'label' => 'Title *',
      'allowEmpty' => false,
      'required' => true,
    	'order' => -4,
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

    $host_url = $_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_id' => 'pagename'), 'page_view');
		$description = sprintf( Zend_Registry::get('Zend_Translate')->_("PAGE_CREATE_URL_DESC"), $host_url);
    $subform->addElement('Text', 'url', array(
      'label' => 'URL *',
    	'required' => true,
    	'order' => -3,
      'description' => $description,
      'filters' => array(
        array('PregReplace', array('/[^a-z0-9-]/i', '-')),
        array('PregReplace', array('/-+/', '-')),
      ),
    ));

    $subform->getElement('url')->getDecorator('Description')->setOption('placement', 'append');
    $subform->getElement('url')->getDecorator('Description')->setOption('escape', false);

    $subform->addElement('Textarea', 'description', array(
      'label' => 'Description',
    	'order' => -2,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));

    $subform->addElement('Hidden', 'photo_id', array('id'=>'photo_id'));
    
    $subform->addElement('File', 'photo', array(
      'label' => 'Photo',
    	'order' => -1
    ));

    if (!isset($_FILES['photo'])){
      // ignore Zend_Validate_File_Upload::INI_SIZE
      $_FILES['photo'] = array(
        'name' => '',
        'type' => '',
        'tmp_name' => '',
        'error' => 4,
        'size' => 0
      );
    }

    $subform->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));

    $subform->tags->getDecorator("Description")->setOption("placement", "append");

    $subform->photo->addValidator('Extension', false, 'jpg,png,gif,bmp');

    $this->addSubForm($subform, $subform->getName());
  }
}
