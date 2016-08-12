<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Settings.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class SocialBoost_Form_Admin_Rewards extends Engine_Form
{
  public function init()
  {
    $this->setTitle('SOCIALBOOST_REWARDS_FORM_TITLE');
    $this->setDescription('SOCIALBOOST_REWARDS_FORM_DESCRIPTION');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $modules = Engine_Api::_()->getDbTable('modules', 'core');

    $this->addElement('Checkbox', 'reward', array(
      'label' => 'If you have not selected any rewarding then it displays only a message above the sharing options, something like this: Love us? Share your love',
      'description' => 'One member can receive reward only one time',
      'checkedValue' => 1,
      'uncheckedValue' => 0,
      'value' => $settings->getSetting('socialboost.admin.reward', 1),
    ));

    $this->addElement('Checkbox', 'credit', array(
      'label' => '',
      'description' => 'Give credits',
      'checkedValue' => 1,
      'uncheckedValue' => 0,
      'value' => $settings->getSetting('socialboost.admin.credit', 0),
      'onClick' => 'changeCredit(this);',
    ));
    if (!$modules->isModuleEnabled('credit')) {
      $this->credit->setLabel("This option requires <a href='http://www.hire-experts.com/social-engine/credits-plugin' target='_blank'>Credits plugin</a>");
      $this->credit->setAttrib('disabled', true);
      $this->credit->getDecorator('label')->setOption('escape', false);
      $this->credit->setValue(0);
    }

    $this->addElement('Text', 'credit_amount', array(
      'Label' => 'Credit Amount',
      'validators' => array(
        array('Int', true),
        array('Between', true, array(0, 1000, true)),
      ),
      'value' => $settings->getSetting('socialboost.credit.amount', 5),
    ));

    $this->addElement('Checkbox', 'offers', array(
      'label' => '',
      'description' => 'Unlock Offers',
      'checkedValue' => 1,
      'uncheckedValue' => 0,
      'value' => $settings->getSetting('socialboost.admin.offers', 1),
      'onClick' => 'changeOffer(this);',
    ));
    if (!$modules->isModuleEnabled('offers')) {
      $this->offers->setLabel("This option requires <a href='http://www.hire-experts.com/social-engine/offers-coupons-plugin' target='_blank'>Offers & Coupons plugin</a>");
      $this->offers->setAttrib('disabled', true);
      $this->offers->getDecorator('label')->setOption('escape', false);
      $this->offers->setValue(0);
    } else {
      $removeHref = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'social-boost', 'controller' => 'index', 'action' => 'clear-offer'), 'admin_default', true);

      $offer_id = $settings->getSetting('socialboost.offer.id', 0);
      $offer = Engine_Api::_()->getItem('offer', $offer_id);

      $lang = ($offer) ? 'another offer' : 'offer';
      $removelang = ($offer) ? ' or <a href="'.$removeHref.'">remove current</a>' : '';


      $href = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'social-boost', 'controller' => 'index', 'action' => 'choose-offer'), 'admin_default', true);
      $this->addElement('Dummy', 'choose_offer', array(
        'content' => 'Choose <a class="smoothbox" href="'.$href.'">'.$lang.'</a>' . $removelang,
      ));

      if ($offer) {
        $view = Zend_Registry::get('Zend_View');

        $tmp = '';

        if (Engine_Api::_()->offers()->availableOffer($offer, true) != 'Unlimit') {
          $tmp = "
            <label>".$view->translate('OFFERS_offer_time_left')."</label>
            <span>".Engine_Api::_()->offers()->availableOffer($offer, true)."</span>
          ";
        } else {
          if (!$offer->coupons_unlimit) {
            $tmp = "
              <label>".$view->translate('OFFERS_offer_available')."</label>
              <span>".$view->translate('%s coupons', $offer->coupons_count)."</span>
            ";
          }
        }

        $offer = <<<OFFER
<table>
<tr><td class='rewards-form-preview'>

<img style="max-width: 150px;" src="{$offer->getPhotoUrl()}">
<h3><a href="{$offer->getHref()}">{$offer->getTitle()}</a></h3>

<label>{$view->translate('OFFERS_offer_discount')}</label>
<span>{$view->getOfferDiscount($offer)}</span><br/>

{$tmp}

</td></tr>
</table>
OFFER;

        $this->addElement('Dummy', 'choosen_offer', array(
          'content' => $offer
        ));
      }
    }

    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
    ));
  }
}
