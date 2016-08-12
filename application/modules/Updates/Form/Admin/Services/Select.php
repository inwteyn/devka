<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Services.php 2012-02-14 15:27 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
  
class Updates_Form_Admin_Services_Select extends Engine_Form
{
  public function init()
  {
  	$this
      ->clearDecorators()
      ->setTitle('UPDATES_Mail Services')
      ->setDescription('UPDATES_FORM_ADMIN_SERVICES_DESCRIPTION');

    $services = array(
      'socialengine' => 'UPDATES_SocialEngine',
      'mailchimp' => 'UPDATES_MailChimp',
      'sendgrid' => 'UPDATES_SendGrid');

    $this->addElement('select', 'services', array(
      'label' => 'UPDATES_Choose mail service',
      'multiOptions' => $services,
      'order' => 0,
      'onchange' => "changeService()",
      //'style' => '',
      //'class' => '',
      ));

    $this->getElement('services')->getDecorator('label')->setOption('class','services_label');

  }
}