<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
  en4.core.runonce.add(function () {
    var myElements = $$('.offer-item');
    var i = 0;
    for (i = 0; i < myElements.length; i++) {
      var photo = myElements[i].getProperty('id');
      initImageZoom(
        {
          rel: photo,
          transition: Fx.Transitions.Cubic.easeIn
        }
      );
    }
  });

  function purchaseOffer($type) {
    if ($type == 'disabled') {
      return false;
    }
    var $url = '<?php echo $this->url(array('offer_id' => $this->offer->getIdentity()), 'offers_subscription', true)?>';
    window.location.href = $url;
  }
</script>
<?php
$result_redeem = preg_match('/(0{4})-(0{2})-(0{2}) (0{2}):(0{2}):(0{2})/', $this->offer->redeem_starttime);
?>
<div class=" view_offer_item_details" id="view_offer_item_details_<?php echo $this->offer->getIdentity() ?>">

<div class="view_offer_options">
<?php if ($this->offer->type != 'paid'): ?>
  <div class="offer_price">
    <label><?php echo $this->translate('OFFERS_offer_price'); ?></label>
    <?php echo $this->getOfferPrice($this->offer); ?>
  </div>
<?php endif; ?>
<!--  <?php /*if(isset($this->offer->price_item) && !empty($this->offer->price_item)): */ ?>
      <div class="item_price">
        <label><?php /*echo $this->translate('OFFERS_Item Price'); */ ?>:</label>
        <span><?php /*echo @$this->locale()->toCurrency((double)$this->offer->price_item, $this->currency); */ ?></span>
      </div>
    --><?php /*endif; */ ?>
<div class="discount">
  <label><?php echo $this->translate('Save'); ?></label>
  <span><?php echo $this->getOfferDiscount($this->offer); ?></span>
</div>
<?php if (isset($this->offer->price_item) && !empty($this->offer->price_item)): ?>
  <div class="item_price with_main_price">
    <label><?php echo $this->translate('OFFERS_Item Price'); ?>:</label>
       <span><span class="line_overline_price_parent">
           <?php echo @$this->locale()->toCurrency((double)$this->offer->price_item, $this->currency); ?>
           <span class="line_overline_price"></span></span> -
         <?php
         if ($this->offer->getOfferType() == 'free') {
           if ($this->offer->discount_type == 'percent') {
             $discount_price_echo = $this->offer->price_item - (($this->offer->price_item / 100) * $this->offer->discount);
             echo @$this->locale()->toCurrency((double)$discount_price_echo, $this->currency);
           } else {
             echo @$this->locale()->toCurrency((double)$this->offer->price_item - $this->offer->discount, $this->currency);
           }

         } else {
           echo @$this->locale()->toCurrency((double)$this->offer->price_offer, $this->currency);
         }


         ?>
       </span>
  </div>
<?php endif; ?>
<?php if (!$this->offer->coupons_unlimit): ?>
  <div class="count"><label><?php echo $this->translate('OFFERS_offer_available'); ?></label>
    <?php if ($this->offer->coupons_count > 0): ?>
      <span><?php echo ($this->offer->coupons_unlimit) ? $this->translate('unlimit coupons') : $this->translate('%s coupons', $this->offer->coupons_count); ?></span>
    <?php else: ?>
      <span class="offer_no_relevant"><?php echo $this->translate('OFFERS_offer_not_left'); ?></span>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php if ($this->time_left != 'Unlimited'): ?>
  <div class="time_left"><label><?php echo $this->translate('OFFERS_offer_time_left'); ?></label>
    <?php if ($this->checkTimeLeft): ?>
      <span class="offer_time_left"><?php echo $this->time_left ?></span>
    <?php else: ?>
      <!--   <span class="offer_last_relevant"><?php /*echo $this->translate('OFFERS_offer_time_is_up'); */ ?></span-->>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php if (!$result_redeem): ?>
