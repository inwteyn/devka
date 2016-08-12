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
?>
<style type="text/css">
  .product-profile-product-info {
    float: left;
    width: 70%;
    padding-top: 20px;
  }

  .product-profile-store-info {
    float: right;
    padding-top: 20px;
  }
  .product-profile-store-info table td h3 {
    margin-bottom: 0;
    font-size: 14px;
  }
  .product-profile-store-info table td img.thumb_normal {
    max-height: 50px;
  }

  .product-profile-store-info .rate_style {
    font-size: 15px;
  }
  .product-profile-store-info .pagereview_element {
    width: 75px !important;
  }
  .product-profile-store-info .pagereview_count {
    font-size: 11px !important;
  }
  .product-profile-product-info h2 img {
    vertical-align: bottom;
  }
  .product-profile-product-info .like_button_container .like_button_link  {
    padding: 4px 7px;
  }
  #global_page_store-product-index .like_container_menu_wrapper {
    padding-top: 2px;
    padding-left: 10px;
  }
</style>

<script type="text/javascript">
  window.addEvent('load', function () {
    var $tabs = $('main_tabs');
    if ($tabs != undefined) {
      var $li = $tabs.getElementsByTagName('li')[0];
      var $a = $li.getElementsByTagName('a')[0];
      tabContainerSwitch($a);
    }
  });
</script>
<div class="product-profile-product-info">
  <h2>
    <?php echo('' != trim($this->product->getTitle()) ? $this->product->getTitle() : '<em>' . $this->translate('Untitled') . '</em>'); ?>
    <?php if ($this->product->sponsored) : ?>
      <img class="icon" src="application/modules/Store/externals/images/sponsoredBig.png"
           title="<?php echo $this->translate('STORE_Sponsored'); ?>">
    <?php endif; ?>
    <?php if ($this->product->featured) : ?>
      <img class="icon" src="application/modules/Store/externals/images/featuredBig.png"
           title="<?php echo $this->translate('STORE_Featured'); ?>">
    <?php endif; ?>
    <?php echo ($this->isLike) ? $this->likeButton($this->product) : ''; ?>
  </h2>
  <?php echo ($this->isRate) ? $this->quickProductRate($this->product, true) : ''; ?>
</div>
<div class="product-profile-store-info">
  <?php if (null != ($store = $this->product->getStore())) : ?>
    <table>
      <tr>
        <td>
          <?php echo $this->htmlLink($store->getHref(), $this->itemPhoto($store, 'thumb.normal')); ?>
        </td>
        <td style="padding-left: 10px; vertical-align: top;">
          <h3>
            <?php echo $this->htmlLink($store->getHref(), $store->getTitle()); ?>
          </h3>

          <div class="rating">
            <?php echo $this->itemRate('page', $store->getIdentity()); ?>
            <div class="page_list_submitted" >
              <?php echo $store->view_count ?> <?php echo $this->translate("views"); ?>
            </div>
          </div>
        </td>
      </tr>
    </table>
  <?php endif; ?>
</div>
<div style="clear:both;"></div>