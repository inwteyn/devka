<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('PAGEVIDEO_SETTINGS_TITLE')
      ->setDescription('PAGEVIDEO_SETTINGS_DESCRIPTION');

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Text', 'video_ffmpeg_path', array(
      'label' => 'Path to FFMPEG',
      'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ffmpeg.path', ''),
    ));
    
    $this->addElement('Text', 'video_jobs', array(
      'label' => 'Encoding Jobs',
      'description' => 'How many jobs do you want to allow to run at the same time?',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagevideo.jobs', 2),
    ));

    $this->addElement('Text', 'video_page', array(
      'label' => 'Listings Per Page',
      'description' => 'How many videos will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagevideo.page', 10),
    ));

    $this->addElement('Checkbox', 'allow', array(
      'label' => 'Display videos from Pages in common Videos section?',
      'description' => 'Unite Videos'
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}