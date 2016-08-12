/* $Id: ProductReview.js 2010-05-25 01:44 michael $ */

var ProductReview = {

  pageId: 0,
  url: {},
  allowedComment: false,
  timeOut: 100,

  init: function () {

    var self = this;
    self.elm = {};
    self.elm.list = '.productreview_container_list';
    self.elm.create = '.productreview_container_create';
    self.elm.edit = '.productreview_container_edit';
    self.elm.view = '.productreview_container_view';
    self.elm.message = '.productreview_container_message';

    self.elm.error = '.productreview_container_error';
    self.elm.loader = '#pagerate_loader';
    self.elm.count = '.tab_layout_rate_page_review a span';

    self.elm.commentForm = 'productreview-comment-form';
    self.elm.mainTab = '.tab_layout_rate_product_review a';
    self.elm.mainTabli = '.more_tab li.tab_layout_rate_product_review';
    self.elm.mainContainer = 'generic_layout_container layout_rate_product_review';

    var $form = $$(self.elm.create)[0].getElement('form');
    var $create = $$(self.elm.create)[0];
    $form.addEvent('submit', function () {
      var title = $form.getElement('input[name=title]').getProperty('value', '').trim();
      var body = $form.getElement('textarea[name=body]').getProperty('value', '').trim();

      var ratings = $form.getElements('.review_stars input');
      for (var i = 0; i < ratings.length; i++) {
        var rt = new Number(ratings[i].getProperty('value', 0));

        if (isNaN(rt) || rt <= 0) {
          he_show_message(
            en4.core.language.translate('RATE_You should rate content to leave a review'),
            'error',
            2000
          );
          return false;
        }
      }
      if (title.length <= 0 && body.length <= 0) {
        he_show_message(en4.core.language.translate('RATE_You have missed some fields'),
          'error',
          2000);
        return false;
      }

      if ($create) {
        $create.addClass('hidden');
      }
      self.request(self.url.create, $(this).toQueryString(),function (obj) {
        self.showMessage(obj.msg, obj.result);
        if (obj.result) {
          $form.getElement('input[name=title]').setProperty('value', '');
          $form.getElement('textarea[name=body]').setProperty('value', '');
          $form.getElements('.review_stars input').setProperty('value', 0);
          $form.getElements('.review_stars .rate_star').removeClass('rated');
          self.setCount(1);
          setTimeout(function () {
            self.view(obj.id);
          }, self.timeOut);
        } else {
        }
      }).send();
    });
  },

  initView: function (id) {
    if ($$(this.elm.mainTab)[0]) {
      tabContainerSwitch($$(this.elm.mainTab)[0], this.elm.mainContainer);
    } else if ($$(this.elm.mainTabli)[0]) {
      tabContainerSwitch($$(this.elm.mainTabli)[0], this.elm.mainContainer);
    }
    this.view(id);
  },

  add_class: function ($element, css_class) {
    if ($element.hasClass(css_class)) {
      return;
    }
    $element.addClass(css_class);
    return;
  },
  toggleTab: function (tab) {
    var self = this;
    if (tab != 'list' && tab != 'create' && tab != 'view' && tab != 'edit' && tab != 'message') {
      return;
    }
    $$(self.elm.list).addClass('hidden');
    $$(self.elm.create).addClass('hidden');
    $$(self.elm.view).addClass('hidden');
    $$(self.elm.edit).addClass('hidden');
    $$(self.elm.message).addClass('hidden');

    var $container = $$(self.elm[tab]);
    if ($container) {
      $container.removeClass('hidden');
    }
  },
  showMessage: function (message, result) {
    var self = this;
    self.toggleTab('message');
    var $container = $$(self.elm.message)[0];
    var ul = (result) ? '.success' : '.error';
    $container.getElements('.success, .error').addClass('hidden');
    $container
      .getElement(ul)
      .removeClass('hidden')
      .getElement('li')
      .setProperty('html', message);
  },
  view: function (id) {
    var self = this;
    var $view = $$(self.elm.view)[0];
    self.request(self.url.view, {'review_id': id},function (obj) {
      if (obj.result && self.showLikes) {
        try {
          new LikeTips('productreview', id, {
            'container': 'productreview_comments',
            'html': obj.likeHtml,
            'url': {
              'like': obj.likeUrl,
              'unlike': obj.unlikeUrl,
              'hint': obj.hintUrl,
              'showLikes': obj.showLikesUrl,
              'postComment': obj.postCommentUrl
            }
          });
        } catch (e) {

        }

      }
    }, $view).send();
    self.toggleTab('view');
  },
  create: function () {
    this.toggleTab('create');
  },
  edit: function (id) {
    var self = this;
    var $edit = $$(self.elm.edit)[0];
    self.toggleTab('edit');
    self.request(self.url.edit, {'productreview_id': id},function () {
    }, $edit).send();
  },
  showLoader: function () {
    var $loader = $$(this.elm.loader)[0];
    if ($loader) {
      $loader.removeClass('hidden');
    }
  },
  hideLoader: function () {
    var $loader = $$(this.elm.loader)[0];
    if ($loader) {
      $loader.addClass('hidden');
    }
  },
  goEdit: function () {
    this.toggleTab('edit');
  },
  goCreate: function () {
    this.toggleTab('create');
  },
  editSubmit: function (form) {
    var self = this;
    var values = $(form).toQueryString();
    values += "&task=dosave";
    var $edit = $$(self.elm.edit)[0];
    if ($edit) {
      $edit.addClass('hidden');
    }
    self.request(self.url.edit, values,function (obj) {
      self.showMessage(obj.msg, obj.result);
      if (obj.result) {
        setTimeout(function () {
          self.view(obj.id);
        }, self.timeOut);
      }
    }).send();
    return false;
  },
  setCount: function (direction) {
    var self = this;
    var $counter = $$(self.elm.count)[0];
    if ($counter) {
      var str = $$(self.elm.count)[0].getProperty('html');
      var count = str.substr(1, (str.length - 2)).toInt() + direction;
      $counter.setProperty('html', '(' + count + ')');
    }
  },
  list: function () {
    var self = this;
    self.toggleTab('list');
    self.refresh();
  },
  remove: function (id) {
    var self = this;
    var title = en4.core.language.translate('RATE_REVIEW_DELETE');
    var description = en4.core.language.translate('RATE_REVIEW_DELETEDESC');
    var $list = $$(self.elm.list)[0];
    he_show_confirm(title, description, function () {
      if ($list) {
        $list.setProperty('html', '');
      }
      self.request(self.url.remove, {'review_id': id},function (obj) {
        self.showMessage(obj.msg, obj.result);
        if (obj.result) {
          self.setCount(-1);
        }
        setTimeout(function () {
          self.list();
        }, self.timeOut);
      }).send();
    });
  },
  refresh: function (page) {
    var self = this;
    if (!page) {
      page = 1;
    }
    var $view = $$(self.elm.list)[0];
    var $counter = $$(self.elm.count)[0];
    self.request(self.url.list, {'page': page},function (obj) {
      if ($counter) {
        $counter.setProperty('html', '(' + obj.count + ')');
      }
    }, $view).send();
  },
  request: function (url, data, callback, $container) {
    var self = this;
    if (typeof(data) == 'string') {
      data += '&no_cache=' + Math.random();
      data += '&product_id=' + self.id;
      data += '&format=json';
    } else {
      data.no_cache = Math.random();
      data.product_id = self.id;
      data.format = 'json';
    }
    if ($container) {
      $container.setProperty('html', '');
    }
    self.showLoader();
    return new Request.JSON({
      'url': url,
      'method': 'post',
      'data': data,
      onSuccess: function (obj) {
        self.hideLoader();
        if ($container && obj.html) {
          $container.setProperty('html', obj.html);
          obj.html.stripScripts(true);
          en4.core.runonce.trigger();
        }
        if (callback) {
          callback(obj);
        }
        if (obj.js) {
          eval(obj.js);
        }
      }
    });
  }

};

