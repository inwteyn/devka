<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Sendgrid.php 2012-02-17 15:52 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
  
class Updates_Form_Admin_Services_Sendgrid extends Engine_Form
{
  private $data;

  public function __construct($data)
  {
    $this->data = $data;
    parent::__construct();
  }

  public function init()
  {
    $this   //loadDefaultDecorators();
      ->clearDecorators()
      ->setTitle('UPDATES_Sendgrid Service Title')
      ->setDescription('UPDATES_Sendgrid Description');

    $this->addElement('Text', 'username', array(
      'label' => 'UPDATES_Username',
      'order' => 0,
      'required' => true,
      'value' => $this->data['username'],
      //'style' => '',
      //'class' => '',
      //'description' => 'UPDATES_some description',
    ));

    $this->addElement('Password', 'password', array(
      'label' => 'UPDATES_Password',
      'order' => 1,
      'required'=>true,
      //'value' => $this->data['password'],
      //'style' => '',
      //'class' => '',
      //'description' => 'UPDATES_some description',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'UPDATES_Save Changes',
      'type' => 'submit',
      'order' => 2,
    ));
  }
}