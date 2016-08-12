<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Subscribe.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Form_Subscribe extends Engine_Form
{
  public function init()
  {
    $this
      	->setTitle('UPDATES_Subscribe to updates')
      	->setDescription("UPDATES_FORM_SUBSCRIBE_DESCRIPTION");

		$this->addElement('Radio', 'subscribe', array(
      'label' => 'UPDATES_Subscribe to get updates?',
      'multiOptions' => array(
        0 => 'UPDATES_No, do not subscribe to get updates.',
        1 => 'UPDATES_Yes, subscribe to get updates.',
      ),
      'value' => 1,
    ));
    Engine_Form::addDefaultDecorators($this->subscribe);

    $this->addElement('Button', 'submit', array(
      'label' => 'UPDATES_Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}