function ProductReviewRate(uid) {
  this.init(uid);
}

ProductReviewRate.prototype = {

  init: function (uid) {

    var self = this;
    self.$container = $(uid);
    if (self.$container) {
      self.$stars = self.$container.getElements('.rate_style');


      self.$stars.addEvent('mouseover', function () {

        self.$stars.removeClass('rate');
        var $star = $(this);
        if ($star) {
          var $prev = $star.getAllPrevious();
          $prev.addClass('rate');
          $star.addClass('rate');
        }
      });
      self.$stars.addEvent('mouseout', function () {
        self.$stars.removeClass('rate');
      });
      self.$stars.addEvent('click', function () {
        var $star = $(this);
        var score = $star.getProperty('id').substr(10);
        if ($star)  {
          var $hidden = self.$container.getElement('input');
          if ($hidden) {
            $hidden.setProperty('value', score);
          }

          self.$stars.removeClass('hei-star');
          self.$stars.addClass('hei-star-o');
          var $prev = $star.getAllPrevious();
          $prev.addClass('hei-star');
          $star.addClass('hei-star');

          $prev.removeClass('hei-star-o');
          $star.removeClass('hei-star-o');
        }
      });
    }

  }

};

var ReviewManager = {
  page_num: 1,
  category: 0,
  keyword: '',
  search: 0,
  sort: 'recent',

  init: function () {
    var self = this;

    if (document.getElementById('reviews_filter_form') != undefined) {
      var default_value = en4.core.language.translate('Search');
      en4.core.runonce.add(function () {
        $('keyword').value = default_value;
        $('submit').addEvent('click', function (e) {
          e.stop();
          self.page_num = 1;
          self.search = 1;
          self.getReviews();
        });
        $('keyword').addEvents({
          'focus': function () {
            if (this.value == default_value) {
              this.value = "";
            }
          },
          'blur': function () {
            if (this.value == "") {
              this.value = default_value;
            }
          }
        });
        $('keyword').getParent().setStyle('display', 'block');
        $('submit').getParent().setStyle('display', 'block');
      });
    }
  },

  getData: function () {
    var self = this;


    var data = {
      'format': 'html',
      'page': self.page_num,
      'category': self.category,
      'keyword': self.keyword,
      'search': self.search,
      'sort': self.sort
    };

    $$('#reviews_filter_form div input').each(function (item) {
      if (null == item.name.match(/^(\d+)_/))
        return;
      data[item.name] = item.get('value');
    });

    return data;
  },

  getReviews: function () {
    var self = this;

    if ($('page_loader_browse')) {
      $('page_loader_browse').removeClass('hidden');
    }

    if ($('reviews_filter_form') != undefined) {
      if ($('keyword').value == en4.core.language.translate('Search')) {
        $('keyword').value = '';
      }

      self.keyword = $('keyword').value;
      self.category = $('profile_type').value;
      if ($('keyword').value == '')
        $('keyword').value = en4.core.language.translate('Search');
    }
    var data = self.getData();

    new Request.HTML({
      url: self.widget_url,
      data: data,
      evalScripts: true,
      onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        var el = $$('.layout_rate_browse_reviews');
        var tElement = new Element('div', {'html': responseHTML});
        el[0].innerHTML = tElement.getElement('.layout_rate_browse_reviews').innerHTML;

        if ($('page_loader_browse')) {
          $('page_loader_browse').addClass('hidden');
        }
        if (self.category) {
          $('page_category_info').removeClass('hidden');
        }

        en4.core.runonce.trigger();
      }
    }).post();
  },

  setCategory: function (category) {
    this.page_num = 1;
    if ($('reviews_filter_form') != undefined) {
      $('reviews_filter_form').reset();
      $('keyword').value = this.keyword;
      $('profile_type').value = category;
    }

    if ($('profile_type')) {
      $('profile_type').value = category;
      $('profile_type').fireEvent('change');
      if (typeof $('profile_type').onClick != 'undefined')
        $('profile_type').onClick();
    }
    this.getReviews();
  },

  setPage: function (page) {
    this.page_num = page;
    this.getReviews();
  },

  setSort: function (sort) {
    this.page_num = 1;
    this.sort = sort;
    this.getReviews();
  }

}