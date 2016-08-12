<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Category.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Form_Admin_DeleteCategory extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('pagedocument_Form Categories Form Title')
      ->setDescription('pagedocument_Form Delete Category Description')
      ->setMethod('post')
      ->setAttrib('class', 'global_form_popup');

    $this->addElement(new Zend_Form_Element_Hidden('confirm'));

    $this->addElement('Button', 'submit', array(
      'label' => 'pagedocument_Form Categories delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
  
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'pagedocument_Form Categories cancel',
      'link' => true,
      'prependText' => ' or ',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array('ViewHelper')
    ));
  }
}
