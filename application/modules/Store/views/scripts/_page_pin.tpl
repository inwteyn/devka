<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _page_list.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/scripts/pinfeed.js'); ?>

<?php if ($this->products->getTotalItemCount() > 0): ?>

    <ul class="he-items store_icons_items" id="stores-icons">
        <div class="store-pinfeed" id="store-pinfeed1"></div>
        <div class="store-pinfeed" id="store-pinfeed2"></div>
        <div class="store-pinfeed" id="store-pinfeed3"></div>

        <?php foreach ($this->products as $product): ?>
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
                        <div class="store_icon_title"
                             id="view_icon_store_product_<?php echo $product->getIdentity() ?>">
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
                function pinProducts() {
                  if(window.domready_for_pin_store == 1){
                    return;
                  }

                  var options = {
                    autoResize: true, // This will auto-update the layout when the browser window is resized.
                    container: $('store-pinfeed'),
                    item: $$('.pin-item'),
                    offset: 2,
                    itemWidth: 255,
                    bottom: 0,
                    page: 1
                  };
                  setTimeout(function(){
                    pinfeed_store(options);
                    window.domready_for_pin_store = 1;
                  },500);
                }
                if($$('.tab_layout_store_page_profile_products').length){
                    $$('.tab_layout_store_page_profile_products').addEvent('click',function() {
                        pinProducts();
                    });
                }
                pinProducts();
            });
        </script>

    </ul>
    <?php if ($this->totalpages > 1): ?>
        <a href="javascript:void(0)" onclick="paging2.setPage()" data-page="2"  isDisplay="1" id="getViewMorePr" max-page="<?php echo $this->totalpages;?>"><?php echo $this->translate('More ');?><i class="hei hei-chevron-down"></i></a>
        <script type="text/javascript">

            var paging2 = {
                page_num : 1,
                widget_url: '',
                page_id: 0,

                getProducts : function() {
                    if(window.getproductRequest == 1) return;
                    window.getproductRequest = 1;
                    var self = this;

                    if ($('store_page_loader')) {
                        $('store_page_loader').removeClass('hidden');
                    }

                    new Request.JSON({
                        url:self.widget_url+'/?p='+self.page_num+'&page_id='+self.page_id+'&ipp='+self.ipp,
                        method: 'post',
                        data:
                        {
                            'format': 'json'
                        },
                        eval: true,
                        onSuccess : function(responseJSON)
                        {
                            if ($('store_page_loader')) {
                                $('store_page_loader').addClass('hidden');
                            }

                            var tElement = new Element('div', {'html': responseJSON.html});
                            var options = {
                                autoResize: true, // This will auto-update the layout when the browser window is resized.
                                container: $('store-pinfeed'),
                                item: tElement.getElements('.pin-item'),
                                offset: 2,
                                itemWidth: 255,
                                bottom: 0
                            };
                            pinfeed_store(options);
                            $('getViewMorePr').set('data-page',self.page_num.toInt()+1);
                            window.getproductRequest = 0;
                        }
                    }).send();
                },

                setPage : function() {
                    var buttonmore= $('getViewMorePr');
                    if(buttonmore.get('data-page').toInt() >= buttonmore.get('max-page')){
                        buttonmore.set('isDisplay',0);
                        buttonmore.hide();
                    }
                    var page = $('getViewMorePr').get('data-page');
                    if (page != undefined) this.page_num = page;
                    this.getProducts();

                }
            };

            paging2.widget_url = '<?php echo $this->url(array('controller' => 'page'), 'store_extended', true); ?>';
            paging2.page_id = '<?php echo $this->page->getIdentity(); ?>';
            paging2.ipp = '<?php echo $this->ipp; ?>';

            window.addEvent('scroll', function () {
                if (window.getScrollTop() + 5 >= window.getScrollSize().y - window.getSize().y) {
                    if($('getViewMorePr').get('isDisplay') == 1 && $$('.tab_layout_store_page_profile_products').hasClass('active')[0]){
                        $('getViewMorePr').click();
                    }
                }
            });

        </script>
    <?php endif; ?>
<?php else: ?>


    <div class="tip">
      <span>
        <?php echo $this->translate('There no products added yet.') . ' '; ?>
        <?php if ($this->page->getStorePrivacy()): ?>
            <?php echo $this->htmlLink(array(
                    'route' => 'store_products',
                    'action' => 'create',
                    'page_id' => $this->page->getIdentity()),
                $this->translate('STORE_Create Product.')); ?>
        <?php endif; ?>
      </span>
    </div>

<?php endif; ?>