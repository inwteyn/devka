<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DeleteTask.php  2012-03-15 12:12 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
  
class Updates_Form_Admin_Tasks_Delete extends Engine_Form
{
  private $type_quantity;

  public function __construct($type_quantity)
  {
    $this->type_quantity = $type_quantity;
    parent::__construct();
  }

  public function init()
  {
    $title = 'UPDATES_Delete Task Title';
    $description = 'UPDATES_Delete Task Description';

    if ($this->type_quantity == 'multiple') {
      $title = 'UPDATES_Delete Selected Tasks';
      $description = 'UPDATES_Delete Selected Tasks Description';
    }
    $this
      ->clearDecorators()
      ->setTitle($title)
      ->setDescription($description);

    $this->addElement('Button', 'delete', array (
      'label' => 'UPDATES_Delete Task',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'UPDATES_cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('delete', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}