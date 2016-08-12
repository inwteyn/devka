<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Storebundle_Form_Admin_Create extends Engine_Form
{
  var $mode = 0;

  public function __construct($mode = 0) {
    $this->mode = $mode;
    parent::__construct();
  }

  public function init()
  {
    $translate = $this->getTranslator();

    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => $translate->translate('STOREBUNDLE_Bundle Title'),
      'required' => true,
      'allowEmpty' => false,
    ));
    $this->addElement('Checkbox', 'enabled', array(
      'label' => $translate->translate('STOREBUNDLE_Enabled')
    ));
    $this->addElement('Checkbox', 'text_visibility', array(
      'label' => $translate->translate('STOREBUNDLE_Enable text')
    ));

    $this->addElement('Text', 'percent', array(
      'label' => $translate->translate('STOREBUNDLE_Percent'),
      'placeholder' => $translate->translate('STOREBUNDLE_Percent placeholder'),
      'onkeyup' => 'StorebundleCore.updatePrices();',
      'required' => true,
      'allowEmpty' => false,
    ));
    $this->addElement('Text', 'product', array(
      'label' => $translate->translate('STOREBUNDLE_Product Title'),
      'required' => true,
      'onkeyup' => 'StorebundleCore.completer($(this).value.trim());',
      'allowEmpty' => false,
    ));
    $this->addElement('hidden', 'products_ids');

    $this->addElement('Dummy', 'products_previews');

    $lang = ($this->mode) ? 'STOREBUNDLE_Edit bundle' : 'STOREBUNDLE_Create bundle';
    $onclick = ($this->mode) ? 'StorebundleCore.edit();' : 'StorebundleCore.create();';

    $this->addElement('Button', 'submit', array(
      'label' => $translate->translate($lang),
      'onclick' => $onclick
    ));

  }
}
