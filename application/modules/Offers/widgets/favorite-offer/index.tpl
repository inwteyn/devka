<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ratbek
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-09-25 12:05 ratbek $
 * @author     Ratbek
 */
?>
<div class="featured_offer">
    <ul class="generic_list_widget generic_list_widget_large_photo">
      <?php $offer = $this->offer; ?>
        <li>
            <div class="photo">
              <?php echo $this->htmlLink($offer->getHref(), $this->itemPhoto($offer, 'thumb.normal'), array('class' => 'thumb')) ?>
            </div>
            <div class="info">
                <div class="title">
                  <?php echo $this->htmlLink($offer->getHref(), $this->string()->truncate($offer->getTitle(), 13)) ?>
                </div>
                <div class="discount item_details">
                  <?php echo $this->translate('OFFERS_offer_discount'); ?> <span class="value"><?php echo $offer->discount; ?> <?php if ($offer->discount_type == 'percent') echo '%'; ?></span>
                </div>
                <div class="offer_price item_details">
                  <?php if ($offer->type == 'paid'): ?>
                  <?php echo $this->translate('OFFERS_offer_price'); ?> <span class="value">$<?php echo $offer->price_offer; ?></span>
                  <?php elseif ($offer->type == 'free'): ?>
                  <?php echo $this->translate('OFFERS_offer_price'); ?> <span class="value"><?php echo $this->translate('OFFERS_offer_price_free'); ?></span>;
                  <?php endif; ?>
                </div>
              <?php if(!$this->offer->coupons_unlimit): ?>
                <div class="count"><label><?php echo $this->translate('OFFERS_offer_available'); ?></label>
                  <?php if ($this->offer->coupons_count > 0): ?>
                        <span><?php echo ($this->offer->coupons_unlimit) ? $this->translate('unlimit coupons') : $this->translate('%s coupons', $this->offer->coupons_count); ?></span>
                    <?php else: ?>
                        <span class="offer_no_relevant"><?php echo $this->translate('OFFERS_offer_not_left'); ?></span>
                    <?php endif; ?>
                </div>
              <?php endif; ?>
                <div class="time_left item_details">
                  <?php echo $this->translate('OFFERS_offer_time_left'); ?> <span class="value"><?php echo Engine_Api::_()->offers()->availableOffer($offer, true);?></span>
                </div>
                <div class="description">
                  <?php echo Engine_String::substr(Engine_String::strip_tags($offer->getDescription(true, true, false, 30)), 0, 100);
                  echo (Engine_String::strlen($offer->getDescription(true, true, false, 30))>99)? "...":'';
                  ?>
                </div>
            </div>
        </li>
    </ul>
</div>