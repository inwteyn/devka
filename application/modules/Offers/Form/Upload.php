<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Upload.php 2012-07-19 17:11:12 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Upload extends Engine_Form
{
  private $offer_id;

  public function __construct($offer_id)
  {
    $this->offer_id = $offer_id;
    parent::__construct();
  }

  public function init()
  {
    // Init form
    $this
      ->setTitle('OFFERS_Add photos')
      ->setDescription('You can add photos to your offers here.')
      ->setAttrib('id','form-upload-offers');
      //->setAttrib('enctype','multipart/form-data');

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
	  Engine_Form::addDefaultDecorators($fancyUpload);

    $this->addElement('Hidden', 'fancyuploadfileids');

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Add Photos',
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
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'manage-photos', 'offer_id'=>$this->offer_id), 'offer_admin_manage', true),
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
}
