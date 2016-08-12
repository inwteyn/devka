
/* $Id: core.js 2010-07-30 18:00 ermek $ */

/*
NEW HECORE CONTACTS MODULE
* */
function he_show_message(message, type, delay) {
  var text = '';
  var duration = 400;
  var delay = (delay == undefined) ? 3000 : delay;

  text = message;

  if (window.$message_container == undefined) {
    window.$message_container = new Element('div', {'class': 'he_message_container'});
    $(document.body).adopt(window.$message_container);
  }

  var className = 'he_msg_text';
  if (type == 'error') {
    className = 'he_msg_error';
  } else if (type == 'notice') {
    className = 'he_msg_notice';
  } else {
    className = 'he_msg_text';
  }

  var $message = new Element('div', {
    'class': className,
    'styles': {
      'opacity': 0
    }
  });
  var $close_btn = new Element('a', {
    'class': 'he_close',
    'href': 'javascript://',
    'events': {
      'click': function(){
        $message.fade('out');
        $message.removeClass('visible');

        window.setTimeout(function(){
          $message.dispose();
          if (window.$message_container.getElements('.visible').length == 0) {
            window.$message_container.empty();
          };
        }, duration);
      }
    }
  });

  $message.addClass('visible');
  $message.adopt($close_btn);
  $message.adopt('html', new Element('span', {'html': message}));
  window.$message_container.adopt($message);

  $message.set('tween', {duration: duration});
  $message.fade('in');

  window.setTimeout(function(){
    $message.fade('out');
    $message.removeClass('visible');
    window.setTimeout(function(){
      if (window.$message_container.getElements('.visible').length == 0) {
        window.$message_container.empty();
      };
    }, duration);
  }, delay);
}

