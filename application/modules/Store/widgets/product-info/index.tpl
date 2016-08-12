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

<div>

<script type="text/javascript">
    var toCart = {
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
                    $('select-error').setStyle('display', 'inline');
                } else {
                  $('select-error').setStyle('display', 'none');
                }
            });

            if ($('quantity') != undefined) {
                var $quantity = parseInt($('quantity').value);
                if (!$quantity) {
                    flag = false;
                    //$('quantity').getNext().setStyle('display', 'inline');
                } else {
                    self.quantity = $quantity;
                    //$('quantity').getNext().setStyle('display', 'none');
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

            store_cart.product.add(false, id, this.values, this.quantity);
        },
        remove: function ($product_id, $item_id) {
            store_cart.product.remove(false, $product_id, $item_id);
        }
    }

    en4.core.runonce.add(function () {
        store_cart.profile_widget_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'content_id' => $this->identity, 'product_id' => $this->product->getIdentity()), 'default', true); ?>';
        toCart.init();

        var element = $$('.he-item-details')[0];
        var elements = element.getElements('ul>li>span');
        for (var i = 0; i < elements.length; i++) {
            if (i % 2 == 0)
                elements[i].innerHTML = elements[i].innerHTML.trim() + ':';
            elements[i].setStyle('margin-right', 0);
            elements[i].innerHTML = elements[i].innerHTML.trim();
        }
    });
</script>

<ul class="he-item-list product_profile_details"
    id="store-product-profile-details-<?php echo $this->product->getIdentity() ?>">
    <li>

        <?php if ($this->allowOrder): ?>
            <div style="float: left; width: 100%;">
                <div class="adding-block">
                    <?php if($this->product->isFree()): ?>
                        <?php echo $this->getPriceBlock($this->product); ?>
                    <?php else : ?>
                        <table width="100%">
                            <?php $hasOptions = (is_array($this->product->params) && count($this->product->params));
                            if ($hasOptions): ?>
                                <?php foreach ($this->product->params as $param): $options = (isset($param['options'])) ? explode(',', $param['options']) : array(); ?>
                                    <tr>
                                        <td><?php echo $param['label']; ?>:&nbsp;&nbsp;</td>
                                        <td class="options">
                                            <select name="<?php echo $param['label']; ?>" onchange="toCart.check()"
                                                    class="store-options">
                                                <option
                                                    value='-1'><?php echo $this->translate('STORE_-Select-'); ?></option>

                                                <?php foreach ($options as $option): ?>
                                                    <option
                                                        value='<?php echo trim($option); ?>'> <?php echo trim($option); ?></option>
                                                <?php endforeach; ?>

                                            </select>
                                            &nbsp;<span id="select-error"
                                                class="select-error">&larr;<?php echo $this->translate('STORE_Select a %1$s', $param['label']); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <span class="product_profile_price"><?php echo $this->getPrice($this->product); ?></span>

                            <?php if (!$this->product->isAddedToCart() || $hasOptions): ?>
                                <?php if ($this->product->type == 'simple') : ?>
                                    <tr>
                                        <td><?php echo $this->translate('STORE_Quantity'); ?>:&nbsp;&nbsp;</td>
                                        <td class="options">
                                            <input type="text" name="quantity" id="quantity" onchange="toCart.check()" style="width: 53px" value="1">
                                        </td>
                                    </tr>
                                <tr>
                                  <td style="font-size:9pt;" colspan="2">
                                    <?php echo $this->translate(
                                      array('%s item available', '%s items available', (int)$this->product->getQuantity()),
                                      @$this->locale()->toNumber($this->product->getQuantity())); ?>
                                    <span class="select-error">&larr;<?php echo $this->translate('STORE_Enter a %1$s', $this->translate('STORE_Quantity')); ?></span>
                                  </td>
                                </tr>


                                <?php endif; ?>

                                <tr>
                                    <td class="options" id="add-to-cart-button" colspan="2">
                                        <button onclick="toCart.add(<?php echo $this->product->getIdentity(); ?>)"
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
                                            onclick="toCart.remove(<?php echo $this->product->getIdentity(); ?>, <?php echo $this->item_id ?>)"
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

<div class="he-item-desc product_profile_desc">
    <?php
     echo $this->heViewMore($this->product->description); ?>
</div>

</div>
