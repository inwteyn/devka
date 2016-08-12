<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Setting.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_Form_Admin_Setting extends Engine_Form
{
	public function init()
  {
    $this
      ->setTitle('UPDATES_FORM_ADMIN_SETTINGS_TITLE')
      ->setDescription('UPDATES_FORM_ADMIN_SETTING_DESCRIPTION');
		
    $this->addElement('select', 'mode', array(
    	'Label' => 'UPDATES_Update mode:',
    	'Description' => 'UPDATES_Select update send mode.',
    	'multiOptions' => array('automatically' => 'UPDATES_Automatically send updates', 'manually'=>'UPDATES_Manually send updates'),
    	'value' => array_keys(array('automatically' => 'UPDATES_Automatically send updates', 'manually'=>'UPDATES_Manually send updates')),
      'onchange' => 'if($(this).value.trim() == "manually") {$("periodTime-wrapper").setStyle("display", "none");} else {$("periodTime-wrapper").setStyle("display", "");}',
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
    	'value' => array_keys($periods),
      'decorators' => array(
      	'ViewHelper'
     	)
    ));
    
    $this->addElement('select', 'hour', array(
    	'multiOptions' => $hours,
    	'value' => array_keys($hours),
      'decorators' => array(
      	'ViewHelper'
     	)
    ));
    
    $this->addElement('select', 'minute', array(
    	'multiOptions' => $minutes,
    	'value' => array_keys($minutes),
      'decorators' => array(
      	'ViewHelper'
     	)
    ));
    
    $this->addElement('select', 'am_pm', array(
    	'multiOptions' => $am_pm,
    	'value' => array_keys($am_pm),
      'decorators' => array(
      	'ViewHelper'
     	)
    ));
    
  	$path = Engine_Api::_()->getModuleBootstrap('updates')->getModulePath();
		$this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
    $this->addDisplayGroup(array('period', 'hour', 'minute', 'am_pm'), 'periodTime', array(
			'description'=>'UPDATES_Select period and show the time for updates recurrency sending.',
      'decorators' => array(
    		'Description',
        'FormElements',
        'DivDivDivWrapper',
    		'UpdatesTimeSelects',
      ),
    ));
    $periodTime = $this->getDisplayGroup('periodTime');
		$periodTime->addDecorator('UpdatesTimeSelects', array('legend'=>'Update Time and Period:'));

    $this->addElement('text', 'per_minute_items', array(
      'label'=>'UPDATES_Per minute items:',
      'description'=>'UPDATES_Write per minute items number',
      'required' => true,
			'trim' => true,
			'validators' => array(
        array('NotEmpty', true),
        array('Int', true),
      ),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'UPDATES_Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
  
  public function saveChanges($values)
  {
  	$settings = Engine_Api::_()->getApi('settings', 'core');
  	
  	//UPDATE MODE
  	$settings->__set('updates.update.mode', $values['mode']);
  	
  	if ($values['mode'] == 'automatically')
  	{
	    //PERIOD AND TIME
	  	$time = $values['hour'].':'.$values['minute'].':00 '.$values['am_pm'];
	  	$period = $values['period'];
	  	

      $next = new Zend_Date();
      $next->setTimezone( Engine_Api::_()->updates()->getTimezone());
	  	$next->setTime($time);

      if ($period == 'everyday')
	  	{
	  		while (Engine_Api::_()->updates()->getTimestamp() > Engine_Api::_()->updates()->getTimestamp(null, $next))
        {
          $next->addDay(1);
        }
	  	}
	  	else 
	  	{
	  		$next->setWeekday($period,'en');

	  		while (Engine_Api::_()->updates()->getTimestamp() > Engine_Api::_()->updates()->getTimestamp(null, $next))
	  		{
          $next->addWeek(1);	  			
	  		}
	  	}

	  	$settings->__set('updates.update.period', $period);
	  	$settings->__set('updates.update.time', Engine_Api::_()->updates()->getTimestamp(null, $next));
  	}

    $settings->__set('updates.perminut.itemnumber', $values['per_minute_items']);
		return true;
  }
}