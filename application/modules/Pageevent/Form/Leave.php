<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Leave.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pageevent_Form_Leave extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Leave Event')
      ->setDescription('Are you sure you want to leave this event?')
      ->setMethod('POST')
      ->setAction($_SERVER['REQUEST_URI'])
      ->setAttrib('class', 'global_form_popup')
    ;


    $this->addElement('Button', 'submit', array(
      'label' => 'Leave Event',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons');
  }
}