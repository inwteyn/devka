
<?php if ($this->allowOrder): ?>
    <script type="text/javascript">
        window.detailsToCart = {
            options: 'store-options',
            addButton: 'add-to-cart',
            values: [],
            quantity: 1,

            init: function () {
                this.options = $$('.' + this.options);
                this.addButton = $(this.addButton);

                if (this.options.length <= 0) {
                    this.addButton.removeClass('store-disabled');
                }
            },
            check: function () {
                var flag = true;
                var self = this;
                var i = 0;

                self.options.each(function ($el) {
                    self.values[i] = {'label': $el.get('name'), 'value': $el.get('value')};
                    i++;

                    if ($el.get('value') == '-1') {
                        flag = false;
                        $el.getNext().setStyle('display', 'inline');
                    } else {
                        $el.getNext().setStyle('display', 'none');
                    }
                });

                if ($('quantity') != undefined) {
                    var $quantity = parseInt($('quantity').value);
                    if (!$quantity) {
                        flag = false;
                        $('quantity').getNext().setStyle('display', 'inline');
                    } else {
                        self.quantity = $quantity;
                        $('quantity').getNext().setStyle('display', 'none');
                    }
                }

                if (flag) {
                    self.addButton.removeClass('store-disabled');
                } else {
                    self.addButton.addClass('store-disabled');
                }
            },
            add: function (id) {
                var self = this;
                self.check();

                if (self.addButton.hasClass('store-disabled')) {
                    return false;
                }

                store_cart.product.add(true, id, this.values, this.quantity);
            },
            remove: function ($product_id, $item_id) {
                store_cart.product.remove(true, $product_id, $item_id);
            }
        };

        detailsToCart.init();

        var element = $$('.he-item-details')[0];
        var elements = element.getElements('ul>li>span');
        for (var i = 0; i < elements.length; i++) {
            if (i % 2 == 0)
                elements[i].innerHTML = elements[i].innerHTML.trim() + ':';
            elements[i].setStyle('margin-right', 0);
            elements[i].innerHTML = elements[i].innerHTML.trim();
        }

        en4.core.runonce.add(function () {
        });
    </script>

    <div class="adding-block">
        <table>
            <?php $hasOptions = (is_array($this->item->params) && count($this->item->params));
            if ($hasOptions): ?>
                <?php foreach ($this->item->params as $param): $options = (isset($param['options'])) ? explode(',', $param['options']) : array(); ?>
                    <tr>
                        <td><?php echo $param['label']; ?>:&nbsp;&nbsp;</td>
                        <td class="options">
                            <select name="<?php echo $param['label']; ?>" onchange="detailsToCart.check()"
                                    class="store-options">
                                <option value='-1'><?php echo $this->translate('STORE_-Select-'); ?></option>

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

            <?php if (!$this->item->isAddedToCart() || $hasOptions): ?>
                <?php if ($this->item->type == 'simple') : ?>
                    <tr>
                        <td><?php echo $this->translate('STORE_Quantity'); ?>:&nbsp;&nbsp;</td>
                        <td class="options">
                            <input type="text" name="quantity" id="quantity" onchange="detailsToCart.check()"
                                   style="width: 53px" value="1">
                            &nbsp;&nbsp;&nbsp;<?php echo $this->translate(
                                array('%s item available', '%s items available', (int)$this->item->getQuantity()),
                                $this->locale()->toNumber($this->item->getQuantity())); ?>
                            &nbsp;<span
                                class="select-error">&larr;<?php echo $this->translate('STORE_Enter a %1$s', $this->translate('STORE_Quantity')); ?></span>
                        </td>
                    </tr>


                <?php endif; ?>

                <tr>
                    <td class="options" id="add-to-cart-button" colspan="2">
                        <span class="product_profile_price"><?php echo $this->getPrice($this->item); ?></span>
                        <button onclick="detailsToCart.add(<?php echo $this->item->getIdentity(); ?>)"
                                class="store-disabled" id='add-to-cart'>
                                <span
                                    class="store-add-button product_button"><?php echo $this->translate('STORE_Add to Cart'); ?></span>
                        </button>
                    </td>
                </tr>

            <?php else : ?>
                <tr>
                    <td class="options" id="add-to-cart-button" colspan="2">
                        <span class="product_profile_price"><?php echo $this->getPrice($this->item); ?></span>
                        <button
                            onclick="detailsToCart.remove(<?php echo $this->item->getIdentity(); ?>, <?php echo $this->item_id ?>)"
                            id='add-to-cart'>
                                <span
                                    class="store-remove-button product_button"><?php echo $this->translate('STORE_Remove from Cart'); ?></span>
                        </button>
                    </td>
                </tr>
            <?php endif; ?>

            <!--<tr>
                <?php /*if (!$this->item->isWished()) : */?>
                    <td class="options" id="add-to-wish-list-button" colspan="2">
                            <span class="product_profile_price"
                                  style="visibility:hidden;"><?php /*echo $this->getPrice($this->item); */?></span>
                        <a
                            href="javascript:void(0)"
                            class="store-add-wish-list-button wishlist_button"
                            onclick="store_cart.product.addToWishList(true, <?php /*echo $this->item->getIdentity(); */?>)"
                            id='add-to-wish-list'>
                            <?php /*echo $this->translate('STORE_Add to Wishlist'); */?>
                        </a>
                    </td>
                <?php /*else : */?>
                    <td class="options" id="add-to-wish-list-button" colspan="2">
                            <span class="product_profile_price"
                                  style="visibility:hidden;"><?php /*echo $this->getPrice($this->item); */?></span>
                        <a
                            href="javascript:void(0)"
                            class="store-remove-wish-list-button wishlist_button"
                            onclick="store_cart.product.removeFromWishList(true, <?php /*echo $this->item->getIdentity(); */?>)"
                            id='add-to-wish-list'>
                            <?php /*echo $this->translate('STORE_In Wishlist'); */?>
                        </a>
                    </td>
                <?php /*endif; */?>
            </tr>-->
        </table>
    </div>
<?php else: ?>
    Order is not allowed
<?php endif; ?>