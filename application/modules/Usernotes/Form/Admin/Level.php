<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_Form_Admin_Level extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    $this->loadDefaultDecorators();

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);

    foreach ($user_levels as $user_level) {
      $levels_prepared[$user_level->level_id]= $user_level->title;
    }
    
    // category field
    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levels_prepared,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));
    
    $this->addElement('Radio', 'enabled', array(
      'label' => 'Allow User Notes',
      'description' => 'Allow this user level to leave notes about users?',
      'multiOptions' => array(1 => 'Yes, allow', 0 => 'No, not allow'),
      'value' => 0,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}