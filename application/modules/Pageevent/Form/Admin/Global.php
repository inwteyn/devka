<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pageevent_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this->setTitle('PAGEEVENT_SETTINGS_TITLE')
      ->setDescription('PAGEEVENT_SETTINGS_DESCRIPTION');

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Text', 'ipp', array(
      'label' => 'Events Per Page',
      'description' => 'How many events will be shown per page?',
      'value' => 10
    ));

    $this->addElement('Checkbox', 'allow', array(
      'label' => 'Display events from Pages in common Events section?',
      'description' => 'Unite Events'
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}