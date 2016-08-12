<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Form_Admin_Global extends Engine_Form
{
  public function init()
  {


    $this
      ->setTitle('pagedocument_Form Global Form Title')
      ->setDescription('pagedocument_Form Global Form Description');


      $this->addElement('Text', 'pagedocument_auth_api_secret', array(
          'label' => 'pagedocument_Form Global api key label_secret',
          'description' => 'pagedocument_Form Global api key description',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagedocument.auth.api', ''),
      ));

      $this->addElement('Button', 'submit_api', array(
          'label' => 'pagedocument_Form Global submit',
          'type' => 'submit',
          'ignore' => true
      ));


    $this->addElement('Text', 'pagedocument_api_key', array(
      'label' => 'pagedocument_Form Global api key label',
      'description' => 'pagedocument_Form Global api key description',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagedocument.api.key', ''),
    ));
    
    $this->addElement('Text', 'pagedocument_secret_key', array(
      'label' => 'pagedocument_Form Global api key label_client_secret',
      'description' => 'pagedocument_Form Global api key description_client_secret',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagedocument.secret.key', ''),
    ));

    $this->addElement('Text', 'pagedocument_redirect_uri', array(
      'label' => 'pagedocument_Form Global api key label_redirect_uris',
      'description' => 'pagedocument_Form Global api key description_redirect_uris',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagedocument.redirect.uri', ''),
    ));

   
    $this->addElement('Text', 'pagedocument_page', array(
      'label' => 'pagedocument_Form Global documents label',
      'description' => 'pagedocument_Form Global documents description',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagedocument.page', 10),
    ));

    $this->addElement('Text', 'pagedocument_document_width', array(
      'label' => 'pagedocument_Form Global document width label',
      'description' => 'pagedocument_Form Global document width description',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagedocument.document.width', 800),
    ));
    $this->addElement('Text', 'pagedocument_document_height', array(
      'label' => 'pagedocument_Form Global document height label',
      'description' => 'pagedocument_Form Global document height description',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagedocument.document.height', 600),
    ));



    $this->addElement('Button', 'submit', array(
      'label' => 'pagedocument_Form Global submit',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}