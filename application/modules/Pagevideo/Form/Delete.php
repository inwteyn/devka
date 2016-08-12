<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Delete.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Form_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Video')
      ->setDescription('Are you sure you want to delete this video?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Video',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $this->getDisplayGroup('buttons');
  }
}