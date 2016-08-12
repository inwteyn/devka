<?php

/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 03.03.2015
 * Time: 14:36
 */
class Heemoticon_Form_Admin_Newcollection extends Engine_Form
{
  public function init()
  {
    $creditModuleStatus = $this->getCreditModuleStatus();
    $this->addElement('Text', 'name', array(
      'label' => 'HE-Emoticon Name',
      'required' => true,
      'allowEmpty' => false,
      'attribs' => array(
        'required' => 'required',
      )
    ));

    $this->addElement('Text', 'author', array(
      'label' => 'HE-Emoticon Author'
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'HE-Emoticon Description',
    ));

    $price_params = array(
      'label' => 'HE-Emoticon Price',
      'placeholder' => 'e.g 120 credits',
      'onkeyup' => 'return checkInput(this,true)',
      'description' => ''
    );

    if (!$creditModuleStatus) {
      $price_params['disabled'] = 1;
    }

    $this->addElement('Text', 'price', $price_params);

    if (!$creditModuleStatus) {
      $description = $this->getTranslator()->translate('HE-Emoticon_CREDIT_MODULE_STATUS');
      $this->price->loadDefaultDecorators();
      $this->price->setDescription($description);
      $this->price->getDecorator('Description')->setOption('escape', false);
    }

    // Init file
    $this->addElement('FancyUpload', 'file');
    $fancyUpload = $this->file;
    $fancyUpload
      ->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
        'viewScript' => '_FancyUpload.tpl',
        'placement' => '',
      ));

    $levels = Engine_Api::_()->getItemTable('authorization_level')->fetchAll();

    if ($levels) {
      foreach ($levels as $level) {
        $this->addElement('checkbox', 'level_' . $level->getIdentity(), array(
          'checked' => 'checked',
          'label' => $level->getTitle(),
        ));
      }
    }


    $order = new Zend_Form_Element_Hidden('order');

    $this->addElements(array(
      $order
    ));

    $this->addElement('hidden', 'cover', array());

    // Add submit button
    $this->addElement('Button', 'add_collection_submit_btn', array(
      'label' => 'HE-Emoticon Add Collection',
      'type' => 'submit'
    ));
  }

  /**
   * @return bool
   */
  private function getCreditModuleStatus()
  {
    $enabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('credit');
    if ($enabled) {
      $table = Engine_Api::_()->getDbTable('modules', 'core');
      $select = $table->select()->where('name = ?', 'credit');
      $row = $table->fetchRow($select);
      $version = $row->version;
      if (version_compare($version, '4.3.1') < 0) {
        return false;
      } else return true;
    } else {
      return false;
    }
  }
}