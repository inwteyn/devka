<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-11-10 17:53 taalay $
 * @author     Taalay
 */

$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Page/externals/scripts/sl_slider.js');
?>

<script type="text/javascript">
    window.addEvent('domready', function () {

        //slider variables for making things easier below
        var itemsHolder = $('slider_container');
        var myItems = $$(itemsHolder.getElements('.item'));

        //controls for slider
        var thePrevBtn = $(itemsHolder.getElement('.prev_btn'));
        var theNextBtn = $(itemsHolder.getElement('.next_btn'));

        if(theNextBtn) {
            //create instance of the slider, and start it up
            var mySlider = new SL_Slider({
                slideTimer: 5000,
                orientation: 'horizontal',      //vertical, horizontal, or none: None will create a fading in/out transition.
                fade: true,                    //if true will fade the outgoing slide - only used if orientation is != None
                isPaused: false,
                container: itemsHolder,
                items: myItems,
                prevBtn: thePrevBtn,
                nextBtn: theNextBtn
            });
            mySlider.start();

            $$('.layout_page_sponsored_carousel').addEvent('mouseenter', function () {
                clearTimeout(window.start);
                if (!mySlider.options.isPaused) {
                    mySlider.isSliding = 0;
                    mySlider.pauseIt();
                }
            });
            $$('.layout_page_sponsored_carousel').addEvent('mouseleave', function () {
                window.start = setTimeout(function () {
                    mySlider.isSliding = 0;
                    mySlider.pauseIt();
                    clearTimeout(window.start);
                }, 1000);
            });
        }
    });

</script>

<div id="slider_container" class="slider_container">

    <?php foreach ($this->pages as $page): ?>
        <div class="item">
            <div class="slider_item_img">
                <?php echo $this->htmlLink($page->getHref(), $this->itemPhoto($page, 'thumb.normal'), array('class' => 'slider_img')); ?>
            </div>
            <div class="content_info">
                <h3>
                    <?php echo $this->htmlLink($page->getHref(), $this->string()->truncate($page->getTitle(), 50, '...'), array('title' => $page->getTitle())); ?>
                </h3>
                <div class="descr">
                    <?php echo $this->string()->truncate(str_replace('<br />', ' ', $page->getDescription()), 250, '...'); ?>
                </div>

                <div class="clr"></div>

                <div class="rating">
                    <?php echo $this->itemRate('page', $page->getIdentity()); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (count($this->pages) < 2) { ?>
        <div id="controls">
            <div class="prev_btn"><i class="hei hei-angle-left hei-5x"></i></div>
            <div class="next_btn"><i class="hei hei-angle-right hei-5x"></i></div>
        </div>
    <?php } ?>

</div>


<script type="text/javascript">
    en4.core.runonce.add(function () {
        $$('.item .pagereview_count').setStyle('display', 'none');
        var miniTipsOptions = {
            'htmlElement': '.pagereview_count',
            'delay': 1,
            'className': 'he-tip-mini',
            'id': 'he-mini-tool-tip-id',
            'ajax': false,
            'visibleOnHover': false
        };

        var internalTips = new HETips($$('.item .pagereview_element'), miniTipsOptions);
    });
</script>