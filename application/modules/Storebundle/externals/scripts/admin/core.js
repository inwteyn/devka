/**
 * Created by asmproger on 14.07.15.
 */

var StorebundleCore = {

  products: null,
  productsCount: 0,
  completerUrl: '',
  createUrl: '',
  enableUrl: '',
  editUrl: '',
  deleteUrl: '',
  deleteProductUrl: '',
  listUrl: '',
  wrapperId: 'storebundle-content-wrapper',
  wrapper: {},
  completerWrapper: null,
  completerContainer: {},
  previewsContainer: null,

  ids: [],
  items: [],

  init: function () {
    var self = this;
    self.wrapper = $('storebundle-content-wrapper');
  },

  initCompleter: function () {
    var self = this;
    self.previewsContainer = $('products_previews-element');
    self.completerContainer = $('product-element');
    self.completerWrapper = new Element('div', {'class': 'storebundle-completer-wrapper'});
    self.completerWrapper.inject(self.completerContainer);
    self.hideCompleter();
  },
  buildCompleter: function (items) {
    var self = this;
    self.completerWrapper.set('html', '');
    for (var i = 0; i < items.length; i++) {
      var item = self.createCompleterItem(items[i]);
      item.inject(self.completerWrapper);
    }
    self.updatePrices();
  },
  createCompleterItem: function (item) {
    var self = this;
    var element = new Element('div', {'class': 'storebundle-completer-item', 'data-id': item.id,
      'data-title': item.title, 'data-image': item.image, 'data-price': item.price});

    var imgW = new Element('div', {'class': 'item-wrapper'});
    var img = new Element('img', {'src': item.image, 'class': 'item-image'});

    var textW = new Element('div', {'class': 'item-wrapper item-title', 'html': item.title});

    var removeW = new Element('div', {'class': 'item-wrapper hei hei-times item-remove', 'data-id': item.id});
    if (!self.ids.contains(item.id + '')) {
      removeW.setStyle('display', 'none');
    }

    img.inject(imgW);
    imgW.inject(element);
    textW.inject(element);
    removeW.inject(element);

    element.addEventListener('click', function (e) {
      if ($(e.target).hasClass('hei')) {
        return;
      }
      var el = $(this);

      if (self.ids.contains($(el).get('data-id'))) {
        return;
      }

      self.addProductId(el);

      var params = {
        id: $(el).get('data-id'),
        image: $(el).get('data-image'),
        title: $(el).get('data-title'),
        price: $(el).get('data-price')
      };

      self.createProductPreview(params);
      //self.createProductPreview(el);
    });

    removeW.addEventListener('click', function (e) {
      var el = $(this);
      if (self.removeProductId(el.get('data-id'))) {
        el.setStyle('display', 'none');
        $('preview-' + el.get('data-id')).remove();
      }
    });
    return element;
  },

  updatePrices: function () {
    var discount = new Number($('percent').value.trim());
    if (isNaN((discount)) || discount < 0) {
      discount = 0;
    }
    if (discount > 99) {
      discount = 99;
      $('percent').value = 99;
    }

    var oldPrices = $$('.preview-old-price');
    var newPrices = $$('.preview-new-price');
    for (var i = 0; i < oldPrices.length; i++) {
      var oP = oldPrices[i];
      var oPrice = new Number(oP.get('html'));
      if (isNaN((oPrice)) || oPrice < 0) {
        oPrice = 0;
      }
      if (!isNaN(oPrice) && oPrice >= 0) {
        var d = oPrice * discount / 100;
        if (d <= 0) {
          if (oldPrices[i].hasClass('active')) {
            oldPrices[i].removeClass('active');
          }
          newPrices[i].set('html', '');
        } else {
          if (!oldPrices[i].hasClass('active')) {
            oldPrices[i].addClass('active');
          }
          var nPrice = new Number(oPrice - d);
          nPrice = nPrice.toFixed(2);
          newPrices[i].set('html', nPrice);
        }
      }
    }
  },

  createProductPreview: function (params) {
    var self = this;
    var div = new Element('div', {'class': 'storebundle-preview-wrapper', 'id': 'preview-' + params.id,
      'data-id': params.id});
    var img = new Element('img', {'src': params.image});
    var imgWrapper = new Element('div');
    img.inject(imgWrapper);

    var title = new Element('div', {'html': params.title, 'class': 'preview-title'});
    var oldPrice = new Element('span', {'html': params.price, 'class': 'preview-old-price'});

    var newPrice = new Element('span', {'class': 'preview-new-price'});
    var priceWrapper = new Element('div');

    title.inject(priceWrapper);
    oldPrice.inject(priceWrapper);
    newPrice.inject(priceWrapper);

    var removeW = new Element('div', {'class': 'preview-remove hei hei-times', 'data-id': params.id});
    removeW.addEventListener('click', function () {
      if (self.removeProductId(params.id)) {
        self.request(
          {product_id:params.id, format:'json'},
          self.deleteProductUrl,
          function() {
            div.remove();
          }
        );
      }
    });
    imgWrapper.inject(div);
    priceWrapper.inject(div);
    removeW.inject(div);
    div.inject(self.previewsContainer);
    self.updatePrices();
  },
  addProductId: function (el) {
    var self = this;
    var id = (el.get('data-id'));
    if (isNaN(id) || id <= 0 || !isFinite(id)) {
      return;
    }
    if (!self.ids.contains(id)) {
      self.ids.push(id);
      el.getElement('div.item-remove').setStyle('display', 'block');
      return true;
    }
    return false;
  },
  removeProductId: function (id) {
    var self = this;
    if (isNaN(id) || id <= 0 || !isFinite(id)) {
      return false;
    }

    var index = self.ids.indexOf(id+'');
    if (index >= 0) {
      self.ids.splice(index, 1);
      return true;
    }
    return false;
  },
  showCompleter: function () {
    var self = this;
    self.completerWrapper.setStyle('display', 'block');
  },
  hideCompleter: function () {
    var self = this;
    if (self.completerWrapper == null) {
      return;
    }
    self.completerWrapper.set('html', '');
    self.completerWrapper.setStyle('display', 'none');
  },


  loaderIsActive: false,
  toggleLoader: function (allow) {
    var self = this;

    var loaderScreen = new Element('div', {'id': 'storebundle-admin-loader-screen'});
    loaderScreen.inject(self.wrapper);

    var loader = new Element('div', {'id': 'storebundle-admin-loader'});
    loader.inject(self.wrapper);

    loaderScreen.addEventListener('click', function () {
      if(allow == 1) {
        loaderScreen.remove();
        loader.remove();
      }
    });
  },

  hideLoader:function() {
    $('storebundle-admin-loader-screen').remove();
    $('storebundle-admin-loader').remove();
  },

  toggleBackButton: function () {
    var btn = $('back-button');
    if (!btn) {
      return;
    }
    var display = btn.getStyle('display');
    if (display == 'block') {
      btn.setStyle('display', 'none');
    } else {
      btn.setStyle('display', 'block');
    }
  },
  completer: function (value) {
    var self = this;
    if (!value || value.length <= 0) {
      return;
    }

    var items = [];
    for (var i = 0; i < self.productsCount; i++) {
      var product = self.products[i];
      if (!product) {
        continue;
      }
      if (product.title && product.title.toLowerCase().indexOf(value) > -1) {
        items.push(product);
      }
    }
    if (items.length > 0) {
      self.buildCompleter(items);
      self.showCompleter();
    }
  },

  edit_id: 0,
  showCreateForm: function (id) {
    var self = this;
    self.ids = [];
    self.toggleLoader(1);

    var data = {};
    var url = self.createUrl;
    if (id != undefined) {
      self.edit_id = id;
      data = {bundle_id: id};
      url = self.editUrl;
    }

    new Request.HTML({
      url: url,
      method: 'get',
      data: data,
      evalScripts: false,
      onRequest: function () {
      },
      onError: function (text, error) {
      },
      onFailure: function (xhr) {
      },
      onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        self.wrapper.set('html', responseHTML);
        eval(responseJavaScript);
        self.initCompleter();

        self.toggleBackButton();
      }
    }).send();
  },

  initEditFormProducts: function (selectedProducts) {
    var self = this;
    self.initCompleter();
    self.ids = [];
    for (var i = 0; i < selectedProducts.length; i++) {
      var product = selectedProducts[i];
      self.ids.push(product.id + '');
      self.createProductPreview(product);
    }
  },

  remove: function (id, el) {
    var self = this;
    self.request(
      { bundle_id: id,
        format: 'json' },
      self.deleteUrl, function (response) {
        if (response.status) {
          self.products = JSON.parse(response.products);
          self.productsCount = response.productsCnt;
          $(el).getParent().getParent().remove();
        }
      });
  },

  list: function () {
    var self = this;
    self.request({format: 'json'}, self.listUrl, function (response) {
      if (response.status) {
        self.products = JSON.parse(response.products);
        self.productsCount = response.productsCnt;
        $('storebundle-content-wrapper').set('html', response.html);
        self.toggleBackButton();
      }
    });
  },

  enable: function (id, el) {
    var self = this;
    var data = {
      bundle_id: id,
      format: 'json'
    };

    self.request(data, self.enableUrl, function (response) {
      if (response.status) {
        if (response.enabled) {
          if (!$(el).hasClass('active-bundle-icon')) {
            $(el).addClass('active-bundle-icon');
          }
        } else {
          if ($(el).hasClass('active-bundle-icon')) {
            $(el).removeClass('active-bundle-icon');
          }
        }
      }
    });
  },

  create: function () {
    var self = this;
    var data = self.collectFormParams();
    if (!data) {
      return;
    }

    self.request(data, self.createUrl, function (response) {
      self.list()
    });
  },
  edit: function () {
    var self = this;
    var data = self.collectFormParams();
    data.bundle_id = self.edit_id;
    if (!data) {
      return;
    }
    self.request(data, self.editUrl, function (response) {
      self.list();
      self.edit_id = 0;
    });
  },

  request: function (data, url, callback) {
    var self = this;
    self.toggleLoader(0);
    new Request.JSON({
      url: url,
      method: 'post',
      data: data,
      onRequest: function () {
      },

      onSuccess: function (response) {
        self.hideLoader();
        if(response.status) {
          callback(response);
        } else {
        }
      }
    }).post();
  },

  collectFormParams: function () {
    var self = this;

    var title = self.getField('title', 1);
    var text_visibility = self.getField('text_visibility');
    var percent = self.getField('percent', 1);
    var products = self.ids.join(',');
    var enabled = $('enabled').checked;

    if (title === false || text_visibility === false
      || percent === false || products === false) {
      return false;
    }

    return {
      title: title,
      text_visibility: text_visibility,
      percent: percent,
      products: products,
      enabled: (enabled) ? 1 : 0,
      format: 'json'
    };
  },

  getField: function (title, required) {
    var self = this;
    var field = $(title).value;
    if (required && (!field || field.length <= 0 || field == '')) {
      self.markField(title);
      return false;
    }
    return field;
  },
  markField: function (id) {
    var field = $(id);
    var color = field.getStyle('background-color');
    field.setStyle('background-color', 'salmon');
    setTimeout(function () {
      field.setStyle('background-color', color);
    }, 2000);

  }
};
