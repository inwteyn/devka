<?php

class Pagedocument_Form_Api extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('pagedocument_Form Global Form Title Api')
      ->setDescription('');


      $this->addElement('Text', 'pagedocument_auth_api', array(
          'label' => 'pagedocument_Form Global api key label',
          'description' => 'pagedocument_Form Global api key description',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pagedocument.auth.api', ''),
      ));

      $this->addElement('Button', 'submit_api', array(
          'label' => 'pagedocument_Form Global submit',
          'type' => 'submit',
          'ignore' => true
      ));
  }
}