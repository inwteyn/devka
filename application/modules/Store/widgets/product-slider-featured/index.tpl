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

$this->headScript()
    ->appendFile('application/modules/Store/externals/scripts/sl_slider.js');
?>

<script type="text/javascript">
    window.addEvent('domready', function () {

        //slider variables for making things easier below
        var p_f_itemsHolder = $('product_slider_container_f');
        var p_f_myItems = $$(p_f_itemsHolder.getElements('.item'));

        //controls for slider
        var p_f_thePrevBtn = $(p_f_itemsHolder.getElement('.prev_btn'));
        var p_f_theNextBtn = $(p_f_itemsHolder.getElement('.next_btn'));

        if(p_f_thePrevBtn) {
            //create instance of the slider, and start it up
            var p_f_product_slider = new SL_Slider({
                slideTimer: 5000,
                orientation: 'horizontal',      //vertical, horizontal, or none: None will create a fading in/out transition.
                fade: true,                    //if true will fade the outgoing slide - only used if orientation is != None
                isPaused: false,
                container: p_f_itemsHolder,
                items: p_f_myItems,
                prevBtn: p_f_thePrevBtn,
                nextBtn: p_f_theNextBtn
            });
            p_f_product_slider.start();

            $$('.layout_store_product_slider_featured').addEvent('mouseenter', function () {
                clearTimeout(window.start);
                if (!p_f_product_slider.options.isPaused) {
                    p_f_product_slider.isSliding = 0;
                    p_f_product_slider.pauseIt();
                }
            });
            $$('.layout_store_product_slider_featured').addEvent('mouseleave', function () {
                window.start = setTimeout(function () {
                    p_f_product_slider.isSliding = 0;
                    p_f_product_slider.pauseIt();
                    clearTimeout(window.start);
                }, 1000);
            });
        }

    });
</script>

<div id="product_slider_container_f" class="product_slider_container">


    <?php foreach ($this->products as $product): ?>
        <div class="item">
            <div class="slider_item_img">
                <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.normal'), array('class' => 'slider_img')); ?>
            </div>
            <div class="content_info">
                <h3>
                    <?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($product->getTitle(), 40, '...'), array('title' => $product->getTitle())); ?>
                </h3>

                <div class="descr">
                    <?php echo $this->string()->truncate(str_replace('<br />', ' ', $product->getDescription()), 150, '...'); ?>
                </div>

                <div class="clr"></div>

                <div class="rating">
                    <?php echo $this->itemRate('store_product', $product->getIdentity()); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (count($this->products) < 2) { ?>
        <div id="controls">
            <div class="prev_btn"><i class="hei hei-angle-left hei-5x"></i></div>
            <div class="next_btn"><i class="hei hei-angle-right hei-5x"></i></div>
        </div>
    <?php } ?>

</div>