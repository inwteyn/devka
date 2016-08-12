<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SyncSetting.php 2012-04-06 10:12 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Form_Admin_Services_SyncSettings extends Engine_Form
{
	public function init()
  {
    $this
      ->setTitle('UPDATES_FORM_ADMIN_SYNC_SETTINGS_TITLE')
      ->setDescription('UPDATES_FORM_ADMIN_SYNC_SETTING_DESCRIPTION');

    $i = -1;
		
    $this->addElement('select', 'mode', array(
    	'Label' => 'UPDATES_Synchronization mode:',
      'order' => $i++,
    	'Description' => 'UPDATES_Select synchronization mode.',
    	'multiOptions' => array('automatically' => 'UPDATES_Automatically synchronization', 'manually'=>'UPDATES_Manually synchronization'),
      'onchange' => 'changeSyncMode()',
    ));

    $periods = array('everyday' => 'UPDATES_Every day', 'Mon' => 'UPDATES_Monday',
    								 'Tue' => 'UPDATES_Tuesday','Wed' => 'UPDATES_Wednesday', 
    								 'Thu' => 'UPDATES_Thursday', 'Fri' => 'UPDATES_Friday', 
    								 'Sat' => 'UPDATES_Saturday', 'Sun' => 'UPDATES_Sunday');
    
    $calendar = new Engine_Form_Element_CalendarDateTime('starttime');
    $hours = array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 11=>11, 12=>12);
    $minutes = array('00'=>'00', 10=>10, 20=>20, 30=>30, 40=>40, 50=>50);
    $am_pm = array('AM'=>'AM', 'PM'=>'PM');
    
    unset($hours['']);
    unset($minutes['']);
    unset($am_pm['']);
    
    $this->addElement('select', 'period', array(
    	'multiOptions' => $periods,
      'order' => $i++,
    	'value' => array_keys($periods),
      'onchange' => 'changeSyncTime()',
      'decorators' => array(
      	'ViewHelper'
     	)
    ));
    
    $this->addElement('select', 'hour', array(
    	'multiOptions' => $hours,
    	'value' => array_keys($hours),
      'order' => $i++,
      'onchange' => 'changeSyncTime()',
      'decorators' => array(
      	'ViewHelper'
     	)
    ));
    
    $this->addElement('select', 'minute', array(
    	'multiOptions' => $minutes,
    	'value' => array_keys($minutes),
      'order' => $i++,
      'onchange' => 'changeSyncTime()',
      'decorators' => array(
      	'ViewHelper'
     	)
    ));
    
    $this->addElement('select', 'am_pm', array(
    	'multiOptions' => $am_pm,
    	'value' => array_keys($am_pm),
      'order' => $i++,
      'onchange' => 'changeSyncTime()',
      'decorators' => array(
      	'ViewHelper'
     	)
    ));
    
    $this->addDisplayGroup(array('period', 'hour', 'minute', 'am_pm'), 'periodTime', array(
			'description' => 'UPDATES_Select period and show the time for synchronisation.',
      'decorators' => array(
    		'Description',
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));

    $this->addElement('Button', 'synchronise', array(
      'label' => 'UPDATES_Synchronise',
      //'order' => $i++,
      'onclick' => 'doSynchronise()',
      //'style' => 'display: none',
    ));

    $this->synchronise->addDecorator('HtmlTag3', array(
      'tag' => 'img',
      'id' => 'synchronise_loading',
      'order' => $i++,
      'placement' => 'APPEND',
      'src' => "application/modules/Updates/externals/images/loading.gif",
      'border' => "0px",
      'title' => 'Loading...',
      'style' => "display: none; margin-left: 14px",
    ));

    $this->addElement('hidden', 'hidden_sync_ok_icon', array(
      'order' => $i++,
    ));

    $this->hidden_sync_ok_icon->addDecorator('HtmlTag3', array(
      'tag' => 'img',
      'class' => 'sync_ok_icon',
      'id' => 'sync_ok_icon',
      'order' => $i++,
      'placement' => 'APPEND',
      'src' => "application/modules/Updates/externals/images/ok.png",
      'alt' => "Successfully saved",
      'title' => 'UPDATES_Successfully saved',
    ));

    $this->addElement('Button', 'save', array(
      'label' => 'UPDATES_Save Changes',
      'order' => $i++,
      //'type' => 'submit',
      'onclick' => 'saveSync();',
    ));

    $this->save->addDecorator('HtmlTag3', array(
      'tag' => 'img',
      'id' => 'save_sync_loading',
      'order' => $i++,
      'placement' => 'APPEND',
      'src' => "application/modules/Updates/externals/images/loading.gif",
      'border' => "0px",
      'title' => 'Loading...',
      'style' => "display: none; margin-left: 14px",
    ));
  }
}