<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdvSearch.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Headvancedmembers_Form_MyLocation extends Engine_Form
{

  public function init()
  {

      $this->setTitle('location Settings')
          ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    //=============================================


    $this->addElement('Text', 'adres', array(
        'label' => 'Address',
        'id' => 'auto_com_adress',
        'order' => -95,
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));
    //===============================================

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => false
    ));

  }
}