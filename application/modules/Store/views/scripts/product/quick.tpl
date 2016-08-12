<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: products.tpl  17.09.11 11:57 TeaJay $
 * @author     Taalay
 */
?>


<?php $this->headScript()->appendFile('application/modules/Store/externals/scripts/Zoomer.js'); ?>

<?php
$paginator = $this->product->getCollectiblesPaginator();
$paginator->setItemCountPerPage(100);
$cnt = $paginator->getTotalItemCount();
?>
<?php if ($cnt): ?>
  <script type="text/javascript">
    var sliderWrapper = $('quick-photos-previews-slider');

    var quickSlider = new SomeSlider({
      container: sliderWrapper,
      content: sliderWrapper.getElement('.quick-photos-previews-wrapper'),
      items: sliderWrapper.getElement('.quick-photos-previews-wrapper').getElement('table'),
      leftBtn: sliderWrapper.getElement('.quick-previews-navigation-left'),
      rightBtn: sliderWrapper.getElement('.quick-previews-navigation-right'),
      itemsCount: '<?php echo $cnt; ?>'
    });
    window.quickSlider = quickSlider;
    window.quickSliderPreviews = 0;

    var $wrapper;
    var mainPreview = 'quick-preview';

      $wrapper = $('quick-photos-main-photo');
      $$('.quick-photos-preview').each(function (el) {
        el.addEvent('click', function (e) {
          var img = $(this).getElement('img');
          var src = img.getAttribute('data-src');
          var big = img.getAttribute('data-big');
          zoomMe(big, big, mainPreview, 'quick-photos-main-photo', true);
        });
      });
      var src = $(mainPreview).getAttribute('src');
      var big = $(mainPreview).getAttribute('data-big');
      zoomMe(big, big, mainPreview, 'quick-photos-main-photo', true);
  </script>
<?php endif; ?>

<div id="quick-preview-wrapper" class="quick-photos-wrapper">
  <?php if ($cnt): ?>
    <div class="quick-photos-main-photo" id="quick-photos-main-photo">
      <img id="quick-preview" style="display: none;" src="<?php echo $this->product->getPhotoUrl(); ?>"
           data-big="<?php echo $this->product->getPhotoUrl(); ?>" />
    </div>

    <div class="quick-photos-previews-slider" id="quick-photos-previews-slider">
      <a href="javascript://" class="quick-previews-navigation quick-previews-navigation-left"></a>

      <div class="quick-photos-previews-wrapper">
        <table style="position: absolute; left: 0;">
          <tr>
            <?php foreach ($paginator as $key => $photo): ?>
              <td>
                <div>
                  <a class="quick-photos-preview"
                     title="<?php echo ($photo->title) ? '<b>' . $photo->title . '</b>: ' . $photo->description : ''; ?>"
                     href="javascript://">
                    <img onload="window.quickSliderPreviews++; window.quickSlider.initValues();"
                         onclick="changeImage('<?php echo $photo->getPhotoUrl(); ?>');"
                         height="100"
                         class="thumbs photo-preview"
                         data-big="<?php echo $photo->getPhotoUrl(); ?>"
                         src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>"
                         data-src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>"
                      >
                  </a>
                </div>
              </td>
            <?php endforeach; ?>
          </tr>
        </table>
      </div>
      <a href="javascript://" class="quick-previews-navigation quick-previews-navigation-right"></a>

      <div style="clear: both"></div>
    </div>
  <?php else : ?>
    <div class="quick-photos-main-photo" style="height: 550px; width: 30%;overflow-y: hidden;">
      <img width="175" src="<?php echo $this->product->getPhotoUrl('thumb.profile'); ?>">
    </div>
  <?php endif; ?>
</div>



















<div class="quick-product-title">
  <h3>
    <a href="<?php echo $this->product->getHref(); ?>" alt="<?php echo $this->product->getTitle(); ?>">
      <?php echo $this->product->getTitle(); ?>
    </a>
  </h3>
</div>


