<?php

    $viewer = Engine_Api::_()->user()->getViewer();
    //$allowOrder = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order');
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $allowFree = $settings->getSetting('store.free.products', 0);
    $allowOrder = Engine_Api::_()->store()->allowOrder($viewer);
    $item_id = $this->item->getIdentity();
?>

<div class="store-price-block">
    <div style="float: left; /*padding-top: 5px;*/">
        <?php echo $this->view->getPrice($this->item); ?>
    </div>
    <div style="float: right;">
        <?php if (!$this->item->isWished()) {
            $class = 'store-add-wish-list-button he-glyphicon-heart-empty';
            $onclick = "store_cart.product.addToWishList(1, " . $item_id . ")";
        } else {
            $class = 'store-remove-wish-list-button he-glyphicon-heart';
            $onclick = "store_cart.product.removeFromWishList(1, " . $item_id . ")";
        } ?>
        <?php if($this->viewer()->getIdentity()): ?>
            <a
                href="javascript:void(0)"
                onclick="<?php echo $onclick; ?>"
                class="<?php echo $class; ?> wishlist_button wishlist-button-<?php echo $item_id; ?> he-glyphicon"
                id='add-to-wish-list-<?php echo $item_id; ?>'>
            </a>
          <?php
          $wcount = Engine_Api::_()->store()->getWishesCount($item_id);
          ?>
          <span class="wl-count wl-count-<?php echo $item_id ?>" <?php if(!$wcount){ ?> style="display: none" <?php } ?>><?php echo $wcount; ?></span>
        <?php endif; ?>
        <?php if($allowOrder): ?>
            <?php if($this->item->isFree() && $allowFree): ?>
                <a class="download he-glyphicon he-glyphicon-download" href="<?php echo $this->url(array('id'=>$item_id), 'store_download_free'); ?>" onclick="">
                    <span class=""></span>
                </a>
            <?php else: ?>
                <a class="add he-glyphicon he-glyphicon-shopping-cart" href="javascript://" onclick="product_manager.showAddingBlock('<?php echo $item_id; ?>');">
                    <span class=""></span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div style="clear: both;"></div>
</div>