<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-09-12 11:42:11 ratbek $
 * @author     Ratbek
 */
?>

<div class="offers_list">
  <ul>
    <?php foreach($this->popularOffes as $offer): ?>
    <li>
      <?php echo $this->htmlLink($offer->getHref(), $this->itemPhoto($offer, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_offer')), array('class' => 'offer_profile_thumb item_thumb')); ?>
      <div class="item_info">
        <div class="item_name">
          <?php echo $this->htmlLink($offer->getHref(), $offer->getTitle(), array('class' => 'offer_profile_title')); ?><br />
        </div>
        <div class="item_discount item_details">
          <?php echo $offer->discount; ?> <?php if ($offer->discount_type == 'percent') echo '%'; ?>
        </div>
        <div class="item_count_users item_details">
          <?php echo $this->translate('OFFERS_Count users') ?> <?php echo $offer->count_offers; ?>
        </div>
        <div class="clr"></div>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
</div>