var HEContacts = new Class({

  Implements: [Events, Options],

  options: {
    m: 'hecore',
    l: '',
    c: '',
    t: '',
    params: {},
    nli: 0,
    keyword: '',
    p: 1,
    ipp: 30,
    total: 0,
    contacts: [],
    itemClass: 'item',
    filterField: 'contacts_filter',
    filterSubmit: 'contacts_filter_submit',
    container: 'he_contacts_list',
		loading: 'he_contacts_loading',
    activeClass: 'active',
    hiddenClass: 'hidden',
    disabledClass: 'disabled',
    visibleClass: 'visible',
    listType: 'all',
    contactsCountNode: 'selected_contacts_count',
    submitButtonNode: 'submit_contacts',
    selectAllNode: 'select_all_contacs',
    moreNode: 'contacts_more',
    listTypeAll: 'he_contacts_list_all',
    listTypeSelected: 'he_contacts_list_selected',
    format: 'json'
  },

  url: 'hecore/index/contacts',

  block: false,

	requested: false,

  $filter: null,

  $container: null,

	$loading: null,
	
  $items: null,

  $selectedCount: null,

  $submit: null,

  $filterSubmit: null,

  $selectAll: null,

  $more: null,

  $listAll: null,

  $listSelected: null,

  needPagination: false,

  initialize: function(options) {
    this.setOptions(options);
  },

  box: function() {
    var url = this.url + this.getQuery();
    var $element = new Element('a', {'href': url, 'class': 'smoothbox full'});
    Smoothbox.open($element);
  },

  getQuery: function() {
    var query = Touch.object_to_query_string(this.options.params); // @todo need to change this
		var contacts = Touch.object_to_query_string(this.options.contacts, 'contacts');

    return '?m='+this.options.m+'&l='+this.options.l+'&c='+this.options.c+'&t='+this.options.t+'&nli='+this.options.nli+'&ipp='+this.options.ipp+contacts+query;
  },

  init: function() {
    this.$filter = $(this.options.filterField);
    this.$container = $(this.options.container);
		this.$loading = $(this.options.loading);
    this.$selectedCount = $(this.options.contactsCountNode);
    this.$submit = $(this.options.submitButtonNode);
    this.$filterSubmit = $(this.options.filterSubmit);
    this.$selectAll = $(this.options.selectAllNode);
    this.$more = $(this.options.moreNode);
    this.$listAll = $(this.options.listTypeAll);
    this.$listSelected = $(this.options.listTypeSelected);
    this.$items = $$(this.$container.getElements('.'+this.options.itemClass));

    if (!this.$filter) {
      return ;
    }

    if (!this.$container) {
      return ;
    }

    if (!this.$submit) {
      return ;
    }

    var self = this;

    if (self.$items.length > 0) {
      self.$items.addEvent('click', function(){
        this.blur();
        self.chooseContact(this);
      });
    }

		if (!self.requested)
		{
			self.$submit.addEvent('click', function(){
				self.submit();
			});

			if (self.$more) {
				self.$more.addEvent('click', function(){
					self.more();
				});
			}

			if (self.$listAll) {
				self.$listAll.addEvent('click', function(){
					self.chooseList('all');
				});
			}

			if (self.$listSelected) {
				self.$listSelected.addEvent('click', function(){
					self.chooseList('selected');
				});
			}

			if (self.$filterSubmit) {
				self.$filterSubmit.addEvent('click', function(){
					self.search();
				});
			}

			if (self.$selectAll) {
				self.$selectAll.addEvent('click', function(){
					var value = this.checked ? 1 : 0;
					self.chooseAll(value);
				});
			}

			self.$filter.addEvent('keyup', function(event) {
				if (event.code == 13) {
					self.search();
				}
			});
		}
  },

  chooseList: function(type) {
    this.options.listType = type;

    switch (type) {
      case 'all':
        this.$listSelected.removeClass(this.options.activeClass);
        this.$listAll.addClass(this.options.activeClass);
        this.$items.removeClass(this.options.hiddenClass);
        if (this.needPagination && this.$more) {
          this.$more.removeClass(this.options.hiddenClass);
        }
      break;
      case 'selected':
        this.$listSelected.addClass(this.options.activeClass);
        this.$listAll.removeClass(this.options.activeClass);
        this.$items.addClass(this.options.hiddenClass);
        if (this.$more) {
          this.$more.addClass(this.options.hiddenClass);
        }
        var $items = $$(this.$container.getElements('.'+this.options.activeClass));
        $items.removeClass(this.options.hiddenClass);
      break;
    }
  },

  chooseContact: function($node) {
    $node = $($node);
    
    if (!$node) {
      return ;
    }

    if ($node.hasClass(this.options.disabledClass)) {
      return ;
    }

    var contactId = parseInt($node.id.substr(8));
    if (this.options.contacts.indexOf(contactId) == -1) {
      this.select($node, contactId);
    } else {
      this.deselect($node, contactId);
    }

    var self = this;
		this.initCount();
    setTimeout(function(){self.no_result();}, 650);
  },

  select: function($node, contactId) {
    $node = $($node);
    if (!$node) {
      return ;
    }

    if ($node.hasClass(this.options.disabledClass)) {
      return ;
    }

    if (!contactId) {
      contactId = parseInt($node.id.substr(8));
    }

    $node = $($node);
    if (!$node.hasClass(this.options.activeClass)) {
      $node.addClass(this.options.activeClass);
    }

    if (this.options.contacts.indexOf(contactId) == -1) {
      this.options.contacts.push(contactId);
    }

    if (this.options.listType == 'selected') {
      this.show($node, true);
    }
  },

  deselect: function($node, contactId) {
    $node = $($node);
    if (!$node) {
      return ;
    }
    if ($node.hasClass(this.options.disabledClass)) {
      return ;
    }

    if (!contactId) {
      contactId = parseInt($node.id.substr(8));
    }

    $node = $($node);
    if ($node.hasClass(this.options.activeClass)) {
      $node.removeClass(this.options.activeClass);
    }

    if (this.options.contacts.indexOf(contactId) > -1) {
      this.options.contacts.splice(this.options.contacts.indexOf(contactId), 1);
    }

    if (this.options.listType == 'selected') {
      this.hide($node, true);
    }
  },

  initCount: function() {
    if (this.$selectedCount) {
			this.$selectedCount.set('text', this.options.contacts.length)
		}
  },

  submit: function() {
    eval(""+this.options.c+"(["+this.options.contacts.toString()+"])");
    window.parent.Smoothbox.close();
  },

  chooseAll: function(choose) {
    var self = this;
    if (choose) {
      $$(this.$items).each(function($item){
        $item = $($item);
        if ($item.hasClass(self.options.disabledClass)) {
          return ;
        }
        self.select($item);
      });
    } else {
      $$(this.$items).each(function($item){
        $item = $($item);
        if ($item.hasClass(self.options.disabledClass)) {
          return ;
        }
        self.deselect($item);
      });
    }
    this.initCount();
  },

  search: function() {
		if (!this.$filter.hasClass('filter_default_value')){
    	this.options.keyword = this.$filter.value;
		} else {
			this.options.keyword = '';
		}

		var c  = this.$container.getParent('div.contacts').getSize();
		this.$loading.setStyles({
			'display':'inline-block',
			'margin-left': (c.x - 50)/2
		});

    if ($type(this.$container) == 'element'){
		  this.$container.addClass(this.options.hiddenClass);
    }

    if ($type(this.$more) == 'element'){
		  this.$more.addClass(this.options.hiddenClass);
    }

		
    this.options.p = 1;
    if (this.options.listType != 'all'){
      this.chooseList('all');
    }
    this.getItems(true);
  },

  more: function() {
    this.options.p++;
    this.getItems();
  },

  getItems: function(replace) {
    if (replace === undefined) {
      replace = false;
    }

    if (this.block) {
      return false;
    }

    this.block = true;
    var self = this;
		self.requested = true;

    new Request.JSON({
      url: self.url,
      method: 'post',
      data: self.options,
      onSuccess: function(response) {
        self.block = false;
				self.respond(response, replace);
      }
    }).send();
  },

  respond: function(response, replace) {
    if (replace === undefined) {
      replace = false;
    }

    if (replace) {
      this.$container.set('html', response.html);
    } else {
      var html = this.$container.get('html') + response.html;
      this.$container.set('html', html);
    }

    if (this.$more) {
      this.needPagination = response.need_pagination;
      if (!this.needPagination) {
        this.$more.addClass(this.options.hiddenClass);
      } else {
        this.$more.removeClass(this.options.hiddenClass);
      }
    }

		this.$container.removeClass((this.options.hiddenClass));
		this.$loading.setStyle('display', 'none');

    this.init();
  },

  hide: function($node, fx) {
    var self = this;
    $node = $($node);
    if (!$node) {
      return ;
    }
    var func = function() {
      if (!$node.hasClass(self.options.hiddenClass)){
        $node.addClass(self.options.hiddenClass);
      }
    }

    if (fx) {
      setTimeout(func, 650);
      $node.tween('opacity', [1, 0]);
    } else {
      $node.setStyle('opacity', 0);
      func();
    }
  },

  show: function($node, fx) {
    var self = this;
    $node = $($node);
    if (!$node) {
      return ;
    }
    var func = function() {
      if ($node.hasClass(self.options.hiddenClass)) {
        $node.removeClass(self.options.hiddenClass);
      }
      if ($node.hasClass(self.options.disabledClass)){
        $node.setStyle('opacity', .5);
      }
    }

    $node.setStyle('visibility', 'visible');
    if (fx) {
      setTimeout(func, 650);
      if ($node.hasClass(this.options.disabledClass)){
        $node.tween('opacity', [0, .5]);
      } else {
        $node.tween('opacity', [0, 1]);
      }
    } else {
      $node.setStyle('opacity', 1);
      func();
    }
  },

  no_result: function(){
    if ($$('.visible').length == 0){
      this.show($('no_result'));
    }else{
      this.hide($('no_result'));
    }
  }
});


