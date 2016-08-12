<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Rename.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Form_Rename extends Engine_Form
{

  public function init()
  {
    $this
        ->setAttrib('onsubmit','return Pagediscussion.doRename(this);');

    $this->addElement('Text', 'title', array(
      'label' => 'PAGEDISCUSSION_RENAME_TITLE',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
      'validators' => array(
        array('StringLength', true, array(1, 64)),
      ),
    ));

    $this->addElement('Hidden', 'topic_id', array(
      'filters' => array('Int')
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'PAGEDISCUSSION_RENAME_SUBMIT',
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