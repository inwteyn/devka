<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ExportMembers.php  2012-04-17 14:50 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
  
class Updates_Form_Admin_Services_ExportMembers extends Engine_Form
{
  private $listName = array();

  public function __construct($listName)
  {
    $this->listName = $listName;
    parent::__construct();
  }

  public function init()
  {
    $this
      ->clearDecorators()
      ->setTitle('UPDATES_Export Members Title');

    $this->addElement('Button', 'export_members', array (
      'label' => 'UPDATES_Export Members',
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
    $this->addDisplayGroup(array('export_members', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}