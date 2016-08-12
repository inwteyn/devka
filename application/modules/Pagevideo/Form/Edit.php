<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Form_Edit extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Edit Video')
      ->setAttrib('class', 'global_form hidden')
      ->setAttrib('name', 'video_edit');
      
    $this->addElement('Text', 'video_title', array(
      'label' => 'Video Title',
      'required' => true,
      'notEmpty' => true,
      'validators' => array(
        'NotEmpty',
      ),
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '100'))
      )
    ));
    $this->video_title->getValidator('NotEmpty')->setMessage("Please specify an video title");
    $this->video_title->setAttrib('id', 'video_title_edit');
    
    // init tag
    $this->addElement('Text', 'video_tags',array(
      'label' => 'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.'
    ));
    $this->video_tags->getDecorator("Description")->setOption("placement", "append");
    $this->video_tags->setAttrib('id', 'video_tags_edit');

    $this->addElement('Textarea', 'video_description', array(
      'label' => 'Video Description',
      'rows' => 2,
      'maxlength' => '512',
      'filters' => array(
        'StripTags',
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      )
    ));
    
    $this->video_description->setAttrib('id', 'video_description_edit');
    
    $this->addElement('Button', 'video_submit', array(
      'label' => 'Save Video',
      'type' => 'submit',
    ));
  }
}
