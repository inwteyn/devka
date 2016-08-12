<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Offers/externals/scripts/offers.js');

?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
<ul class="offers HeCubeView">
  <?php foreach($this->paginator as $offer): ?>
  <?php $link_view = $this->url(array('action' => 'view', 'offer_id' => $offer->getIdentity()), 'offers_specific'); ?>
  <li id="offer_id_<?php echo $offer->offer_id;?>" class="offer_item" style="box-shadow: 0 0 2px 0 rgba(0, 0, 0, 0.3);">
    <a href="<?php echo $offer->getHref()?>"><div  class="offer_photo"
                                                   style="background-image: url('<?php echo $offer->getPhotoUrl('thumb.profile') ?>');"></div></a>
    <div class="offer_info">
      <div class="offer_title">
        <h3><?php echo $this->htmlLink($offer->getHref(), $this->string()->truncate($offer->getTitle(), 20)); ?></h3>
      </div>
    </div>
    <div class="offer_options">
      <?php if($offer->featured):?>
        <div class="featured_offers_status">
          <?php echo $this->translate('Featured')?>
        </div>
      <?php endif;?>
      <?php
      if(count($pop )>0) {
        foreach ($pop as $popular) {
          if ($popular['offer_id'] == $offer->getIdentity()) {
            ?>
            <div class="popular_offers_status">
              <?php echo $this->translate('Popular') ?>
            </div>
          <?php
          }
        }
      }
      ?>
      <div class="offer_discount">
        <!--<label><?php /*echo $this->translate('OFFERS_offer_discount'); */?></label>-->
        <span><?php echo $this->getOfferDiscount($offer);?></span></div>
      <?php if(isset($offer->price_item) && !empty($offer->price_item)): ?>
        <div class="item_price with_main_price" style="float: none;">
                <span style="font-size: 9pt">
                <span class="line_overline_price_parent" >
                  <?php echo @$this->locale()->toCurrency((double)$offer->price_item, $this->currency); ?>
                  <span class="line_overline_price"></span>
                </span> - <?php
                  if($offer->getOfferType() == 'free'){
                    if($offer->discount_type  == 'percent'){
                      $discount_price_echo = $offer->price_item - (($offer->price_item / 100)* $offer->discount);
                      echo @$this->locale()->toCurrency((double)$discount_price_echo, $this->currency);
                    }else{
                      echo @$this->locale()->toCurrency((double)$offer->price_item - $offer->discount, $this->currency);
                    }

                  }else{
                    echo @$this->locale()->toCurrency((double)$offer->price_offer, $this->currency);
                  }


                  ?>
              </span>
        </div>
      <?php endif;
      ?>
      <?php if($offer->type != 'paid'): ?>
        <div class="offer_price" style="float: none;">
          <?php echo $this->getOfferPrice($offer); ?>
        </div>
      <?php endif; ?>
      <?php if (Engine_Api::_()->offers()->availableOffer($offer, true) != 'Unlimited'): ?>
        <div class="offer_time_left">
          <label>
            <?php echo $this->translate('OFFERS_offer_time_left'); ?></label>
                <span>
                  <?php echo Engine_Api::_()->offers()->availableOffer($offer, true); ?></span></div>
        <?php //else: ?>
        <?php if ($offer->coupons_count>0): ?>
          <div class="offer_count"><label><?php echo $this->translate('OFFERS_offer_available'); ?></label>
            <span> <?php echo $this->translate('%s coupons', $offer->coupons_count); ?></span></div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <div class="offer_view_button">
      <button name="submit" class="btn_view_offer" onclick='javascript:Offers.view("<?php echo $link_view; ?>");' type="submit"><?php echo $this->translate('OFFERS_offer_view'); ?></button>
    </div>
  </li>
  <?php endforeach; ?>
</ul>
<?php echo $this->paginationControl($this->paginator, null, array('pagination/page.tpl', 'offers'), array('page' => $this->pageObject)); ?>
<?php else: ?>
<div class="tip">
    <span>
      <?php if ($this->filter == 'upcoming') : ?>
        <?php echo $this->translate('OFFERS_No upcoming items');?>
      <?php else : ?>
        <?php echo $this->translate('OFFERS_No past items');?>
      <?php endif; ?>
      <?php if ($this->isAllowedPost && $this->subject->isOwner($this->viewer())):?>
        <?php echo $this->translate('OFFERS_pageoffers_create %s', $this->htmlLink('javascript:void(0)', $this->translate('OFFERS_pageoffers_create'), array('onClick' => 'Offers.loadTab("form")'))); ?>
      <?php endif; ?>
    </span>
</div>
<?php endif; ?>