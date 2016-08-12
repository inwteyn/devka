<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagealbum_Form_Edit extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Edit Album')
      ->setAttrib('class', 'global_form hidden')
      ->setAttrib('name', 'albums_edit');
    
    $this->addElement('Hidden', 'page_id');
    $this->addElement('Hidden', 'album');
      
    $this->addElement('Text', 'title', array(
      'label' => 'Album Title',
      'required' => true,
      'notEmpty' => true,
      'validators' => array(
        'NotEmpty',
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
        //new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      )
    ));
    $this->title->getValidator('NotEmpty')->setMessage("Please specify an album title");
    
		$this->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Textarea', 'description', array(
      'label' => 'Album Description',
      'rows' => 2,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      )
    ));

    // Submit or succumb!
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Album',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      )
    ));


    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      ),
      'onclick' => 'javascript:page_album.list();'
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
      
  }
}
