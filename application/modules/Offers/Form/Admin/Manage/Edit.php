<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2012-06-07 11:40 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Admin_Manage_Edit extends Engine_Form
{
  private $offer_id = 0;

  public function __construct($offer_id = 0)
  {
    $this->offer_id = $offer_id;
    parent::__construct();
  }

  public function init()
  {
    $categories = Engine_Api::_()->offers()->getCategories();

    $module_path = Engine_Api::_()->getModuleBootstrap('offers')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $this
      ->setTitle('OFFERS_Edit Offer')
      ->setDescription('OFFERS_ADMIN_EDIT_DESCRIPTION')
      ->setAttrib('id', 'form-upload-offers');

    $i = -1;
    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => 'OFFERS_Title',
      'required' => true,
      'allowEmpty' => false,
      'order' => $i++,
    ));

    // Element: item price
    $this->addElement('Text', 'item_price', array(
        'label' => 'OFFERS_Item Price',
        'value' => '00.00$',
        'order' => $i++,
        'onfocus' => 'clearInput(this)',
        'onkeyup' => 'return checkInput(this,true)'
    ));

    // Element: type
    $this->addElement('Select', 'type', array(
      'label' => 'OFFERS_Type',
      'multiOptions' => array(
        'paid' => 'OFFERS_Paid',
        'free' => 'OFFERS_Free',
        'condition' => 'OFFERS_Condition'
      ),
      'onchange' => 'changeType($(this))',
      'order' => $i++,
    ));

    $this->addElement('text', 'require', array('order' => $i++));
    $this->require->addDecorator('offersRequire', array(
      'items' => Engine_Api::_()->offers()->getRequireList()
    ));

    // Element: price
    $this->addElement('Text', 'price', array(
      'label' => 'OFFERS_Price',
      'value' => '00.00$',
      'order' => $i++,
      'onfocus' => 'clearInput(this)',
        'onkeyup' => 'return checkInput(this,true)'
    ));

    // Element: time limit
    $this->addElement('Select', 'time_limit', array(
      'label' => 'OFFERS_Time Limit',
      'multiOptions' => array(
        'unlimit' => 'OFFERS_Unlimit',
        'limit' => 'OFFERS_Limit',
      ),
      'onchange' => 'changeTimeLimit($(this))',
      'order' => $i++,
    ));

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("OFFERS_Start Time");
    //$start->setAllowEmpty(true);
    $start->setOrder($i++);
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("OFFERS_End Time");
    //$end->setAllowEmpty(true);
    $start->setOrder($i++);
    $this->addElement($end);

    // Element: Discount
    $this->addElement('Text', 'discount', array(
      'label' => 'OFFERS_Discount',
      'required' => true,
      'allowEmpty' => false,
      'order' => $i++,
        'onkeyup' => 'return checkInput(this)'
    ));

    $this->addElement('Select', 'discount_type', array(
        'multiOptions' => array(
          'percent' => '%',
          'currency' => '$',
        ),
        'order' => $i++,
    ));

    // Element: Coupons Count
    $this->addElement('Text', 'coupons_count', array(
      'label' => 'OFFERS_Coupons Count',
      //'required' => true,
      //'allowEmpty' => false,
      'order' => $i++,
        'onkeyup' => 'return checkInput(this, false)'
    ));

    $this->addElement('checkbox', 'enable_coupons_count', array(
      //'label' => 'HEBADGE_FORM_ADMIN_BADGE_LABEL_ENABLED',
      'value' => 1,
      'order' => $i++,
      'onclick' => 'enableCouponsCount($(this))',
      'decorators'=>array(
        'ViewHelper',
        array('HtmlTag3', array('tag'=>'div', 'id'=>'enable_coupons_count_container'))
      ),
    ));

    // Element: Coupons Code
    $this->addElement('Text', 'coupons_code', array(
      'label' => 'OFFERS_Coupons Code',
      'required' => true,
      'allowEmpty' => false,
      'order' => $i++,
    ));

    $this->addElement('Button', 'generate_code', array(
      'label' => 'UPDATES_Generate Code',
      'onclick' => 'generateCouponsCode()',
      'order' => $i++,
      'decorators'=>array(
        'ViewHelper',
        array('HtmlTag3', array('tag'=>'div', 'id'=>'generate_code_container'))
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

    // Element: Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'OFFERS_Description',
      'order' => $i++,
    ));

    $this->addElement('Select', 'category_id', array(
      'label' => 'OFFERS_Category',
      'order' => $i++,
      'multiOptions' => $categories,
      'description' => '<div id="editphotos-wrapper" class="form-wrapper">
                          <a href="'.$this->getView()->url(array('action'=>'manage-photos', 'offer_id' => $this->offer_id), 'offer_admin_manage', true).'"
                            class="buttonlink offer_photos_manage" >Manage Photos</a>
                        </div>'
    ));

    $this->category_id->getDecorator('Description')->setOption('escape', false);
    $this->category_id->getDecorator("Description")->setOption("placement", "APPEND");

    $this->addElement('Hidden', 'page_id', array(
      'value' => 0,
      'order' => $i++,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'href' => 'javascript:void(0);',
      'onClick' => 'history.go(-1); return false;',
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
  }
}