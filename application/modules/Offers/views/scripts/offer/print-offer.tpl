<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: print-offer.tpl  08.11.11 15:39 ratbek $
 * @author     Ratbek
 */
?>

<?php
  $this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Offers/externals/styles/print_offer.css');
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Offers/externals/scripts/core.js');
?>

<script type="text/javascript">
  function print_page() {
    $('print_offer').setStyle('display', 'none');
    window.print();
    setTimeout("show_button()", 60000);
  }
  function show_button() {
    $('print_offer').setStyle('display', 'block');
  }
</script>

<link href="<?php echo $this->baseUrl().'/application/modules/Offers/externals/styles/print_offer.css'?>" type="text/css" rel="stylesheet" media="print">
<div class="print_offer_preview">
  <div id="print_offer" class="print_offer_button">
    <a href="javascript:void(0);" style="background-image: url('./application/modules/Offers/externals/images/print_offer.png'); width: 100px;" class="buttonlink" onclick="print_page()" align="right"><?php echo $this->translate('Take Print') ?></a>
  </div>
  <div class="print_offer_title">
    <?php echo $this->offer->getTitle()?>
  </div>
  <div class="print_offer_body">
    <div id="offer_id_<?php echo $this->offer->offer_id;?>" class="offer_item">
      <div class="offer_photo">
        <?php echo $this->htmlLink(array('route' => 'offers_specific', 'action' => 'view', 'offer_id' => $this->offer->offer_id), $this->itemPhoto($this->offer, 'thumb.main')); ?>
      </div>
      <div class="right">
        <div class="offer_info">
          <div class="offer_title">
            <h3><?php echo $this->htmlLink($this->offer->getHref(), $this->offer->getTitle()); ?></h3>
          </div>
          <div class="offer_discount">
            <label><?php echo $this->translate('OFFERS_offer_discount'); ?></label>
            <span><?php echo $this->offer->discount; ?><?php if ($this->offer->discount_type == 'percent'): ?> % <?php endif; ?></span>
          </div>
          <div class="offer_count">
            <label><?php echo $this->translate('OFFERS_offer_available'); ?></label><span><?php echo $this->offer->coupons_count . ' coupons'; ?></span>
          </div>
          <div class="offer_redeem">
            <label><?php echo $this->translate('OFFERS_Redeem'); ?></label> <span><?php echo Engine_Api::_()->offers()->timeInterval($this->offer);?></span>
          </div>
          <?php if ($this->offer->page_id > 0): ?>
            <div class="offer_presented_by">
              <label><?php echo $this->translate('OFFERS_Presented by'); ?></label> <span><?php if ($this->page && !$this->page->page_id) echo $this->page->getTitle(); ?></span>
            </div>
          <?php endif; ?>
        </div>
        <div class="offer_how_to_get">
          <div class="offer_address">Manas street 40, Bishkek city, Kyrgyzstan</div>
          <br>
          <div class="offer_contacts">(996) 777-555-700</div>
        </div>
      </div>
    </div>
  </div>
</div>