<div class="redeem">
  <label><?php echo $this->translate('OFFERS_offer_redeem'); ?></label>
  <?php if ($this->checkTimeRedeem): ?>
    <span>
            <?php echo date('M d, Y', strtotime($this->offer->redeem_starttime)) . ' - ' . date('M d, Y', strtotime($this->offer->redeem_endtime)); ?>
          </span>
  <?php else: ?>
    <span class="offer_no_relevant">
              <?php echo $this->translate('OFFERS_offer_no_relevant'); ?>
            </span>
  <?php endif; ?>
</div>
  <?php endif; ?>

<?php if (isset($this->page) && $this->page): ?>
  <div class="present_by">
    <label><?php echo $this->translate('OFFERS_offer_presented_by'); ?></label>
    <span><?php echo $this->htmlLink($this->page->getHref(), $this->page->getTitle()); ?></span>
  </div>
<?php endif; ?>

<?php if (!$this->offer->isEnable()): ?>
  <ul class="form-errors">
    <li><?php echo $this->translate('OFFERS_offer_disabled'); ?></li>
  </ul>
<?php endif; ?>
<?php if ($this->checkTimeLeft): ?>
  <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
    <?php if (!$this->isSubscribed) : ?>
      <div class="accept_offer">
        <?php if ($this->offer->getPrice()) : ?>
          <!-- <span class="offer_profile_price"><?php /*echo $this->getOfferPrice($this->offer); */ ?></span>-->
        <?php endif; ?>
        <?php if (!$this->canAccept): ?>
          <ul class="form-errors">
            <li>
              <ul class="errors">
                <li>Your member level is not allowed to receive this offer. Please upgrade your member level.</li>
              </ul>
            </li>
          </ul>
        <?php elseif ($this->offer->coupons_unlimit == 1 || $this->offer->coupons_count > 0): ?>
          <button onclick="purchaseOffer(<?php if (!$this->requireIsComplete): ?>'disabled'<?php endif; ?>)"
                  name="submit" id="submit" type="submit"
                  class="<?php if (!$this->requireIsComplete) : ?>disabled<?php endif; ?>">
              <span class="offer_button">
                <?php if ($this->offer->getOfferType() == 'free') : ?>
                  <?php echo $this->translate('OFFERS_Accept Offer'); ?>
                <?php elseif ($this->offer->getOfferType() == 'reward' || $this->offer->getOfferType() == 'store') : ?>
                  <?php echo $this->translate('OFFERS_Accept Offer'); ?>
                <?php
                elseif ($this->offer->getOfferType() == 'paid') : ?>
                  <?php echo $this->translate('OFFERS_Purchase Offer'); ?>
                <?php endif; ?>
              </span>
          </button>
          <?php if ($this->offer->getOfferType() == 'reward' || $this->offer->getOfferType() == 'store') : ?>
            <div class="accept_offer_desc">
              <?php echo $this->translate('As soon as you will fulfill the following requirements you will be able to get this offer') ?>
              :
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($this->requires)): ?>
          <div class="item_require">
            <ul>
              <?php $counter = 1;
              foreach ($this->requires as $item) : ?>
                <?php
                $require = Engine_Api::_()->offers()->getRequire($item->type, ($this->offer->getPage() ? 'page' : 0));
                if (empty($require)) {
                  continue;
                }
                $link = 'javascript:void(0);';
                if (!empty($require['require_link'])) {
                  $link = $require['require_link'];
                }
                ?>
                <li
                  class="<?php if (in_array($item->getIdentity(), $this->require_complete)): ?>complete<?php endif; ?>">
                  <div class="item_title">
                    <span class="item_counter"><?php echo $counter; ?></span>
                    <?php if ($item->type == 'likepage'): ?>
                      <span
                        class="<?php echo (in_array($item->getIdentity(), $this->require_complete)) ? 'complete' : ''; ?>"><?php echo $this->translate('OFFERS_REQUIRE_' . strtoupper($item->type), $item->params['count'], $this->htmlLink($this->page->getHref(), $this->page->getTitle(), array('target' => '_blank', 'style' => 'display: inline'))); ?></span>
                    <?php elseif ($item->type == 'review' && $this->offer->page_id): ?>
                      <span
                        class="<?php echo (in_array($item->getIdentity(), $this->require_complete)) ? 'complete' : ''; ?>"><?php echo $this->translate('OFFERS_REQUIRE_PAGEREVIEW', $this->htmlLink($this->page->getHref(), $this->page->getTitle(), array('target' => '_blank', 'style' => 'display: inline'))); ?></span>
                    <?php
                    elseif ($item->type == 'suggest' && $this->offer->page_id): ?>
                      <span
                        class="<?php echo (in_array($item->getIdentity(), $this->require_complete)) ? 'complete' : ''; ?>"><?php echo $this->translate('OFFERS_REQUIRE_PAGESUGGEST', $this->htmlLink($this->page->getHref(), $this->page->getTitle(), array('target' => '_blank', 'style' => 'display: inline')), $item->params['count']); ?></span>
                    <?php
                    else: ?>
                      <span
                        class="<?php echo (in_array($item->getIdentity(), $this->require_complete)) ? 'complete' : ''; ?>"><?php echo $this->translate('OFFERS_REQUIRE_' . strtoupper($item->type), $item->params); ?></span>

                    <?php endif; ?>
                  </div>
                </li>
                <?php $counter++; endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    <?php else : ?>
      <div>
        <ul class="form-notices">
          <li>

            <?php if ($this->offer->getOfferType() == 'paid') : ?>
              <?php echo $this->translate('You have already purchased this offer'); ?> <a
                href="<?php echo $this->serverUrl() . $this->url(array('action' => 'manage'), 'offers_general', true); ?>"><?php echo $this->translate('View') ?></a>
            <?php else : ?>
              <?php echo $this->translate('You have already accepted this offer'); ?> <a
                href="<?php echo $this->serverUrl() . $this->url(array('action' => 'manage'), 'offers_general', true); ?>"><?php echo $this->translate('View') ?></a>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <ul class="form-errors">
      <li>
        <?php echo $this->translate('OFFERS_login_to_get_coupon'); ?>
      </li>
    </ul>
  <?php endif; ?>
