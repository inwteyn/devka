<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _productreview_richcontent.tpl 2011-03-16 16:14 kirill $
 * @author     Kirill
 */

  $product = $this->product;
  $productImageUrl = $product->getPhotoUrl();

  $review = $this->review;
  $types = $review->getTypes();

?>

<div>
  <div class="productreview-rich-photo">
    <a href="<?php echo $product->getHref(); ?>" title="<?php echo $product->getTitle(); ?>">
      <img style="width: 100%; height: auto; max-height: none; max-width: none;" src="<?php echo $productImageUrl;?>">
    </a>
  </div>

  <div class="productreview-rich-review" style="padding-left: 10px; padding-right: 10px; border-left: 2px solid lightgrey;">

    <div class="container">

      <?php foreach ($types as $type): ?>

        <div class="review_stars_static view">
          <div class="rating">
            <?php echo $this->reviewRate($this->rating); ?>
          </div>
          <div class="clr"></div>
        </div>
        <div class="clr"></div>

      <?php endforeach; ?>

      <div style="clear:both;"></div>

    </div>

    <div>
        <?php echo $this->heViewMore(nl2br($review->body), 100); ?>
    </div>

  </div>

</div>