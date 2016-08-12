<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexControlle.php 2015-10-06 16:58:20  $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedmembers_Form_Admin_General extends Engine_Form
{
    public function init()
    {
        //$this->setTitle('Global settings');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->addElement('Radio', 'view', array(
          'label' => 'View Mode',
          'class' => 'headvancedmembers_admin_radio',
          'multiOptions' => array("0"=>"Grid", "1"=>"Circles","2"=>"Map"),
          'value' => $settings->getSetting('headvancedmembers.mode', 0),
        ));

        $this->addElement('Radio', 'verification', array(
          'label' => 'Allow member verification',
          'class' => 'headvancedmembers_admin_radio',
          'multiOptions' => array("0"=>"Yes", "1"=>"No"),
          'value' => $settings->getSetting('headvancedmembers.verification', 0),
        ));
        $this->addElement('Button', 'save', array(
          'label' => 'Save',
          'type' => 'submit'
        ));
    }
}