<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-09-13 11:42:11 ratbek $
 * @author     Ratbek
 */
?>

<div class="offers_list">
  <ul>
    <?php foreach($this->offers as $offer): ?>
      <?php foreach($this->hotOffers as $hotOffer): ?>
        <?php if($offer->offer_id == $hotOffer['id']): ?>
          <li>
            <?php echo $this->htmlLink($offer->getHref(), $this->itemPhoto($offer, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_offer')), array('class' => 'offer_profile_thumb item_thumb')); ?>
            <div class="item_info">
              <div class="item_name">
                <?php echo $this->htmlLink($offer->getHref(), $offer->getTitle(), array('class' => 'offer_profile_title')); ?><br />
              </div>
              <div class="item_discount item_details">
                <?php echo $offer->discount; ?> <?php if ($offer->discount_type == 'percent') echo '%'; ?>
              </div>
              <div class="item_days_left">
                <?php $days = $hotOffer['days_left'];
                  $hours = $hotOffer['hours_left'];
                ?>
                <?php if($days) echo $this->translate(array('OFFERS_%s day','%s days', $days),$days); ?>
                <?php if ($hours) echo $this->translate(array('OFFERS_%s hour', '%s hours', $hours), $hours) . ' left'; ?>
              </div>
              <div class="clr"></div>
            </div>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </ul>
</div>