var he_list = {

  list_type: '',
  keyword: '',
  page: 1,
  ajax_url: '',
  module: '',
  list: '',
  params: {},

  box: function(module, list, title, params){
    this.params = params;
    var not_logged_in = 0;
    var query = object_to_query_string(params);
    var $el = new Element('a', {'href': 'hecore/index/list?m='+module+'&l='+list+'&t='+title+'&nli='+not_logged_in+query, 'class': 'smoothbox'});
    Smoothbox.open($el);
  },

  init: function(){
    var self = this;

    $('list_filter_btn').addEvent('click', function(){
      self.page = 1;
      self.get_items();
    });

    $('list_filter').addEvent('keydown', function(event){
      if(event.key == 'enter') {
        self.page = 1;
        self.get_items();
      }
    });
  },

  select: function(list_type) {
    if (list_type == this.list_type){
      return ;
    }

    this.keyword = '';
    this.page = 1;
    this.list_type = list_type;
    this.get_items();
  },

  get_items: function() {
    var self = this;
    this.keyword = $('list_filter').value;
    $('he_list').innerHTML = '';
    $('he_contacts_loading').setStyle('display', 'block');
    if (this.params.list_type){
      this.params.list_type = self.list_type;
    }
    var query = object_to_query_string(this.params);
    new Request.JSON({
      'url' : self.ajax_url+'?nocache='+Math.random()+query,
      'method' : 'post',
      'data' : {
        'format' : 'json',
        'keyword' : self.keyword,
        'list_type' : self.list_type,
        'p' : self.page,
        'm' : self.module,
        'l' : self.list
      },
      onSuccess: function(response){
        $('he_list').innerHTML = response.html;
        $('he_contacts_loading').setStyle('display', 'none');
        Touchajax.bind($('he_list'));
      }
    }).send();
  },

  set_page: function(page){
    this.page = page;
    this.get_items();
  }
}


function object_to_query_string(object)
{
  var query = "";
  for(key in object){
    query += '&params['+key+']='+object[key];
  }

  return query;
}