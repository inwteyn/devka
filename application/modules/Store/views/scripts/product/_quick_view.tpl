<script type="text/javascript">
var quickToCart = {
  options: 'store-options',
  addButton: 'add-to-cart',
  values: [],
  quantity: 1,

  init: function () {
    this.options = $$('.store-options');
    this.addButton = $('add-to-cart');
    if (this.options.length <= 0 && this.addButton) {
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
  add: function (id, item_id, el) {
    var self = this;
    self.init();
    self.check();

    if (self.addButton.hasClass('store-disabled')) {
      return false;
    }
    var options = {};
    options.el = el;
    options.item_id = item_id;
    options.text = '<?php echo $this->translate('STORE_Remove from Cart'); ?>';
    store_cart.product.add(2, id, this.values, this.quantity, options);
  },
  remove: function ($product_id, $item_id, el) {
    this.init();
    var options = {};
    options.el = el;
    options.item_id = $item_id;
    options.text = '<?php echo $this->translate('STORE_Add to Cart'); ?>';
    store_cart.product.remove(2, $product_id, $item_id, options);
  }
}

en4.core.runonce.add(function () {
  $('product-quick-screen').addEvent('click', function () {
    hideQuickViewWindow();
  });

  quickToCart.init();

  var element = $$('.he-item-details')[0];
  var elements = element.getElements('ul>li>span');
  for (var i = 0; i < elements.length; i++) {
    if (i % 2 == 0)
      elements[i].innerHTML = elements[i].innerHTML.trim() + ':';
    elements[i].setStyle('margin-right', 0);
    elements[i].innerHTML = elements[i].innerHTML.trim();
  }
});

function changeImage(src) {
  var preview = $('quick-preview');

  var previewSrc = preview.get('src');

  if(previewSrc == src) {
    return;
  }

  preview.set('width', '');
  preview.set('height', '');

  preview.set('src', src);
  preview.setStyle('display', 'none');

  preview.addEvent('load', function () {
    resizeImage(preview);
  });
}

function resizeImage(img) {
  var w1 = img.getParent().getParent().getWidth();
  var h1 = img.getParent().getHeight();

  var w = img.width;
  var h = img.height;

  var r = w / h;

  var h3 = 400;
  var w3 = h3 * r;

  if (w3 > w1) {
    w3 = w1;
    h3 = w1 / r;
  }

  img.set('width', w3);
  img.set('height', h3);

  img.setStyle('display', '');
}

function showQuickViewWindow() {
  $(document.html).setStyle('overflow-y', 'hidden');
  var screen = $('product-quick-screen');
  var container = $('product-quick-view-wrapper');

  screen.setStyle('display', 'block');
  setTimeout(function () {
    screen.setStyle('opacity', .7);

    container.setStyle('display', 'block');
    setTimeout(function () {
      container.setStyle('opacity', 1);
    }, 10);

  }, 10);

  var Loader = new Element('div', {
    class: 'product-quick-popup-loader product-quick-popup-loader-animation'
  });

  container.grab(Loader);
}

function hideQuickViewWindow() {
  $(document.html).setStyle('overflow-y', 'scroll');
  var screen = $('product-quick-screen');
  var container = $('product-quick-view-wrapper');

  container.setStyle('opacity', 0);
  container.setStyle('display', 'none');
  container.set('html', '');

  screen.setStyle('opacity', 0);
  setTimeout(function () {
    screen.setStyle('display', 'none');
  }, 500);
}

function showQuickView(id) {
  showQuickViewWindow();

  var container = $('product-quick-view-wrapper');
  var url = '<?php echo $this->url(array('controller' => 'product', 'action'=>'quick'), 'store_extended', 1); ?>';
  new Request.HTML({
    url: url,
    data: {
      format: 'html',
      product_id: id
    },
    evalScripts: false,
    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
      container.set('html', responseHTML);
      quickToCart.init();
      eval(responseJavaScript);
    },
    onFailure:function(err) {
      hideQuickViewWindow();
    }
  }).send();
}


</script>
<div class="product-quick-popup product-quick-view-wrapper" id="product-quick-view-wrapper"></div>
<div class="product-quick-popup product-quick-screen" id="product-quick-screen"></div>