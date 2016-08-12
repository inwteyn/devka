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
  
class Updates_Form_Admin_Services_Mailchimp extends Engine_Form
{
  private $data;

  public function __construct($data)
  {
    $this->data = $data;
    parent::__construct();
  }

  public function init()
  {
    $this   //loadDefaultDecorators();
      ->clearDecorators()
      ->setTitle('UPDATES_Mailchimp Service Title')
      ->setDescription('UPDATES_Mailchimp Description');

    $i = -1;
    $listName = '';
    if ($this->data['list_name'] == '') {
      $listName = $_SERVER['HTTP_HOST'].'-'. mt_rand(999,9999);
    }
    else {
      $listName = $this->data['list_name'];
    }

    $this->addElement('text', 'list_name', array(
      'label' => 'UPDATES_List name',
      'order' => $i++,
      'required' => true,
      'value' => $listName,
      'attribs' => array('readonly' => 'readonly'),
    ));

    $this->list_name->addDecorator('HtmlTag3', array(
      'tag' => 'p',
      'id' => 'list_name_description',
      'order' => $i++,
      'placement' => 'APPEND',
    ));

    $this->addElement('text', 'api_key', array(
      'label' => 'UPDATES_Api key',
      'order' => $i++,
      'required'=>true,
      'value' => $this->data['api_key'],
    ));

    $this->addElement('text', 'title', array(
      'label' => 'UPDATES_MailChimp campaign title',
      'order' => $i++,
      'required'=>true,
      'value' => $this->data['title'],
    ));

    $this->addElement('text', 'subject', array(
      'label' => 'UPDATES_Subject:',
      'order' => $i++,
      'required'=>true,
      'value' => $this->data['subject'],
    ));

    $this->addElement('text', 'from_email', array(
      'label' => 'UPDATES_From email',
      'order' => $i++,
      'required'=>true,
      'value' => $this->data['from_email'],
    ));

    $this->addElement('text', 'from_name', array(
      'label' => 'UPDATES_From name',
      'order' => $i++,
      'required'=>true,
      'value' => $this->data['from_name'],
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'UPDATES_Save Changes',
      'type' => 'submit',
      'order' => $i++,
    ));
  }
}