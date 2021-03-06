<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Form_Admin_Products_Edit extends Engine_Form
{

  protected $_item;

  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }

  public function init()
  {

    /**
     * @var $settings Core_Model_DbTable_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $href = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'store', 'controller' => 'products'), 'admin_default', true);
    // Init form

    $this
      ///->setTitle('STORE_Edit Product')
      //->setDescription('You can add edit product here, or <a href="' . $href . '">back</a> to the list of products.')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('id', 'form-upload');


    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => 'STORE_Product Title',
      'required' => true,
      'allowEmpty' => false,
    ));

    // Element product_id
    $this->addElement('Hidden', 'product_id', array());

    // Element tags
    $this->addElement('Text', 'tags', array(
      'label' => 'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    // Element type
    $this->addElement('Select', 'type', array(
      'label' => 'STORE_Product Type',
      'description' => 'STORE_PRODUCT_TYPE_DESCRIPTION',
      'required' => true,
      'disabled' => true,
      'multiOptions' => array(
        'simple' => 'STORE_Tangible',
        'digital' => 'STORE_Digital',
      ),
      "onchange" => "switchAmount()"
    ));
    $this->type->getDecorator('Description')->setOptions(array('placement' => 'append'));

    // Element: quantity
    $this->addElement('Text', 'quantity', array(
      'label' => 'STORE_Quantity',
      'description' => 'STORE_Amount of product for sell',
      'required' => true,
      'allowEmpty' => false,
      'value' => 1,
      'validators' => array(
        array('Digits'),
      )
    ));
    $this->quantity->getValidator('Digits')->setMessage('STORE_Please enter a valid digits.', 'digitsInvalid');
    $this->quantity->getDecorator("Description")->setOption("placement", "append");

    // Element price_type
    $this->addElement('Select', 'price_type', array(
      'label' => 'STORE_Price Type',
      'description' => 'STORE_PRICE_TYPE_DESCRIPTION',
      'multiOptions' => array(
        'simple' => 'STORE_Simple',
        'discount' => 'STORE_Discount'
      ),
      "onchange" => "switchType()"
    ));
    $this->price_type->getDecorator('Description')->setOptions(array('placement' => 'append'));

    // Element: price
    $this->addElement('Text', 'price', array(
      'label' => 'STORE_Product Price',
      'required' => 0,
      'allowEmpty' => 1,
      'validators' => array(
      ),
      'value' => $settings->getSetting('store.minimum.price', 0.15),
    ));
    $this->price->addFilter('StripTags')
      ->addFilter('StringTrim')
      ->addFilter('pregReplace', array('match' => '/\s+/', 'replace' => ''))
      ->addFilter('LocalizedToNormalized')
      ->addValidator('stringLength', true, array(1, 12))
      ->addValidator('float', true, array('locale' => 'en_US'))
      ->addValidator('greaterThan', true, array('min' => 0))
      ->addValidator(new Engine_Validate_AtLeast($settings->getSetting('store.minimum.price', 0.15)));


    // Element: list_price
    $this->addElement('Text', 'list_price', array(
      'label' => 'STORE_Product List Price',
      'description' => 'STORE_LIST_PRICE_DESCRIPTION',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('float', true, array('locale' => 'en_US')),
        new Engine_Validate_AtLeast($settings->getSetting('store.minimum.price', 0.15)),
      ),
      'value' => (float)($settings->getSetting('store.minimum.price', 0.15) + 0.01),
    ));

    // Discount expiry date
    $discount_expiry_date = new Engine_Form_Element_CalendarDateTime('discount_expiry_date');
    $discount_expiry_date->setLabel("Discount expiry date");
    $discount_expiry_date->setAllowEmpty(true);
    $discount_expiry_date->setDescription('STORE_DISCOUNT_EXPIRY_DATE_DESC');
    $this->addElement($discount_expiry_date);
    $discount_expiry_date->getDecorator('Description')->setOption('escape', false);

    if (Engine_Api::_()->store()->isCreditEnabled()) {
      $this->addElement('Checkbox', 'via_credits', array(
        'label' => 'Selling with Credits',
        'description' => 'STORE_Select checkbox if you want to sell product with credits, but this doesn\'t mean that you cannot sell with default currency ($ etc), they will work together',
      ));
    }

    $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
    ));

    $params = array(
      'mode' => 'exact',
      'elements' => 'description',
      'width' => '660px',
      'height' => '240px',
      'theme_advanced_buttons1' => array(
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|',
        'bullist', 'numlist', '|',
        'undo', 'redo', '|',
        'sub', 'sup', '|',
        'forecolor', 'forecolorpicker', 'backcolor', 'backcolorpicker', '|'
      ),
      'theme_advanced_buttons2' => array(
        'newdocument', 'code', 'image', 'media', 'preview', 'fullscreen', '|',
        'link', 'unlink', 'anchor', 'charmap', 'cleanup', 'hr', 'removeformat', 'blockquote', 'separator', 'outdent', 'indent', '|',
        'selectall', 'advimage'),
      'theme_advanced_buttons3' => array('formatselect', 'fontselect', 'fontsizeselect', 'styleselectchar', '|', 'table', '|'),
    );

    $this->getView()->getHelper('TinyMce')->setOptions($params);

    // Add subforms
    if (!$this->_item) {
      $customFields = new Store_Form_Admin_Custom_Fields();
    } else {
      $customFields = new Store_Form_Admin_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if (get_class($this) == 'Store_Form_Admin_Products_Edit') {
      $customFields->setIsCreation(true);
    }

    $customFields->removeElement('submit');

    $this->addSubForms(array(
      'fields' => $customFields
    ));

    /**
     * @var $category Fields_Form_Element_ProfileType
     * @var $multiOptions Array
     */
    $category = $this->getSubForm('fields')->getElement('0_0_1');
    $multiOptions = $category->getMultiOptions();
    foreach ($multiOptions as $key => $value) {
      if ($key == '') unset($multiOptions[$key]);
    }
    $category->setMultiOptions($multiOptions);

    /**
     * @var $table Store_Model_DbTable_Taxes
     */
    $table = Engine_Api::_()->getDbTable('taxes', 'store');
    $taxes = $table->getTaxesArray();

    // Element type
    $this->addElement('Select', 'tax_id', array(
      'label' => 'Tax',
      'description' => 'STORE_TAX_DESCRIPTION',
      'multiOptions' => $taxes
    ));
    $this->tax_id->getDecorator('Description')->setOptions(array('placement' => 'append'));

    // Element: additional_params
    $path = Engine_Api::_()->getModuleBootstrap('store')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');

    $additionalParams = new Store_Form_Element_AdditionalParams('additional_params', array(
      'label' => 'Additional Params',
      'description' => 'STORE_ADDITIONAL_PARAMS_DESCRIPTION'
    ));
    $additionalParams->clearDecorators()
      ->addDecorator('FormAdditionalParams')
      ->addDecorator('viewScript', array(
        'viewScript' => '_AdditionalParams.tpl',
        'placement' => '',
      ));
    Engine_Form::addDefaultDecorators($additionalParams);
    $this->addElement($additionalParams);
    $additionalParams->getDecorator('Description')->setOption('escape', false);

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'store', 'controller' => 'products'), 'admin_default', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }

  public function isValid($data)
  {
    /**
     * @var $atLeast Engine_Validate_AtLeast
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $allowFree = $settings->getSetting('store.free.products', 0);

    if ($data['price_type'] == 'discount') {
      $element = $this->getElement('list_price');
      $element->setAllowEmpty(0);
      $element->setRequired(1);
      $element->addValidator(new Engine_Validate_AtLeast($settings->getSetting('store.minimum.price', 0.15)));

      $atLeast = $element->getValidator('AtLeast');
      $minimum_price = (float)($data['price'] + 0.01);
      $atLeast->setMin($minimum_price);
    }

    if ($data['price_type'] == 'simple') {
      $el = $this->getElement('list_price');
      $el->clearValidators();
      $el->addValidator(new Engine_Validate_AtLeast(0));
    }

    if ($data['type'] == 'digital' && $allowFree) {
      $element = $this->getElement('price');
      $element->setAllowEmpty(1);
      $element->clearValidators();
      $element->addValidator('float', true, array('locale' => 'en_US'));
      $element->addValidator(new Engine_Validate_AtLeast(0));
    }

    return parent::isValid($data);
  }
}
