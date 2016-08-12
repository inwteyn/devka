<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2012-06-07 11:40 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Create extends Engine_Form
{

  public function init()
  {

    $module_path = Engine_Api::_()->getModuleBootstrap('offers')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');
    $this->addPrefixPath('Engine_Form_Element_', $module_path . '/Form/Element/', 'element');
    $categories = Engine_Api::_()->offers()->getAllCategories();

    $payment_currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

    try {
      $currencyOptions = new Zend_Currency($payment_currency, 'en_US');
      $currency = $currencyOptions->getSymbol();
    }catch (Exception $e) {
      $currency = '$';
    }

    $this
        ->setTitle('OFFERS_Create New Offer')
        ->setDescription('OFFERS_PAGEOFFERS_CREATE_DESCRIPTION')
        ->setAttrib('id', 'form-upload-offers')
        ->setAttrib('onSubmit', 'return Offers.formCreate(this);')
        ->clearDecorators();

    $i = -1;

    $this->addElement('Text', 'popup_products', array(
      'label' => 'OFFERS_form_add_products',
      'description' => 'OFFERS_form_products_desc',
      'order' => $i++,
      'decorators' => array(array('ViewScript', array(
        'viewScript' => '_hrefPopupProducts.tpl'
      )))
    ));

    $this->addElement('Text', 'title', array(
      'label' => 'OFFERS_Title',
      'required' => true,
      'allowEmpty' => false,
      'order' => $i++,
    ));

    // Element: item price
    $this->addElement('Text', 'price_item', array(
      'label' => 'OFFERS_Item Price',
      'description' => 'OFFERS_create_price_item_desc',
      'value' => '00.00',
      'order' => $i++,
      'onfocus' => 'clearInput(this)',
      'onkeyup' => 'return checkInput(this,true)',
    ));

    // Element: price offer
    $this->addElement('Text', 'price_offer', array(
      'label' => 'OFFERS_Price',
      'value' => '00.00',
      'onclick' => 'clearInput(this)',
      'order' => $i++,
      'onfocus' => 'clearInput(this)',
      'onkeyup' => 'return checkInput(this,true)'

    ));

    // Element allow credit
    if (Engine_Api::_()->offers()->isPageCreditEnabled()) {
      $this->addElement('Checkbox', 'via_credits', array(
        'label' => 'OFFERS_form_allow_credit_desc',
        'description' => 'OFFERS_form_allow_credit',
        'order' => $i++
      ));
    }

    // Element description
    $this->addElement('TinyMce', 'offer_description', array(
      'label' => 'Description',
      'class' => 'offer_description',
      'order' => $i++,
    ));

    $params = array(
      'mode' => 'exact',
      'elements' => 'blog_body',
      'width' => '550px',
      'height' => '225px'
    );

    $this->getView()->getHelper('TinyMce')->setOptions($params);

    // Element: category offer
    $this->addElement('Select', 'category_id', array(
      'label' => 'OFFERS_Category',
      'order' => $i++,
      'multiOptions' => $categories
    ));

    // Element: Discount
    $this->addElement('Text', 'discount', array(
      'label' => 'OFFERS_Discount',
      'required' => true,
      'allowEmpty' => false,
      'order' => $i++,
      'onkeyup' => 'return checkInput(this,false)'

    ));

    // Equivalent
    $this->addElement('Select', 'discount_type', array(
      'multiOptions' => array(
        'percent' => '%',
        'currency' => $currency,
      ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag3', array('tag'=>'div', 'id'=>'discount_type_container', 'style'=>'float:left; margin-left: 5px;'))
      ),
      'order' => $i++,
    ));

    $this->addElement('Checkbox', 'enable_time_left', array(
      'label' => 'OFFERS_pageoffers_form_time_limit_desc',
      'description' => 'OFFERS_form_time_limit',
      'order' => $i++,
      'checked' => '',
      'onclick' => 'changeTimeLimit(this)',
    ));

    $this->getElement('enable_time_left')->getDecorator('label')->setOption('escape', false);

    // Element: Start time
    $this->addElement('Datepicker', 'starttime', array(
      'order' => $i++,
    ));
    $this->starttime->setLabel('OFFERS_Start Time');

    // Element: End time
    $this->addElement('Datepicker', 'endtime', array(
      'order' => $i++
    ));
    $this->endtime->setLabel('OFFERS_End Time');

    $this->addElement('Checkbox', 'enable_redeem_time', array(
      'label' => 'OFFERS_pageoffers_form_redeem_time_limit_desc',
      'description' => 'OFFERS_form_redeem_time',
      'value' => 1,
      'order' => $i++,
      'checked' => '',
      'onclick' => 'changeTimeLimit(this)',
    ));

    // Element: Redeem Start time
    $this->addElement('Datepicker', 'redeem_starttime', array(
      'order' => $i++
    ));
    $this->redeem_starttime->setLabel('OFFERS_form_redeem_start_time');

    // Element: Redeem End time
    $this->addElement('Datepicker', 'redeem_endtime', array(
      'order' => $i++
    ));
    $this->redeem_endtime->setLabel('OFFERS_form_redeem_end_time');

    $this->addElement('Checkbox', 'enable_coupon_count', array(
      'description' => 'OFFERS_form_coupons_count',
      'label' => 'OFFERS_form_coupons_count_desc',
      'value' => 1,
      'order' => $i++,
      'checked' => '',
      'onclick' => 'enableCouponsCount(this)',
    ));

    // Element: Coupons Count
    $this->addElement('Text', 'coupons_count', array(
      'order' => $i++,
      'onkeyup' => 'return checkInput(this,false)'

    ));

    // Element: Coupons Code
    $this->addElement('Radio', 'type_code', array(
      'label' => 'OFFERS_form_select_type_code',
      'ored' => $i++,
      'value' => 'unique_code',
      'multiOptions' => array(
        'unique_code' => 'OFFERS_form_unique_code_for_user',
        'offer_code' => 'OFFERS_form_offer_code'
      ),
      'onClick' => 'selectTypeCode(this)'
    ));

    $this->addElement('Text', 'coupons_code', array(
      'order' => $i++,
      'style' => 'float: left'
    ));

    $this->addElement('Button', 'generate_code', array(
      'label' => 'UPDATES_Generate Code',
      'onclick' => 'generateCouponsCode()',
      'order' => $i++,
      'decorators'=>array(
        'ViewHelper',
        array('HtmlTag3', array('tag'=>'div', 'id'=>'generate_code_container'))
      ),
      'filters' => array(
        new Engine_Filter_Html(),
      ),
    ));

    $this->generate_code->addDecorator('HtmlTag3', array(
      'tag' => 'img',
      'id' => 'generateCode_loading',
      'src' => "application/modules/Offers/externals/images/loading.gif",
      'border' => "0px",
      'title' => 'Loading...',
      'placement' => 'APPEND',
    ));

    // Requires
    $this->addElement('text', 'require', array(
      'order' => $i++,
      'label' => 'OFFERS_form_require_desc',
      'description' => 'OFFERS_form_require'
    ));
    $this->require->addDecorator('offersRequire', array(
      'items' => Engine_Api::_()->offers()->getRequireList('page')
    ));

    // Init file
    $this->addElement('FancyUpload', 'file');
    $fancyUpload = $this->file;
    $fancyUpload
        ->clearDecorators()
        ->addDecorator('FormFancyUpload')
        ->addDecorator('viewScript', array(
      'viewScript' => '_FancyUpload.tpl',
      'placement'  => '',
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Post Entry',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'onClick' => 'Offers.formCancel();',
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));

    $this->addElement('Hidden', 'page_id', array(
      'value' => 0
    ));

    $this->addElement('Hidden', 'products_ids', array(
      'order' => $i++,
      'value' => 0
    ));

    $this->addElement('Hidden', 'type', array(
      'order' => $i++,
      'value' => 'free'
    ));

  }

  public function isValidRequire($post)
  {
    $valid = true;

    foreach ($this->getElement('require')->getDecorator('offersRequire')->getData() as $type => $item){
      $item['element']->setChecked($post['require'][$type]);
      if ($post['require'][$type]){
        if (!$item['form']->isValid($post)){
          $valid = false;
        }
      } else {
        // @TODO set populate values
      }
    }
    return $valid;
  }

  public function getValuesRequire()
  {
    $values = array();

    foreach ($this->getElement('require')->getDecorator('offersRequire')->getData() as $type => $item) {
      foreach ($item['form']->getValues() as $key => $value){
        if (!$item['element']->isChecked()){
          continue ;
        }

        $new_key = substr($key, 8); // cut require_
        $values[$new_key] = $value;
      }
    }
    return $values;
  }

  public function setValuesRequire($values = array())
  {
    foreach ($this->getElement('require')->getDecorator('offersRequire')->getData() as $type => $item){
      if (!empty($values[$type])){
        $item['element']->setChecked(true);
        if (is_array($values[$type])){
          $item['form']->populate($values[$type]);
        }
      }
    }
  }
}