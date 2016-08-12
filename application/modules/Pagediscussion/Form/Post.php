<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Post.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Form_Post extends Engine_Form
{

  public function init()
  {
    $this
        ->setTitle('PAGEDISCUSSION_POST_HEADER')
        ->setDescription('PAGEDISCUSSION_POST_DESCRIPTION')
        ->setAttrib('onsubmit', 'return Pagediscussion.doPost(this);');

    $this->addElement('Textarea', 'body', array(
      'label' => 'PAGEDISCUSSION_POST_BODY',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
    ));

    $this->addElement('Checkbox', 'watch', array(
      'label' => 'PAGEDISCUSSION_NOTIFY',
      'value' => '1',
    ));

    $this->addElement('Hidden', 'topic_id', array(
      'filters' => array(
        'Int'
      )
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'PAGEDISCUSSION_POST_SUBMIT',
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
