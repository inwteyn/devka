<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: contacts.tpl 2012-10-03 17:53 taalay $
 * @author     TJ
 */
?>

<?php if ($this->error): ?>
  <div class="contacts_error"><?php echo $this->message; ?></div>

<?php else: ?>
  <script type="text/javascript">
      he_contacts.callback = "<?php echo $this->callback; ?>";
      he_contacts.list_type = 'all';
      he_contacts.contacts = <?php echo Zend_Json::encode($this->checkedItems) ?>;
      he_contacts.init();
  </script>
  <div id="he_contacts_loading" style="display:none;">&nbsp;</div>
  <div id="he_contacts_message" style="display:none;">
    <div class="msg"></div>
  </div>
  <div class="he_contacts">
    <h4 class="contacts_header"><?php echo $this->translate('OFFERS_Choose products'); ?></h4>

    <div style="clear:both"></div>

    <?php if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
      <div class="options">
        <div class="select_btns">
          <a href="javascript:void(0)" class="active" onClick="he_contacts.select('all'); he_contacts.add_class(this, 'active', $$('.select_btns a')[1]); this.blur();">
            <?php echo $this->translate("All"); ?>
          </a>
          <a href="javascript:void(0)" onClick="he_contacts.select('selected'); he_contacts.add_class(this, 'active', $$('.select_btns a')[0]); this.blur();">
            <?php echo $this->translate("Selected"); ?>
          </a>
        </div>
        <div class="contacts_filter">
          <input type="text" id="contacts_filter" class="filter"/>
        </div>
        <div class="clr"></div>
      </div>
      <div class="clr"></div>
    <?php endif; ?>

    <?php $priceColor = Engine_Api::_()->getApi('settings', 'core')->__get('store.price.color'); ?>
    <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
    <div class="contacts">
      <div id="he_contacts_list">
        <?php if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
          <?php foreach ($this->items as $item): ?>
            <?php $itemChecked = in_array($item->getIdentity(), $this->checkedItems); ?>
            <a class="item visible <?php if ($itemChecked) echo "active" ?> <?php echo 'content_item'; ?>" id="contact_<?php echo $item->getIdentity(); ?>" href='javascript:he_contacts.choose_contact(<?php echo $item->getIdentity(); ?>);'>
              <span class='photo' style='background-image: url()'>
                <?php if (!empty($item->photo_id)): ?>
                  <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
                <?php else: ?>
                  <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
                <?php endif; ?>
                <span class="inner"></span>
              </span>
              <span class="name" style="font-weight: bold"><?php echo $item->getTitle(); ?></span>

              <div>
                <?php if (isset($item->price_type) && $item->price_type == 'discount'): ?>
                  <span style='text-decoration: line-through'><?php echo @$this->locale()->toCurrency((double)$item->list_price, $currency); ?></span>
                <?php endif; ?>
                &nbsp;&nbsp;
                <span style='color: <?php echo $priceColor; ?>'><?php echo @$this->locale()->toCurrency((double)$item->price, $currency); ?></span>
              </div>
              <div class="clr"></div>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no"><?php echo $this->translate("STORE_There is no products."); ?></div>
        <?php endif; ?>

        <div id="no_result" class="hidden"><?php echo $this->translate("STORE_There is no products."); ?></div>
        <div class="clr" id="he_contacts_end_line"></div>
      </div>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
    <div class="btn" style="width:450px">
      <button onclick="he_contacts.callback = 'Offers.selectProducts';  he_contacts.send();" style="float:left;" id="select_products"><?php echo $this->translate('OFFERS_Select Products'); ?></button>
      <div class="he_contacts_choose_all" style="width: 100px; float:left; margin:5px">
        <input type="checkbox" onclick="he_contacts.choose_all_contacts($(this))" id="select_all_contacts" name="select_all_contacts">
        <label for="select_all_contacts"><?php echo $this->translate('HECORE_Select all');?></label>
      </div>
    </div>
  </div>
<?php endif; ?>

<div id="tmp_items" style="display:none">

</div>