<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photos.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagealbum_Form_Photos extends Engine_Form
{
  public function init()
  {
    $this
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
      
    $this->addElement('Radio', 'cover', array(
      'label' => 'Album Cover',
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }
}