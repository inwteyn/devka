<?php
/*
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php if ($this->paginator->getTotalItemCount()): ?>

  <script type="text/javascript">

    var $wrapper;

    window.addEvent('load', function (e) {

      var bW = $('add-bundle-to-cart');
      if(bW) {
        bW.addEvent('click', function(e) {
          var ids = $(this).get('data-ids');
          if(ids.length) {
            store_cart.addBundle(ids);
          }
        });
      }


      $wrapper = $('main-preview-wrapper');

      $$('.photo-preview').each(function (el) {
        el.addEvent('mouseenter', function (e) {
          var src = this.getAttribute('data-src');
          var big = this.getAttribute('data-big');
          zoomMe(big, big, 'main-preview', 'main-preview-wrapper', false);
        });
      });

      var src = $('main-preview').getAttribute('src');
      var big = $('main-preview').getAttribute('data-big');
      zoomMe(big, big, 'main-preview', 'main-preview-wrapper', false);
    });
  </script>
  <div>
    <div id="main-preview-wrapper">
      <img id="main-preview" src="<?php echo $this->product->getPhotoUrl(); ?>"
           data-big="<?php echo $this->product->getPhotoUrl(); ?>">
    </div>

    <div class="previews-list">
      <?php foreach ($this->paginator as $key => $photo): ?>
        <div id="photo_<?php echo $key + 1; ?>" class="center">
          <a style="text-align: left;"
             title="<?php echo ($photo->title) ? '<b>' . $photo->title . '</b>: ' . $photo->description : ''; ?>"
             href="javascript://">
            <img class="photo-preview" data-big="<?php echo $photo->getPhotoUrl(); ?>"
                 data-src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>"
                 src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" class="thumbs">
          </a>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="clear: both;"></div>
  </div>

<?php else: ?>
  <div style="text-align: center;">
    <img width="200" src="<?php echo $this->product->getPhotoUrl('thumb.profile'); ?>">
  </div>
<?php endif; ?>
<style>

</style>

<?php if($this->enabled && $this->bundle): ?>
  <div>
      <?php $products = $this->bundle->getProducts(); ?>
      <div class="storebundle-bundle">

        <?php if($this->bundle->text_visibility): ?>
          <h4><?php echo $this->bundle->title; ?></h4>
        <?php endif; ?>

        <?php $ids=array(); $cnt = 0; foreach($products as $itm){ $product = $itm->getProduct();
          $cnt++;
          $nPrice = $this->getNewPrice($product->getPrice(), $this->bundle->percent);
          $total += $product->getPrice();
          $totalD += $nPrice;
          $ids[] = $product->getIdentity();
        ?>
          <div class="storebundle-bundle-item">
            <div>
              <a target="_blank" href="<?php echo $product->getHref(); ?>">
                <img src="<?php echo $product->getPhotoUrl(); ?>" >
              </a>
            </div>
            <div class="storebundle-bundle-item-text">
                <p>
                  <a target="_blank" href="<?php echo $product->getHref(); ?>">
                    <?php echo $product->getTitle(); ?>
                  </a>
                </p>
                <span class="storebundle-bundle-item-old-price"><?php echo $this->toCurrency($product->getPrice()); ?></span>
                <span class="storebundle-bundle-item-new-price">
                  <?php
                    echo $this->toCurrency( $nPrice );
                  ?>
                </span>
            </div>
          </div>
            <?php if($cnt != count($products) ): ?>
            <div class="storebundle-bundle-item-delimiter">+</div>
            <?php else: ?>
              <div class="storebundle-bundle-item-delimiter">
                =
              </div>
              <div class="storebundle-bundle-item-delimiter">
                <button id="add-bundle-to-cart" data-ids="<?php echo implode(',', $ids); ?>">
                  <p>Add bundle to cart</p>
                  <p>
                    <span style="text-decoration: line-through; font-size: 14px;">
                      <?php echo $this->toCurrency($total); ?></span>
                    <span><?php echo $this->toCurrency($totalD); ?></span>
                  </p>
                </button>
              </div>
            <?php endif; ?>

        <?php }; ?>
      </div>
  </div>
<?php endif; ?>