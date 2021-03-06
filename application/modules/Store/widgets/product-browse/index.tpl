<?php

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
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/scripts/pinfeed.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/scripts/manager.js');
?>

<script type="text/javascript">
  product_manager.widget_url = '<?php echo $this->url(array('module'=>'core', 'controller'=>'widget'), 'default', 'true')?>';
  product_manager.content_id = '<?php echo $this->identity; ?>';
  product_manager.widget_element = '.layout_store_product_browse';
  product_manager.tag_id = '<?php echo $this->tag_id?>';
  product_manager.category = '<?php echo $this->cat_id; ?>';
  product_manager.sub_category = '<?php echo $this->subCat_id; ?>';
  product_manager.view = '<?php echo $this->view; ?>';
  product_manager.isPin = '<?php echo $this->isPin; ?>';
  var internalTips2 = null;

  en4.core.runonce.add( function () {
    var $cat_id = '<?php echo $this->cat_id; ?>';
    var $subCat_id = '<?php echo $this->subCat_id; ?>';
    var $child_id = '<?php echo $this->child_id; ?>';
    if ($('filter_form').getElementById('profile_type')) {
      $('filter_form').getElementById('profile_type').value = $cat_id;
    }
    if ($('filter_form').getElementById('field_' + $child_id)) {
      $('filter_form').getElementById('field_' + $child_id).value = $subCat_id;
    }

    var miniTipsOptions1 = {
      'htmlElement': '.he-hint-text',
      'delay': 1,
      'className': 'he-tip-mini',
      'id': 'he-mini-tool-tip-id',
      'ajax': false,
      'visibleOnHover': false
    };

    var internalTips1 = new HETips($$('.he-hint-tip-links'), miniTipsOptions1);
  });

  function openProduct(url) {
    location.href = url;
  }
  product_manager.pinfeed_widget_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action'=>'index', 'name' => 'store.product-browse'), 'default', true); ?>';

  window.addEvent('scroll', function () {
    if (window.getScrollTop() + 5 >= window.getScrollSize().y - window.getSize().y) {
      product_manager.loadMore();
    }
  });
</script>

<?php
$this->headTranslate(
  array('STORE_Reset product search')
);
?>
<div class="store-tabs layout_core_container_tabs fw_active_theme_<?php echo $this->activeTheme() ?>">
  <div class="tabs_alt tabs_parent">
    <ul id="main_tabs">
      <li class="<?php if ($this->sort == 'recent') echo 'active'; ?>">
        <a class="store_sort_buttons" id="store_sort_recent"
           href="javascript:product_manager.setSort('recent');"><?php echo $this->translate("Recent") ?></a>
      </li>
      <li class="<?php if ($this->sort == 'popular') echo 'active'; ?>">
        <a class="store_sort_buttons" id="store_sort_popular"
           href="javascript:product_manager.setSort('popular');"><?php echo $this->translate("Most Popular") ?></a>
      </li>
      <li class="<?php if ($this->sort == 'sponsored') echo 'active'; ?>">
        <a class="store_sort_buttons" id="store_sort_sponsored"
           href="javascript:product_manager.setSort('sponsored');"><?php echo $this->translate("STORE_Sponsored") ?></a>
      </li>
      <li class="<?php if ($this->sort == 'featured') echo 'active'; ?>">
        <a class="store_sort_buttons" id="store_sort_featured"
           href="javascript:product_manager.setSort('featured');"><?php echo $this->translate("STORE_Featured") ?></a>
      </li>

      <?php if ($this->count) : ?>
        <span class="modes">
          <li class="view_mode_wrapper">
            <a class="<?php echo ($this->view == 'icons') ? 'active' : ''; ?> icons store-view-types he-hint-tip-links"
               onfocus="this.blur();" href="javascript://" onclick="product_manager.setView('icons', $(this));">
              <i class="hei hei-th-large"></i>
            </a>

            <div class="he-hint-text hidden"><?php echo $this->translate('Icons'); ?></div>
          </li>
          <li class="view_mode_wrapper">
            <a class="<?php echo ($this->view == 'list') ? 'active' : ''; ?> list store-view-types he-hint-tip-links"
               onfocus="this.blur();" href="javascript://" onclick="product_manager.setView('list', $(this));">
              <i class="hei hei-th-list"></i>
            </a>

            <div class="he-hint-text hidden"><?php echo $this->translate('List'); ?></div>
          </li>
        </span>
      <?php endif; ?>

      <a id="store_loader_browse"
         class="store_loader_browse hidden"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/loader.gif', ''); ?></a>
    </ul>
  </div>
</div>

<span id="product_form_info" class="product_form_info hidden"></span>
<span id="product_tag_info" class="product_tag_info hidden"></span>
<span id="product_category_info" class="product_category_info hidden"></span>

