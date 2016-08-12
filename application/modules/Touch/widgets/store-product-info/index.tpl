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

<script type="text/javascript">
  var toCart = {
    options:'store-options',
    addButton:'add-to-cart',
    values:[],

    init: function(){
      this.options = $$('.'+this.options);
      this.addButton = $(this.addButton);

      if (this.options.length <= 0){
        this.addButton.removeClass('store-disabled');
      }
    },
    check:function(){
      var flag = true;
      var self = this;
      var i = 0;

      self.options.each(function($el){
        self.values[i] = {'label':$el.get('name'), 'value':$el.get('value')};
        i++;

        if ($el.get('value') == '-1'){
          flag = false;
        }
      });

      if (flag){
        self.addButton.removeClass('store-disabled');
      } else {
        self.addButton.addClass('store-disabled');
      }
    },
    add:function(id){
      var self = this;

      self.check();

      self.options.each(function($el){
        if ($el.get('value') == '-1'){
          $el.getNext().setStyle('display', 'inline');
        } else {
          $el.getNext().setStyle('display', 'none');
        }
      });

      if (self.addButton.hasClass('store-disabled')) {
        return false;
      };

      store_basket.product.add(id, null, this.values);
    }
  }

  en4.core.runonce.add( function() {
    toCart.init();

    var element = $$('.he-item-details')[0];
    var elements = element.getElements('ul>li>span');
    for(var i = 0; i < elements.length; i++){
      if (i%2 == 0)
        elements[i].innerHTML = elements[i].innerHTML.trim()+':';
      elements[i].setStyle('margin-right', 0);
      elements[i].innerHTML = elements[i].innerHTML.trim();
    }
  });
</script>

<ul class="he-item-list">
	<li>
    <div class="price product_info">
      <span>
      <?php echo $this->translate("price: "); ?>
      </span>
      <span>
      <?php echo $this->getPrice($this->product); ?>
      </span>
    </div>
		<div class="he-item-details product_info">
			<?php if( $this->product->hasStore() ): ?>
      <span>
        <?php echo $this->translate('STORE_Store').': '; ?>
      </span>
      <span>
        <?php echo $this->htmlLink($this->product->getStore()->getHref(), $this->product->getStore()->getTitle(), array('target' => '_blank')) ?>
      </span>
      <?php endif; ?>
      <span>
      <?php echo $this->translate('Posted').': '; ?>
      </span>
      <?php echo $this->timestamp($this->product->creation_date); ?>

			<?php if ( null != ($cat = $this->product->getCategory())): ?>
      <span>
				<?php echo $this->translate($cat->label) . ': '; ?>
      </span>
    <span>
				<?php echo ((null !== ($category = $cat->category)) ? $this->htmlLink($this->product->getCategoryHref(array('action' => 'products')), $this->translate($category), array()) : ("<i>".$this->translate("Uncategorized")."</i>")); ?>
    </span>
			<?php endif; ?>

      <?php echo $this->product->getInfo(); ?>
		</div>
    <div class="he-item-desc product_description">
  			<?php echo $this->product->description ?>
  		</div>

    <?php if( $this->allowOrder ): ?>
      <?php if (isset($this->product->additional_params) && count($this->product->additional_params) > 0): ?>
        <div class="adding-block">
          <table>
          <?php foreach($this->product->additional_params as $param): $options = (isset($param['options']))?explode(',', $param['options']):array();?>
          <tr>
            <td><?php echo $param['label']; ?>:&nbsp;&nbsp;</td>
            <td class="options">
            <select name="<?php echo $param['label']; ?>" onchange="toCart.check()" class="store-options">
              <option value='-1'><?php echo $this->translate('STORE_-Select-'); ?></option>

              <?php foreach( $options as $option):?>
                <option value='<?php echo trim($option); ?>'> <?php echo trim($option); ?></option>
              <?php endforeach; ?>

            </select>
            &nbsp;<span class="select-error">&larr;<?php echo $this->translate('STORE_Select a %1$s', $param['label']); ?></span>
            </td>
          </tr>
          <?php endforeach; ?>
          <tr>
            <td><?php // echo $this->translate('STORE_Price'); ?>&nbsp;&nbsp;</td>
            <td class="options">
              <button onclick="toCart.add(<?php echo $this->product->getIdentity(); ?>)" class="store-disabled" id='add-to-cart'>
                <span class="store-add-button product_button"><?php echo $this->translate('STORE_Add to Cart'); ?></span>
              </button>
            </td>
          </tr>
          </table>
        </div>
      <?php else: ?>
      <br/>
      <button onclick="toCart.add(<?php echo $this->product->getIdentity(); ?>)" class="store-disabled" id='add-to-cart'>
        <span class="store-add-button product_button"><?php echo $this->translate('STORE_Add to Cart'); ?></span>
      </button>
      <?php endif; ?>
    <?php endif; ?>

	</li>
</ul>
