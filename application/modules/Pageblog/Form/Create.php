<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageblog_Form_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $this->setTitle('New Blog Entry')
      ->setDescription('pageblog_NEW_BLOG_DESCRIPTION_FORM')
      ->setAttrib('id', 'page_blog_create_form')
      ->setAttrib('class', 'global_form hidden')
      ->setAttrib('name', 'blogs_create')
      ->setAttrib('onSubmit', 'return page_blog.post(this);');
      
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

    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'blog', 'auth_html');

    $upload_url = "";
    if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum')){
      $page_id = 0;
      if( Engine_Api::_()->core()->hasSubject('page') ) {
        $page_id = Engine_Api::_()->core()->getSubject()->getIdentity();
      }
      $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photos', 'page_id' => $page_id), 'pageblog', true);
    }
      $editorOptions = array(
          'upload_url' => $upload_url,
          'html' => (bool) $allowed_html,
      );
      if (!empty($upload_url))
      {
          $editorOptions['plugins'] = array(
              'table', 'fullscreen', 'media', 'preview', 'paste',
              'code', 'image', 'textcolor', 'jbimages', 'link'
          );

          $editorOptions['toolbar1'] = array(
              'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
              'media', 'image', 'jbimages', 'link', 'fullscreen',
              'preview'
          );
      }

      $this->addElement('TinyMce', 'blog_body', array(
          'disableLoadDefaultDecorators' => true,
          'required' => true,
          'allowEmpty' => false,
          'decorators' => array(
              'ViewHelper'
          ),
          'editorOptions' => $editorOptions,
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
      ));

    // Init file
    $this->addElement('FancyUpload', 'file');
    $this->file->setLabel('Main Photo');
    $fancyUpload = $this->file;
    $fancyUpload
      ->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
      'viewScript' => '_FancyUpload.tpl',
      'placement'  => '',
    ));
    Engine_Form::addDefaultDecorators($fancyUpload);

    $params = array(
      'mode' => 'exact',
	    'elements' => 'blog_body',
	    'width' => '550px',
	    'height' => '225px'
    );

    $this->getView()->getHelper('TinyMce')->setOptions($params);

    $this->addElement('Button', 'submit', array(
      'label' => 'Post Entry',
      'type' => 'submit',
      'id' => 'blog-submit'
    ));
    
    $this->getElement('submit')->removeDecorator('DivDivDivWrapper');
  }
}