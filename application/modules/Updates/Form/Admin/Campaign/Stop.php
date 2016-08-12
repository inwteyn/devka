<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Module.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Form_Admin_Campaign_Stop extends Engine_Form
{
  public function init()
  {
    $this
	    ->loadDefaultDecorators();
    // Buttons

    $this->addElement('hidden', 'campaign_id', array(
      'type'=>'hidden',
    ));

    $this->addElement('Button', 'button', array(
      'label' => 'Stop sending',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('button', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->loadDefaultDecorators();
  }
}