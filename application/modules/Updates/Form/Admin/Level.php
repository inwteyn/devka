<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
  
class Updates_Form_Admin_Level extends Engine_Form
{
  public function init()
  {
    $this
    	->setTitle('UPDATES_Member Level Settings')
      ->setDescription("UPDATES_FORM_ADMIN_LEVEL_DESCRIPTION")
	    ->loadDefaultDecorators();
    	
	  $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $user_levels = $table->fetchAll($table->select());
    
    foreach ($user_levels as $user_level)
    {
      $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }
    
    $this->addElement('Select', 'level_id', array(
      'label' => 'UPDATES_Member Level',
      'multiOptions' => $levels_prepared,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));
    
    $this->addElement('Radio', 'use', array(
      'label' => 'UPDATES_Allow use Updates Plugin?',
      'description' => 'UPDATES_Do you want to let members use Updates Plugin to get updates about your Social Network?',
      'multiOptions' => array(
        0 => 'UPDATES_No, do not allow use Updates Plugin.',
        1 => 'UPDATES_Yes, allow use Updates Plugin.',
      ),
      'value' => 1,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'UPDATES_Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}