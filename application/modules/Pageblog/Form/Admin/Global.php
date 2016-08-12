<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pageblog_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this->setTitle('PAGEBLOGS_SETTINGS_TITLE')
      ->setDescription('PAGEBLOGS_SETTINGS_DESCRIPTION')
    ;

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Text', 'ipp', array(
      'label' => 'Blogs Per Page',
      'description' => 'How many blogs will be shown per page?',
      'value' => 10
    ));

    $this->addElement('Checkbox', 'allow', array(
      'label' => 'Display Blogs from Pages in common Blogs section?',
      'description' => 'Unite Blogs'
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}