<div class="clr"></div>
<?php if ($this->count > 0) : ?>
  <div class="he-items" id="stores-items" style="display:<?php echo ($this->view == 'list') ? 'block' : 'none'; ?>;">
    <ul class="he-item-list">
      <?php
      /**
       * @var $item Store_Model_Product
       */

      foreach ($this->paginator as $item):
        ?>
        <li>
          <div class="he-item-photo">
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
          </div>
          <div class="he-item-options store-item-options" style="text-align: center">
            <?php echo $this->getPriceBlock($item); ?>
            <?php if ($item->type == 'simple'): ?>
              <br/>
              <div class="store_products_count">
                <?php echo $this->translate(
                  array('%s item available', '%s items available', (int)$item->getQuantity()),
                  @$this->locale()->toNumber($item->getQuantity())); ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="he-item-info">
            <div class="he-item-title">
              <h3>
                <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 40), array('title' => $item->getTitle())) ?>
                <?php if ($item->sponsored) : ?>
                  <img class="icon" src="application/modules/Store/externals/images/sponsored.png"
                       title="<?php echo $this->translate('STORE_Sponsored'); ?>">
                <?php endif; ?>
                <?php if ($item->featured) : ?>
                  <img class="icon" src="application/modules/Store/externals/images/featured.png"
                       title="<?php echo $this->translate('STORE_Featured'); ?>">
                <?php endif; ?>
              </h3>
            </div>
            <div class="rating">
              <?php echo $this->itemRate($item->getType(), $item->getIdentity()); ?>
            </div>
            <div class="clr"></div>
            <div class="he-item-details">
              <?php if ($item->getCategory()->category) : ?>
                <span class="float_left"><?php echo $this->translate('STORE_Category') . ': '; ?>
                  &nbsp;</span>
                <span class="float_left"><?php
                  echo $this->htmlLink(
                    $item->getCategoryHref(array('action' => 'products')),
                    $item->getCategory()->category,
                    array()
                  )
                  ?></span><br/>
              <?php endif; ?>
              <?php if ($item->hasStore()): ?>
                <?php echo $this->translate('in %s store', $this->htmlLink($item->getStore()->getHref(), $item->getStore()->getTitle(), array('target' => '_blank'))); ?>
                <br/>
              <?php endif; ?>
            </div>
            <div class="he-item-desc">
              <?php echo $this->viewMore(Engine_String::strip_tags($item->getDescription()), 80) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="he-pagination">
      <?php echo $this->paginationControl($this->paginator, null, array("pagination/index.tpl", "store"), array(
        'storeAsQuery' => true,
        'query' => $this->formValues,
        'params' => $this->formValues,
      )); ?>
    </div>
  </div>

  <ul class="he-items store_icons_items" id="stores-icons"
      style="display:<?php echo ($this->view == 'icons') ? 'block' : 'none'; ?>;">
    <div class="store-pinfeed" id="store-pinfeed1"></div>
    <div class="store-pinfeed" id="store-pinfeed2"></div>
    <div class="store-pinfeed" id="store-pinfeed3"></div>

    <?php foreach ($this->paginator as $product): ?>
      <?php $ico = (null !== ($ico_tmp = $product->getPhotoUrl())) ? $ico_tmp : 'application/modules/Store/externals/images/nophoto_product_thumb_normal.png'; ?>
      <li class="item pin-item">
        <div class="icon_view" style="position: relative;">
          <img src="<?php echo $ico; ?>" onclick="openProduct('<?php echo $product->getHref(); ?>')">
          <a class="pin-quick-view" href="javascript://"
             onclick="showQuickView(<?php echo $product->getIdentity(); ?>);">
            <?php echo $this->translate("STORE_Quick View"); ?>
          </a>
        </div>
        <div class="item-body">
          <div class="store_browse">
            <div class="store_icon_title" id="view_icon_store_product_<?php echo $product->getIdentity() ?>">
              <h3>
                <a href="<?php echo $product->getHref(); ?>"
                   class="store_profile_title store_icons_items"
                   title="<?php echo $product->getTitle() ?>">
                  <?php echo $this->string()->truncate($product->getTitle(), 40, '...'); ?>
                </a>
              </h3>
            </div>
            <?php echo $this->getPriceBlock($product); ?>
          </div>
          <div class="product_amount">
            <?php if (!$product->isDigital()) : ?>
              <?php echo $this->translate(
                array('%s item available', '%s items available', (int)$product->getQuantity()),
                @$this->locale()->toNumber($product->getQuantity())); ?>
            <?php else : ?>
              &nbsp;
            <?php endif; ?>
            <div class="saf_product">
              <?php if ($product->sponsored) : ?>
                <img class="icon" src="application/modules/Store/externals/images/sponsored.png"
                     title="<?php echo $this->translate('STORE_Sponsored'); ?>">
              <?php endif; ?>
              <?php if ($product->featured) : ?>
                <img class="icon" src="application/modules/Store/externals/images/featured.png"
                     title="<?php echo $this->translate('STORE_Featured'); ?>">
              <?php endif; ?>
            </div>
          </div>
        </div>
      </li>
    <?php endforeach; ?>

    <script type="text/javascript">
      window.addEvent('domready', function () {
        var options = {
          autoResize: true, // This will auto-update the layout when the browser window is resized.
          container: $('store-pinfeed'),
          item: $$('.pin-item'),
          offset: 2,
          itemWidth: 255,
          bottom: 0

        };
        pinfeed_store(options);
      });
    </script>

    <?php if ($this->paginator->getPages()->current < $this->paginator->getPages()->next): ?>
      <div class="pagination">
        <div class="label">
          <a data-page="<?php echo $this->paginator->getPages()->next; ?>" href="javascript://"
             onclick="product_manager.loadMore()">
            View More
          </a>
        </div>
        <div class="loader" style="display: none;">
          <div class="icon">&nbsp;</div>
          <div class="text">
            <?php echo $this->translate('Loading ...') ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </ul>
<?php else: ?>
  <div class="tip"><span><?php echo $this->translate("STORE_There is no products."); ?></span></div>
<?php endif; ?>
