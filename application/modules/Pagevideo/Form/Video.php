<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Video.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Form_Video extends Engine_Form
{

  public function init()
  {
    // Init form
    $this
      ->setTitle('Add New Video')
      ->setDescription('pagevideo_NEW_VIDEO_DESCRIPTION_FORM')
      ->setAttrib('id', 'form-video-upload')
      ->setAttrib('name', 'video_create')
      ->setAttrib('class', 'global_form hidden')
      ->setAttrib('enctype','multipart/form-data');

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
    $this->addElement('Text', 'video_tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      )
    ));
    $this->video_tags->getDecorator("Description")->setOption("placement", "append");

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
      'onchange' => "updateVideoFields()",
    ));

    //YouTube, Vimeo
    $video_options = Array();
    $video_options[1] = "YouTube";
    $video_options[2] = "Vimeo";

    //My Computer
//    $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'upload'); // @todo maybe use our own settings
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
    if( (!empty($ffmpeg_path) ) ){
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


    $fancyUpload = new Engine_Form_Element_FancyUpload('video_file');
    $fancyUpload->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
      'viewScript' => '_FancyUpload.tpl',
      'placement'  => '',
    ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    // Init submit
    $this->addElement('Button', 'video_upload', array(
      'label' => 'Save Video',
      'type' => 'submit',
    ));
  }

}
