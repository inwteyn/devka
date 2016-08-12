<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DeleteSelected.php 2012-03-23 13:57 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Form_Admin_Subscribers_DeleteSelected extends Engine_Form
{
  public function init()
  {
    $this   //loadDefaultDecorators();
      //->clearDecorators()
      ->setTitle('UPDATES_Delete Selected Subscribers')
      ->setDescription('UPDATES_Are you sure you want to delete selected subscribers?');

    $this->addElement('Button', 'delete', array (
      'label' => 'UPDATES_Delete Subscribers',
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