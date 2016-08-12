<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pageevent_Form_Search extends Engine_Form
{
	public function init()
	{
    $this
      ->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box',
    ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('GET')
    ;

    parent::init();

    $this->addElement('Text', 'search', array(
      'label' => 'Search Event:'
    ));

    $this->addElement('Select', 'view', array(
      'label' => 'View:',
      'multiOptions' => array(
        '0' => 'Everyone\'s Events',
        '1' => 'My Friend\'s Events',
        '2' => 'All My Events',
        '3' => 'Only Events I Lead',
      ),
      'onchange' => '$(this).getParent("form").submit();'
    ));

    $this->addElement('Select', 'order', array(
      'label' => 'Browse By:',
      'multiOptions' => array(
        'creation_date DESC' => 'Most Recent',
        'member_count DESC' => 'Most Popular',
        'starttime ASC' => 'Start Time',
      ),
      'onchange' => '$(this).getParent("form").submit();'
    ));
  }
}
