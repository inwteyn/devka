<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pagealbum_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this->setTitle('PAGEALBUMS_SETTINGS_TITLE')
      ->setDescription('PAGEALBUMS_SETTINGS_DESCRIPTION');

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Text', 'ipp', array(
      'label' => 'Albums Per Page',
      'description' => 'How many albums will be shown per page?',
      'value' => 10
    ));

    $this->addElement('Checkbox', 'allow', array(
      'label' => 'Display albums from Pages in common Albums section?',
      'description' => 'Unite Albums'
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}