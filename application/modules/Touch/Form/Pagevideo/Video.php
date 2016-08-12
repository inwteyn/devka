<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Video.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Touch_Form_Pagevideo_Video extends Touch_Form_Standard
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Add New Video')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('name', 'video_create')
      ->setAttrib('enctype','multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
      //->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'album', 'controller'=>'album', 'action'=>'upload-photo', 'format' => 'json'), 'default'));
    $user = Engine_Api::_()->user()->getViewer();

    // Init name
    $this->addElement('Text', 'video_title', array(
      'label' => 'Video Title',
      'maxlength' => '100',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '100')),
      )
    ));

    // init tag
    $this->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      )
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    // Init descriptions
    $this->addElement('Textarea', 'video_description', array(
      'label' => 'Video Description',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    // Init video
    $this->addElement('Select', 'video_type', array(
      'label' => 'Video Source',
      'multiOptions' => array('0' => ' '),
      'onchange' => "updateTextFields()",
    ));

    //YouTube, Vimeo
    $video_options = Array();
    $video_options[1] = "YouTube";
    $video_options[2] = "Vimeo";

    //My Computer
    $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'upload');
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
    if( !empty($ffmpeg_path) && $allowed_upload ) {
      $video_options[3] = "My Computer";
    }
    $this->video_type->addMultiOptions($video_options);

    //ADD AUTH STUFF HERE

    // Init url
    $this->addElement('Text', 'video_url', array(
      'label' => 'Video Link (URL)',
      'description' => 'Paste the web address of the video here.',
      'maxlength' => '50'
    ));
    $this->video_url->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Hidden', 'video_code', array(
      'order' => 1
    ));
      
    $this->addElement('Hidden', 'video_id', array(
      'order' => 2
    ));
    $this->addElement('Hidden', 'video_ignore', array(
      'order' => 3
    ));

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

    $this->addElement('File', 'file', array(
      'label' => 'Upload a Video:',
      'required' => false,
      'allowEmpty' => true,
      'ignore' => true,
      'class' => 'iphone-ignore'
    ));


    // Init submit
    $this->addElement('Button', 'video_upload', array(
      'label' => 'Save Video',
      'type' => 'submit',
    ));

  }


}
