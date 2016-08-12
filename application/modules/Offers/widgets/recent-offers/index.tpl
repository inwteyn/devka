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
        <?php foreach ($this->recentOffes as $offer): ?>
            <li>
                <?php echo $this->htmlLink($offer->getHref(), $this->itemPhoto($offer, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_offer')), array('class' => 'offer_profile_thumb item_thumb')); ?>
                <div class="item_info">
                    <div class="item_name">
                        <?php echo $this->htmlLink($offer->getHref(), $offer->getTitle(), array('class' => 'offer_profile_title')); ?>
                        <br/>
                    </div>
                    <span class="item_discount">
                        <?php echo $offer->discount; ?> <?php echo $offer->discount_type == 'percent' ? '%' : '$'; ?>
                    </span>

                    <div class="item_price">

                        <?php if ($offer->type == 'store') { ?>
                            <div class="item_price_after">
                                <?php echo $offer->discount . '%'; ?>
                            </div>
                        <?php } else { ?>
                            <div class="item_price_before">
                                <?php echo '$' . $offer->price_item; ?>
                            </div>
                            <div class="item_price_after">
                                <?php if ($offer->discount_type == 'percent') {
                                    echo '$' . $offer->price_item * ((100 - $offer->discount) / 100);
                                } else { ?>
                                    <?php echo '$' . ($offer->price_item - $offer->discount);
                                } ?>
                            </div>

                        <?php } ?>
                    </div>
                    <div class="clr"></div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>