<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pagemusic_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this->setTitle('PAGEMUSIC_SETTINGS_TITLE')
      ->setDescription('PAGEMUSIC_SETTINGS_DESCRIPTION');

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Text', 'ipp', array(
      'label' => 'Playlists Per Page',
      'description' => 'How many playlists will be shown per page?',
      'value' => 10
    ));

    $this->addElement('Checkbox', 'allow', array(
      'label' => 'Display music from Pages in common Music section?',
      'description' => 'Unite Music'
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}