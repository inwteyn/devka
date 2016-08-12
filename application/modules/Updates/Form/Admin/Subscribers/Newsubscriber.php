<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Newsubscriber.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
  
class Updates_Form_Admin_Subscribers_Newsubscriber extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form');

    $this
      ->setAttribs(array(
        'id' => 'new_subscriber_form',
        'class' => 'global_form_box',
      ));

    $add = new Zend_Form_Element_Button('add', array(
    	'label' => 'UPDATES_Add Subscriber(s)',
    	'type' => 'button',
    	'style' => 'padding-left: 15px; 
    							background-image: url(application/modules/Updates/externals/images/add.png);
    							background-repeat: no-repeat;
    							background-position:left center;'));
    $add
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag2', array('tag' => 'a', 'class'=>'smoothbox', 'href'=>'admin/updates/subscribers/add'));
      
    $upload = new Zend_Form_Element_Button('import', array(
    	'label' => 'UPDATES_Import Subscriber(s)',
    	'type' => 'button', 
    	'style' => 'padding-left: 15px; 
    							background-image: url(application/modules/Updates/externals/images/upload.png);
    							background-repeat: no-repeat;
    							background-position:left center;'));
    $upload
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag2', array('tag' => 'a', 'class'=>'smoothbox', 'href'=>'admin/updates/subscribers/import'));

    
    $this->addElements(array(
      $add,
      $upload
    ));

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}