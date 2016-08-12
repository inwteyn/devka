<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photos.php 2012-07-03 11:40 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Admin_Manage_Photos extends Engine_Form
{
  public function init()
  {
    $this
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
      
    $this->addElement('Radio', 'cover', array(
      'label' => 'Offer Cover',
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }
}