<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Favorite.php 2012-09-27 12:40 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Offers_Form_Favorite extends Engine_Form
{
  private $favorite_status;

  public function __construct($favorite_status)
  {
    $this->favorite_status = $favorite_status;
    parent::__construct();
  }
  public function init()
  {
    $description = 'OFFERS_FAVORITE_DESCRIPTION';
    $type_button = 'submit';
    $class = '';
    $buttonLabel = 'OFFERS_FAVORITE_BTN_Make as Favorite';
    if ($this->favorite_status == 'active') {
      $description = 'OFFERS_FAVORITE_DESCRIPTION_ACTIVE';
      $buttonLabel = 'OFFERS_FAVORITE_BTN_Make as Simple';
    }

    $this->setTitle('OFFERS_FAVORITE_TITLE')
      ->setDescription($description)
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');

    $this->addElement('Button', 'submit', array(
      'label' => $buttonLabel,
      'type' => 'submit',
      //'class' => $class,
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'parentText' => 'or',
      'href' => '',
      'onClick' => 'parent.Smoothbox.close()',
      'decorators' => array('ViewHelper')
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}