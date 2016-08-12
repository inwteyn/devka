<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: print.tpl  2012-08-17 15:39 ratbek $
 * @author     Ratbek
 */
?>
<style type='text/css'>
  .layout_page_footer {
    display: none;
  }
  .layout_page_header {
    display: none;
  }
  #global_content {
    margin-left: 0;
  }
  #im_container {
    display: none;
  }
</style>

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
<div class="print_offer_preview">
  <div id="print_offer" class="print_offer_button">
    <a href="javascript:void(0);" style="background-image: url('./application/modules/Offers/externals/images/print_offer.png'); width: 100px;" class="buttonlink" onclick="print_page()" align="right"><?php echo $this->translate('Take Print') ?></a>
  </div>
</div>

<div style="border:1px dashed #cccccc; padding: 10px; width: 660px;">
  <table cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td width="220" height="200">
        <img class="print_offer_photo" style="max-width: 220px" src="<?php echo $this->offer->getPhotoUrl('thumb.normal')?>">
      </td>
      <td valign="top">
        <table style="margin-left: 12px; line-height: 18px;" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <h3 style="margin-bottom: 12px">
                <a href="http://<?php echo $_SERVER['HTTP_HOST'] . $this->offer->getHref() ?>" style="color: #5F93B4; text-decoration: none;"><?php echo $this->offer->getTitle(); ?></a>
              </h3>
            </td>
          </tr>
          <tr>
            <td>
              <span><?php echo $this->translate("OFFERS_offer_price");?></span>
              <span style="font-weight: bold; color: #AF3706"><?php echo $this->getOfferPrice($this->offer); ?></span>
            </td>
          </tr>
          <tr>
            <td>
              <span><?php echo $this->translate("OFFERS_offer_discount");?></span>
              <span style="font-weight: bold"><?php echo $this->getOfferDiscount($this->offer); ?></span>
            </td>
          </tr>
          <tr>
            <td>
              <span><?php echo $this->translate("OFFERS_Redeem"); ?></span>
              <span style="font-weight: bold"><?php echo Engine_Api::_()->offers()->timeInterval($this->offer); ?></span>
            </td>
          </tr>
          <tr>
            <td>
              <?php if ($this->offer->page_id): ?>
                <span><?php echo $this->translate("OFFERS_Presented by"); ?></span>
                <span style="font-weight: bold"><?php echo $this->page->title; ?></span>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td>
              <?php if ($this->currentDate >= $this->offer->endtime && $this->offer->time_limit == "limit"): ?>
                <span><?php echo $this->translate("OFFERS_Status"); ?></span>
                <span style="font-weight: bold; color: #CC1A1A;"> <?php echo $this->translate("OFFERS_Expired"); ?></span>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td>
              <span><?php echo $this->translate("OFFERS_offer_coupon_code"); ?></span>
              <span style="font-weight: bold; color: #CC1A1A;"> <?php echo $this->offer->getCouponCode(); ?></span>
            </td>
          </tr>
          <?php if($this->contacts): ?>
          <tr>
            <td>
                <div style="background: none repeat scroll 0 0 #E9F4FA;border: 1px solid #D0E2EC;border-radius: 3px 3px 3px 3px;float: left;margin-top: 10px;padding: 6px;width: 400px;">
                  <?php if ($this->contacts->address != '' || $this->contacts->city != '' || $this->contacts->state != '' || $this->contacts->country != ''): ?>
                    <div style="font-size: 0.8em;padding-bottom: 4px;">
                      <span style="float: left; margin-right: 5px;">
                        <img src="http://<?php echo $_SERVER['HTTP_HOST'] . $this->baseUrl() . '/application/modules/Offers/externals/images/map.png';?>">
                      </span>
                      <?php echo $this->contacts->address.", ".$this->contacts->city.", ".$this->contacts->state.", ".$this->contacts->country; ?>
                    </div>
                  <?php endif; ?>
                  <?php if ($this->contacts->phone != ''): ?>
                    <div style="font-size: 0.8em;padding-bottom: 4px;">
                      <span style="float: left; margin-right: 5px;">
                        <img src="http://<?php echo $_SERVER['HTTP_HOST'] . $this->baseUrl() .'/application/modules/Offers/externals/images/phone.png'; ?>">
                      </span>
                      <?php echo $this->contacts->phone; ?>
                    </div>
                  <?php endif; ?>
                  <?php if ($this->contacts->website != ''): ?>
                    <div style="font-size: 0.8em;padding-bottom: 4px;">
                      <span style="float: left; margin-right: 5px; ">
                        <img src="http://<?php echo $_SERVER['HTTP_HOST'] . $this->baseUrl() . '/application/modules/Offers/externals/images/website.png'; ?>">
                      </span>
                      <?php echo $this->contacts->website; ?>
                    </div>
                  <?php endif; ?>
                </div>
            </td>
          </tr>
          <?php endif; ?>
        </table>
      </td>
    </tr>
  </table>
</div>