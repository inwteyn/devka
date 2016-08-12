<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_PageBlog_Create extends Touch_Form_Standard
{

  public function init()
  {
    $this->setTitle('New Blog Entry')
      ->setDescription('pageblog_NEW_BLOG_DESCRIPTION_FORM')
      ->setAttrib('id', 'page_blog_create_form')
      ->setAttrib('class', 'global_form hidden')
      ->setAttrib('name', 'blogs_create');

    $user = Engine_Api::_()->user()->getViewer();

		$this->addElement('Hidden', 'page_id');

    $this->addElement('Text', 'blog_title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
    )));

    // init to
    $this->addElement('Text', 'blog_tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->blog_tags->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Textarea', 'blog_body', array(
    	'class' => 'page_blog_tinymce_editor',
      'required' => true,
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper'
      ),
      'filters' => array(
        new Engine_Filter_Censor()
    	)
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Post Entry',
      'type' => 'submit',
      'id' => 'blog-submit'
    ));

  }
}