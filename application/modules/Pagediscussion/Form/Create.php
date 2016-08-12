<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Form_Create extends Engine_Form
{

  public function init()
  {
    $this
        ->setTitle('PAGEDISCUSSION_CREATE_HEADER')
        ->setDescription('PAGEDISCUSSION_CREATE_DESCRIPTION')
        ->setAttrib('onsubmit', 'return Pagediscussion.doCreate(this);');

    $this->addElement('Text', 'title', array(
      'label' => 'PAGEDISCUSSION_CREATE_TITLE',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
      'validators' => array(
        array('StringLength', true, array(1, 64)),
      )
    ));

    $this->addElement('Textarea', 'body', array(
      'label' => 'PAGEDISCUSSION_CREATE_BODY',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $this->addElement('Checkbox', 'watch', array(
      'label' => 'PAGEDISCUSSION_NOTIFY',
      'value' => true,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'PAGEDISCUSSION_CREATE_SUBMIT',
      'ignore' => true,
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'type' => 'link',
      'link' => true,
      'decorators' => array(
        'ViewHelper',
      ),
      'onclick' => 'Pagediscussion.list();$(this).getParent("form").reset();return false;'
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');

  }

}