<div class="quick-info-wrapper">

  <div>
    <ul class="he-item-list product_profile_details"
        id="store-product-profile-details-<?php echo $this->product->getIdentity() ?>">
      <li>
        <?php if ($this->allowOrder): ?>
          <div style="float: left;">
            <div class="adding-block">
              <?php if ($this->product->isFree()): ?>
                <?php echo $this->getPriceBlock($this->product); ?>
              <?php else : ?>
                <table>
                  <?php $hasOptions = (is_array($this->product->params) && count($this->product->params));
                  if ($hasOptions): ?>
                    <?php foreach ($this->product->params as $param): $options = (isset($param['options'])) ? explode(',', $param['options']) : array(); ?>
                      <tr class="quick-product-options"
                          style="display: <?php echo (!$this->product->isAddedToCart()) ? '' : 'none'; ?>;">
                        <td><?php echo $param['label']; ?>:&nbsp;&nbsp;</td>
                        <td class="options">
                          <select name="<?php echo $param['label']; ?>" onchange="quickToCart.check()"
                                  class="store-options">
                            <option
                              value='-1'><?php echo $this->translate('STORE_-Select-'); ?></option>

                            <?php foreach ($options as $option): ?>
                              <option
                                value='<?php echo trim($option); ?>'> <?php echo trim($option); ?></option>
                            <?php endforeach; ?>

                          </select>
                          &nbsp;<span
                            class="select-error">&larr;<?php echo $this->translate('STORE_Select a %1$s', $param['label']); ?></span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>

                  <span class="product_profile_price"><?php echo $this->getPrice($this->product); ?></span>

                  <?php //if (!$this->product->isAddedToCart() || $hasOptions): ?>

                  <?php if ($this->product->type == 'simple') : ?>
                    <tr class="quick-product-options"
                        style="display: <?php echo (!$this->product->isAddedToCart()) ? '' : 'none'; ?>;">
                      <td><?php echo $this->translate('STORE_Quantity'); ?>:&nbsp;&nbsp;</td>
                      <td class="options">
                        <input type="text" name="quantity" id="quantity" onchange="quickToCart.check()"
                               style="width: 53px" value="1">
                        &nbsp;&nbsp;&nbsp;<?php echo $this->translate(
                          array('%s item available', '%s items available', (int)$this->product->getQuantity()),
                          @$this->locale()->toNumber($this->product->getQuantity())); ?>
                        &nbsp;<span
                          class="select-error">&larr;<?php echo $this->translate('STORE_Enter a %1$s', $this->translate('STORE_Quantity')); ?></span>
                      </td>
                    </tr>


                  <?php endif; ?>

                  <?php if (!$this->product->isAddedToCart()) : ?>

                    <tr>
                      <td  class="options" id="add-to-cart-button" colspan="2">
                        <button
                          onclick="quickToCart.add(<?php echo $this->product->getIdentity(); ?>, <?php echo $this->item_id ?>, this)"
                          class="store-disabled" id='add-to-cart'>
                          <span
                            class="store-add-button product_button"><?php echo $this->translate('STORE_Add to Cart'); ?></span>
                        </button>
                        <?php echo $this->GetWish($this->product); ?>
                      </td>
                    </tr>

                  <?php else : ?>
                    <tr>
                      <td class="options" id="add-to-cart-button" colspan="2">
                        <button
                          onclick="quickToCart.remove(<?php echo $this->product->getIdentity(); ?>, <?php echo $this->item_id ?>, this)"
                          id='add-to-cart'>
                          <span
                            class="store-remove-button product_button"><?php echo $this->translate('STORE_Remove from Cart'); ?></span>
                        </button>
                        <?php echo $this->GetWish($this->product); ?>
                      </td>
                    </tr>
                  <?php endif; ?>
                </table>
              <?php endif; ?>

            </div>
          </div>

        <?php else : ?>
          <?php if (!$this->viewer->getIdentity()) : ?>
            <div class="tip" style="margin-top: 10px">
              <span><?php echo $this->translate("You need to login to add the product to your cart."); ?></span>
            </div>
          <?php else : ?>
            <div class="tip" style="margin-top: 10px">
              <span><?php echo $this->translate("You do not have a permission to order products"); ?></span></div>
          <?php endif; ?>
        <?php endif; ?>
      </li>
    </ul>

    <div class="he-item-desc product_profile_desc" style="overflow-y: hidden;">
      <?php echo $this->heViewMore($this->product->getShortDescription(700), 200); ?>
    </div>

    <div class="product-quick-rating">
      <?php if ($this->reviews && $this->reviews->getTotalItemCount()): ?>
        <?php echo $this->quickProductRate($this->product); ?>
        <?php foreach ($this->reviews as $row): $user = Engine_Api::_()->getItem('user', $row->user_id); ?>
          <div style="margin-top: 10px;">
            <!--- Product Reviews -->
            <div class="quick-productreview-item">
              <div style="float: left;">
                <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
              </div>
              <div style=" margin-left: 15px; float: left; width: 80%;">
                <div>
                  <div>
                    <?php if ($this->countOptions): ?>
                      <div class="he_rate_small_cont"  style="display: inline;">
                        <?php echo $this->reviewRate($row->rating, true) ?>
                        <div class="clr"></div>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="writer">
                    <div style="float: left;">
                      <a href="<?php echo $user->getHref(); ?>"><?php echo $user->getTitle(); ?></a>
                    </div>
                    <div class="posted" style="float: right;">
                      <?php echo $this->timestamp($row->creation_date) ?>
                    </div>
                    <div style="clear: both"></div>
                  </div>
                </div>
              </div>
              <div style="clear: both"></div>
              <div class="productreview-description">
                <?php echo $this->heViewMore($row->body, 100); ?>
              </div>
            </div>
            <!--- Product Reviews -->
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div>
          <span>
            <?php $title = $this->product->getTitle();
            $title = $this->product->getSlug($title);
            $params = array(
              'product_id' => $this->product->getIdentity(),
              'title' => $title,
              'content' => 'productreview'
            );
            $href = $this->url($params, 'store_profile', 1);
            $href = '<a href="'.$href.'">post</a>';
            ?>
            <?php echo $this->translate('STORE_No reviews quick %s', $href); ?>
          </span>
        </div>
      <?php endif; ?>
    </div>

  </div>

</div>

<div style="clear: both;"></div>
