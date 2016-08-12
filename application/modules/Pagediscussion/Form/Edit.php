<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Form_Edit extends Engine_Form
{

  public function init()
  {
    $this
        ->setTitle('PAGEDISCUSSION_EDIT_HEADER')
        ->setDescription('PAGEDISCUSSION_EDIT_DESCRIPTION')
        ->setAttrib('onsubmit', 'return Pagediscussion.doEdit(this);');

    $this->addElement('Textarea', 'body', array(
      'label' => 'PAGEDISCUSSION_EDIT_BODY',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
    ));

    $this->addElement('Hidden', 'post_id', array(
      'filters' => array(
        'Int'
      )
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'PAGEDISCUSSION_EDIT_SUBMIT',
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
      'onclick' => 'Pagediscussion.goTopic();$(this).getParent("form").reset();return false;'
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');

  }

}