<?php else: ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->translate('OFFERS_offer_time_left_expired'); ?>
    </li>
  </ul>
<?php endif; ?>

<br/>

<div class="view_offer_desc">
  <?php echo $this->offer->description; ?>
</div>

<?php if (($this->offer->type === 'store') && ($this->products) && (count($this->products) > 0)): ?>
  <div id="offer_products">
    <span><?php echo $this->translate('OFFERS_offer_products'); ?></span>

    <div class="view_products_offer">
      <ul>
        <?php foreach ($this->products as $product): ?>
          <li>
            <span
              class="offer_product_photo"><?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb_icon', $product->getTitle())); ?></span>
            <span
              class="offer_product_title"><?php echo $this->htmlLink($product->getHref(), $product->getTitle()); ?></span>
            <span
              class="offer_product_price"><?php echo @$this->locale()->toCurrency((double)$product->price, $this->currency); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif; ?>
<div style="clear: both;"></div>
<?php if ($this->offerPhotos->getTotalItemCount() > 0): ?>
  <span class="offer-item" id="thumbs-photo">
     <?php foreach ($this->offerPhotos as $key => $photo) : ?>
       <div class="offer_thumbs_nocaptions" style="height: 160px;">
         <table>
           <tr valign="middle">
             <td valign="middle" height="160" width="140">
               <div id="photo_<?php echo $key + 1; ?>" class="center">
                 <a style="text-align: left;" rel="thumbs-photo[<?php echo $this->subject()->getTitle() ?>]"
                    title="<?php echo ($photo->title) ? '<b>' . $photo->title . ': </b>' . $photo->description : ''; ?>"
                    href="<?php echo $photo->getPhotoUrl(); ?>">
                   <img src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" class="thumbs"
                        style="max-height: 160px; max-width: 140px;">
                 </a>
               </div>
             </td>
           </tr>
         </table>
       </div>
     <?php endforeach; ?>
    </span>
<?php endif; ?>
</div>

</div>