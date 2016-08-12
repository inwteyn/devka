    <ul class="offer_store_product_list">
        <?php foreach ($this->products as $product): ?>
            <li class="offer_store_product_item">
                <div class="offer_store_product_photo">
                    <?php echo $this->itemPhoto($product, 'thumb.normal'); ?>
                </div>
            <span class="offer_store_product_title">
                <?php echo $product->title; ?>
            </span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php if (count($this->products) > 4) { ?>
    <div id="offer_store_product_more" onclick="showMoreProducts()">
        <?php echo $this->translate('OFFERS_offer_show_all'); ?>
        <i class="hei hei-chevron-down"></i></div>
    <div id="offer_store_product_less" onclick="showLessProducts()" style="display: none">
        <?php echo $this->translate('OFFERS_offer_show_less'); ?>
        <i class="hei hei-chevron-up"></i></div>
<?php } ?>