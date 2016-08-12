var Wall = {};


Wall.globalBind = function ()
{
  window.addEvent('domready', function (){
    try {

    Smoothbox.bind($$('body')[0]);

    if (!Wall.elements){
      Wall.elements = new Wall.Storage();
    }
    $$('.wall_blurlink').each(function (item){
      Wall.elementClass(Wall.BlurLink, item);
    });
  } catch (e){}

  });

};

Wall.globalBind();

Wall.runonce = {

  is_ready: false,

  add: function (fn, force)
  {
    if (this.is_ready && force){
      fn();
    } else {
      en4.core.runonce.add(fn);
    }
  }

};




Wall.window_title = '';
Wall.activityCount = function (count)
{
  Wall.runonce.add(function (){
    if (!count){
      document.title = Wall.window_title;
    } else {
      document.title = '('+count+') ' + Wall.window_title;
    }
  }, true);
};

var WallLoader = function (){
  Wall.window_title = document.title;
  Wall.runonce.is_ready = true;
};

window.addEvent('load', function(){
  WallLoader();
});
window.addEvent('domready', function(){
  WallLoader();
});




Wall.is_request = false;

Wall.request = function (url, data, callback)
{
  var is_tl = Touch.isTimeline() ? 1 : 0;
  if (typeof(data) == 'object'){
    data.format = 'json';
    data.is_timeline = is_tl;
  } else if (typeof(data) == 'string'){
    data += '&format=json';
    data += is_tl ? '&is_timeline=1' : '';
  }
 Wall.is_request = true;

(new Request.JSON({
    secure: false,
    url: url,
    method: 'post',
    data: data,
    onSuccess: function(obj) {
      Wall.is_request = false;

      // callback
      if ($type(callback) == 'function') {
        callback(obj);
      }

      Wall.globalBind();
      en4.core.runonce.trigger();
    }
  })).send();
};

Wall.requestHTML = function (url, callback, $container, data)
{
  data = $merge({'format': 'html'}, data);
  data.is_timeline = Touch.isTimeline() ? 1 : 0;
  Wall.is_request = true;

  var request = new Request.HTML({
    'url': url,
    'method': 'get',
    'data': data,
    'evalScripts' : false,
    'onComplete': function (responseTree, responseElements, responseHTML, responseJavaScript){

      Wall.is_request = false;

      if ($container && $type($container) == 'element'){
        $container.set('html', responseHTML);
      }

      if ($type(callback) == 'function'){
        callback(responseHTML);
      }

      eval(responseJavaScript);
      Wall.globalBind();
      en4.core.runonce.trigger();


    }
  });

/*  if (window.parent.Smoothbox.instance){
    window.parent.Smoothbox.instance.doAutoResize()
  } else if (window.Smoothbox.instance){
    window.Smoothbox.instance.doAutoResize()
  }*/
  request.send();
};



Wall.is_setKeyEvent = false;

Wall.setKeyEvent = function (fn)
{
  var self = this;
  if (this.is_setKeyEvent){
    return ;
  }
  window.addEvent('keyup', function(e) {
    if( e.target.get('tag') == 'html' ||
        e.target.get('tag') == 'body' ) {

      if (fn && $type(fn) == 'function'){
        fn(e);
      }
    }
  });
  this.is_setKeyEvent = true;
};



Wall.$external_div = null;

Wall.externalDiv = function (){
  if (!this.$external_div || $type(this.$external_div) != 'element'){
    this.$external_div = new Element('div', {'class': 'wall-element-external'});
    this.$external_div.inject($$('body')[0]);
  }
  return this.$external_div;
};



Wall.applyAll = function (fn)
{
  if (!fn || $type(fn) != 'function'){
    return ;
  }
  Wall.feeds.getAll().each(function (item){
    fn(item);
  });
};


Wall.BlurLink = new Class({

  name: 'Wall.BlurLink',

  initialize: function (element)
  {
    element = $(element);
    element.addEvent('focus', function (){
      if (element){
        element.blur();
      }
    });
  }

});


Wall.injectAbsolute = function (element, container)
{
  element = $(element);
  container = $(container);

  if ($type(element) != 'element' || $type(container) != 'element'){
    return ;
  }

  var build = function ()
  {
    var pos = element.getCoordinates();

    container
        .setStyle('position', 'absolute')
        .setStyle('top', pos.top + pos.height)
        .setStyle('right', $$('body')[0].getCoordinates().width - pos.left - pos.width);

  };

  container.inject(Wall.externalDiv(), 'bottom');

//  window.addEvent('resize', function (){
//      build(); }.
//      bind(this));
  build();

  return container;

};



Wall.OverText = new Class({

  element: null,
  text: '',

  initialize: function (element, text)
  {
    this.element = element = $(element);
    this.text = text;

    element.set('value', text);
    element.addClass('overTxtLabel');

    element.addEvent('focus', function (){
      if (element.get('value') == text){
        element.set('value', '');
      }
      element.removeClass('overTxtLabel');
    });
    element.addEvent('blur', function (){
      if (element.get('value') == ''){
        element.set('value', text);
      }
      if (element.get('value') == text){
        element.addClass('overTxtLabel');
      }
    });
  },

  clear: function ()
  {
    this.element.value = this.text;
    this.element.addClass('overTxtLabel');
  }

});



Wall.Loader = new Class({

  element: null,

  initialize: function (element)
  {
    this.element = element = $(element);
  },

  hide: function ()
  {
    this.element.setStyle('display', 'none');
  },

  show: function ()
  {
    this.element.setStyle('display', '');
  }

});


Wall.OverLoader = new Class({

  name: 'Wall.OverLoader',
  Implements : [Events, Options],

  element: null,
  overloader: null,
  icon: null,
  type: '',

  options: {
    is_smoothbox: false
  },

  initialize: function (element, type, options)
  {
    this.element = element = $(element);
    this.type = type || 'loader1';

    this.setOptions(options);

    this.overloader = new Element('div', {'class': 'wall-overloader ' + this.type, 'style': 'display: none'});
    this.icon = new Element('div', {'class': 'icon1'});

    this.icon.inject(this.overloader);

    this.build();
    this.overloader.inject(element, 'after');

  },

  show: function ()
  {
    this.build();
    this.overloader.setStyle('display', '');
  },

  hide: function ()
  {
    this.overloader.setStyle('display', 'none');
  },

  build: function ()
  {
    if (!this.element.isVisible()){
      return ;
    }

    var pos = this.element.getCoordinates();

    this.overloader
      .setStyle('position', 'absolute')
      .setStyle('left', pos.left)
      .setStyle('top', pos.top)
      .setStyle('width', pos.width + ((this.options.is_smoothbox) ? 0 : 1)) // borders
      .setStyle('height', pos.height + ((this.options.is_smoothbox) ? 0 : 1))
      .setStyle('display', 'none');


    var loader_width = 0;
    var loader_height = 0;

    if (this.type == 'loader1'){
      loader_width = 16;
      loader_height = 16;
    } else if (this.type == 'loader2') {
      loader_width = 48;
      loader_height = 48;
    }

    this.icon
      .setStyle('margin-left', pos.width / 2 - loader_width/2)
      .setStyle('margin-top', pos.height / 2 - loader_height/2);

  }


});

Wall.SlideLoader = new Class({

  name: 'Wall.OverLoader',
  Implements : [Events, Options],

  element: null,
  overloader: null,
  icon: null,
  type: '',

  options: {
    is_smoothbox: false
  },

  initialize: function (element, type, options)
  {
    this.element = element = $(element);
    this.type = type || 'loader1';

    this.setOptions(options);

    this.overloader = new Element('div', {'class': 'wall-overloader ' + this.type, 'style': 'display: none'});
    this.icon = new Element('div', {'class': 'icon1'});

    this.icon.inject(this.overloader);

    this.build();
    this.overloader.inject(element, 'after');

  },

  show: function ()
  {
    this.build();
    this.overloader.setStyle('display', '');
  },

  hide: function ()
  {
    this.overloader.setStyle('display', 'none');
  },

  build: function ()
  {
    if (!this.element.isVisible()){
      return ;
    }

    var pos = this.element.getCoordinates();

    this.overloader
      .setStyle('position', 'absolute')
      .setStyle('left', 0)
      .setStyle('top', 0)
      .setStyle('width', pos.width + ((this.options.is_smoothbox) ? 0 : 1)) // borders
      .setStyle('height', pos.height + ((this.options.is_smoothbox) ? 0 : 1))
      .setStyle('display', 'none');


    var loader_width = 0;
    var loader_height = 0;

    if (this.type == 'loader1'){
      loader_width = 16;
      loader_height = 16;
    } else if (this.type == 'loader2') {
      loader_width = 48;
      loader_height = 48;
    }

    this.icon
      .setStyle('margin-left', pos.width / 2 - loader_width/2)
      .setStyle('margin-top', pos.height / 2 - loader_height/2);

  }


});

Wall.elementClass = function ()
{
var options = Array.prototype.slice.call(arguments || []);
var newClass = options[0];

if (!newClass || ($type(newClass) != 'class')){
return ;
}
var name = newClass.prototype.name;
if (!name){
return ;
}

if ($type($(options[1])) == 'element'){

var element = $(options[1]);
var key = name + '_' + (window.$uid || Slick.uidOf)(element);
var instance = Wall.elements.get(key);

if (instance){
return instance;
}
if(Touch.isTimeline()){
  instance = newClass.Invoke(options.slice(1));
} else{
  newClass._prototyping = true;
  newClass.$prototyping = true;
  instance = new newClass();
  delete newClass._prototyping;
  delete newClass.$prototyping;
  newClass.prototype.initialize.apply(instance, options.slice(1));
}
Wall.elements.add(key, instance);

return instance;

}
};






Wall.TipFx = new Class({

  Implements: [Events, Options],
  options: {
    class_var: '',
    is_arrow: true,
    relative_element: null,
    delay: 1
  },
  timeout: null,
  mouseActive: false,

  initialize: function (element, options)
  {
    this.setOptions(options);
    this.element = $(element);
    this.createDom();
  },

  createDom: function ()
  {
    var self = this;


    if ($type($(this.element)) != 'element'){
      return ;
    }

    this.$container = new Element('div', {'class': 'wall-tips ' + (this.options.class_var || ''), style: 'display:none'});
    this.$inner = new Element('div', {'class': 'container', 'html': (this.options.html || '')});

    if (!Wall.isLightTheme()){
      this.$container.addClass('night_theme');
    }

    if (this.options.is_arrow){
      this.$arrow_container = new Element('div', {'class': 'arrow_container'});
      this.$arrow = new Element('div', {'class': 'arrow'});
    }

    this.$inner.inject(this.$container);

    if (this.options.is_arrow){
      this.$arrow.inject(this.$arrow_container);
      this.$arrow_container.inject(this.$container);
    }

    this.$container.inject(Wall.externalDiv());

//    window.addEvent('resize', function (){
//      this.build();
//    }.bind(this));

    this.build();



    var mouseover = function (){

      this.mouseActive = true;

      if (this.options.delay){
        window.clearTimeout(this.timeout);
        this.timeout = window.setTimeout(function (){
          this.build();
          this.$container.setStyle('display', '');
          this.fireEvent('mouseover');
        }.bind(this), this.options.delay);

      } else {
        this.build();
        this.$container.setStyle('display', '');
        this.fireEvent('mouseover');
      }

    }.bind(this);

    this.element.addEvent('mouseover', mouseover);
    this.$container.addEvent('mouseover', mouseover);



    var mouseout = function (e){

      this.mouseActive = false;

      if (this.options.delay){
        window.clearTimeout(this.timeout);
        this.timeout = window.setTimeout(function (){
          if (e && $(e.relatedTarget)){
          }
          this.$container.setStyle('display', 'none');
          this.fireEvent('mouseout');
        }.bind(this), this.options.delay);

      } else {
        this.$container.setStyle('display', 'none');
        this.fireEvent('mouseout');
      }


    }.bind(this);

    this.element.addEvent('mouseout', mouseout);
    this.$container.addEvent('mouseout', mouseout);


    this.fireEvent('complete');


  },

  build: function ()
  {
    if (!this.element.isVisible()){
      return ;
    }
    this.$container.setStyle('display', '');

    var e_pos = this.element.getCoordinates();
    var c_pos = this.$container.getCoordinates();
    var w_pos = this.options.relative_element || $$('body')[0].getSize();

    this.$container
      .setStyle('display', 'none')
      .setStyle('padding-bottom', 2);

    var rebuild = function (left, top){

      if (left){
        this.$container.setStyle('left', e_pos.left);
        if (this.options.is_arrow){
          this.$arrow.setStyle('left', (e_pos.width/2-2.5).toInt());
        }
      } else {
        this.$container.setStyle('left', e_pos.left-(c_pos.width-e_pos.width));
        if (this.options.is_arrow){
          this.$arrow.setStyle('right', (e_pos.width/2-2.5).toInt());
        }
      }
      if (top){
        this.$container.setStyle('top', e_pos.top-c_pos.height-1);
        if (this.options.is_arrow){
          this.$arrow_container.inject(this.$inner, 'after');
          this.$arrow.addClass('bottom');
        }

      } else {
        this.$container.setStyle('top', e_pos.top+e_pos.height+1);
        if (this.options.is_arrow){
          this.$arrow_container.inject(this.$inner, 'before');
          this.$arrow.addClass('top');
        }

      }

      this.fireEvent('build');

    }.bind(this);

    //rebuild((e_pos.left+c_pos.width < w_pos.x-10), (e_pos.top+c_pos.height < w_pos.y-10));
    if (this.options.top != undefined && this.options.left != undefined){
      rebuild(this.options.left, this.options.top);
    } else {
      rebuild(1,1);
    }


  }

});



Wall.Tips = new Class({

  Extends: Wall.TipFx,

  name: 'Wall.Tips',

  initialize: function (element, options)
  {
    this.addEvent('onComplete', function (){
      var title = this.options.title || this.element.get('title');
      this.$inner.set('html', '<div class="data"><div class="title">'+title+'</div></div>');
      this.element.removeProperty('title');
    }.bind(this));

    this.parent(element, options);
  },

  setTitle: function (title)
  {
    this.$inner.set('html', '<div class="data"><div class="title">'+title+'</div></div>');
    this.build();
    if (this.mouseActive){
      this.$container.setStyle('display', 'block');
    }
  }

});




Wall.Storage = new Class({

  items: {},

  initialize: function ()
  {
    this.items = new Hash();
  },

  add: function (key, object)
  {
    if (this.items[key]){
      return ;
    }
    this.items[key] = object;
    return this;
  },

  get: function (key)
  {
    var options = Array.prototype.slice.call(arguments || []);
    if (options.length > 1){
      key = options.join("_");
    }
    return this.items[key];
  },

  getAll: function ()
  {
    return this.items
  },

  remove: function (key)
  {
    this.items.erase(key);
    return this;
  }

});




Wall.dialog = {};

Wall.dialog.message = function (message, result, in_parent){
  var type = (result) ? '' : 'error';

  if (result == 1){
    type = '';
  } else if (result == 2){
    type = 'notice';
  } else {
    type = 'error';
  }

  var from = null;
  if (in_parent && window.parent){
    from = window.parent;
  } else {
    from = window;
  }
  alert(message);
  //from.he_show_message(message, type);
};

Wall.dialog.confirm = function (type, callback, in_parent){

  var title = en4.core.language.translate('WALL_CONFIRM_' + type.toUpperCase() + '_TITLE' );
  var description = en4.core.language.translate('WALL_CONFIRM_' + type.toUpperCase() + '_DESCRIPTION' );

  var from = null;
  if (in_parent && window.parent){
    from = window.parent;
  } else {
    from = window;
  }
  if(confirm(description)){
    callback();
  }
  //from.he_show_confirm(title, description, callback);
};





Wall.List = new Class({

  Implements : [Events, Options],

  params: {
   'tab': 'all',
   'page': 1,
   'search': ''
  },
  options: {
    url: {},
    is_edit: false,
    list_id: 0,
    selected: []
  },

  select_paging_active: false,
  morph: null,

  initialize: function (options)
  {
    this.setOptions(options);
    this.init($('wall-list'));
  },

  init: function (container)
  {
    var self = this;
    this.$container = $(container);

    Wall.globalBind();

    this.loader = new Wall.OverLoader(this.$container.getElement('.items-container'), 'loader2');

    if (this.options.is_edit){
      this.toggle(false);
      this.$container.getElements('.tabs-container .selected').addClass('is_active');
      this.$container.getElements('.selected-container .item').addClass('is_active');
      this.$container.getElement('.selected_count').set('html', self.options.selected.length);
    }

    this.$container.getElements('.tabs-container .all').addEvent('click', function (){

      self.toggle(true);
      $(this).addClass('is_active');

      self.params = {
        'tab': 'all',
        'page' : 1,
        'search': ''
      };
      self.getItems(self.params);

    });

    this.$container.getElements('.tabs-container .selected').addEvent('click', function (){
      self.toggle(false);
      $(this).addClass('is_active');
    });

    this.$container.getElements('.tabs-container .type').addEvent('click', function (){

      self.toggle(true);
      $(this).addClass('is_active');

      var type = $(this).get('rev').substr(5);

      self.params = {
        'tab': type,
        'page' : 1,
        'search': ''
      };
      self.getItems(self.params);

    });

    this.$container.getElement('.all-prev').addEvent('click', function (){

      if ($(this).hasClass('disabled')){
        return ;
      }
      self.params.page--;
      self.getItems(self.params);

    });

    this.$container.getElement('.all-next').addEvent('click', function (){

      if ($(this).hasClass('disabled')){
        return ;
      }
      self.params.page++;
      self.getItems(self.params);

    });

    var searchSubmit = function (){
      self.params.page = 1;
      self.params.search = self.$container.getElement('.search-form input').value;
      self.getItems(self.params);
    };

    this.$container.getElement('.search-form form').addEvent('submit', function (e){
      e.stop();
      searchSubmit();
    });
    this.$container.getElement('.search-form a').addEvent('click', function (){
      searchSubmit();
    });


    this.$container.getElement('.select-prev').addEvent('click', function (){
      self.selectedPaging(-1);
    });
    this.$container.getElement('.select-next').addEvent('click', function (){
      self.selectedPaging(1);
    });


    var $form = this.$container.getElement('.form');

    var formSubmit = function (){

      var data = {
        'label': $form.label.value,
        'guids': self.options.selected,
        'list_id': self.options.list_id
      };

      Wall.request(self.options.url.save, data, function (obj){
        Wall.dialog.message(obj.message, obj.result, window.parent);
        if (obj.result){
          window.parent.Wall.feeds.getAll().each(function (item){
            item.loadList(obj.html);
            var $link = item.$types.getElement('.item[rev=list-' + obj.list_id + ']');
            if ($link){
              $link.fireEvent('click');
            }
            window.parent.Smoothbox.close();
          });
        }
      });

    };

    this.$container.getElement('.form').addEvent('submit', function (e){
      e.stop();
      formSubmit();
    });

    this.$container.getElement('.form-submit').addEvent('click', function (e){
      e.stop();
      formSubmit();
    });


    this.initItem();
  },


  toggle: function (is_all)
  {
    var tabs = this.$container.getElements('.tabs-container ul li a');
    var selected = this.$container.getElements('.selected-container');
    var all = this.$container.getElements('.all-container');

    if (is_all){
      tabs.removeClass('is_active');
      selected.removeClass('is_active');
      all.addClass('is_active');

      this.$container.getElements('.all-prev, .all-next').setStyle('display', '');
      this.$container.getElements('.select-prev, .select-next').setStyle('display', 'none');

    } else {

      tabs.removeClass('is_active');
      selected.addClass('is_active');
      all.removeClass('is_active');

      this.$container.getElements('.all-prev, .all-next').setStyle('display', 'none');
      this.$container.getElements('.select-prev, .select-next').setStyle('display', '');

      this.checkCountSelected();

      // After all
      this.selectedPaging(0);

    }

  },

  checkCountSelected: function ()
  {
    var selected = this.$container.getElements('.selected-container');
    if (this.options.selected.length){
      selected.getElement('.message').setStyle('display', 'none');
    } else {
      selected.getElement('.message').setStyle('display', '');
    }
  },

  selectedPaging: function (direction)
  {
    var self = this;

    if (this.select_paging_active){
      return ;
    }

    var $items = this.$container.getElement('.selected-container .items');
    var $total = this.$container.getElement('.items-container .container');

    var new_top = 0;
    var top = $items.getStyle('top').toInt();
    var step = $total.getSize().y;
    var total = $items.getSize().y;

    if (direction == 1){
      new_top = top - step;
    } else if (direction == -1){
      new_top = top + step;
    } else {

      // back
      if (top <= -total){
        new_top = top + step;
      } else {
        new_top = top;
      }

    }

    if (new_top > 0 || new_top <= -total){
      return ;
    }

    if (new_top-step <= -total){
      this.$container.getElement('.select-next').addClass('disabled');
    } else {
      this.$container.getElement('.select-next').removeClass('disabled');
    }
    if (new_top+step > 0){
      this.$container.getElement('.select-prev').addClass('disabled');
    } else {
      this.$container.getElement('.select-prev').removeClass('disabled');
    }

    if (top == new_top){
      return ;
    }

    this.select_paging_active = true;

    var morph = new Fx.Morph($items, {duration: 'short'});

    morph.addEvent('onComplete', function (){
      self.select_paging_active = false;
    });

    morph.start({
      'top': new_top
    });

  },

  getItems: function ()
  {
    var self = this;

    this.loader.show();

    Wall.request(self.options.url.browse, this.params, function (obj){

      self.$container.getElements('.all-container .items').set('html', obj.html);

      if (obj.prev){
        self.$container.getElement('.all-prev').removeClass('disabled');
      } else {
        self.$container.getElement('.all-prev').addClass('disabled');
      }

      if (obj.next){
        self.$container.getElement('.all-next').removeClass('disabled');
      } else {
        self.$container.getElement('.all-next').addClass('disabled');
      }

      self.loader.hide();

      self.initItem();

    });
  },

  initItem: function ()
  {
    var $container = this.$container;
    var self = this;

    this.options.selected.each(function (guid){
      $container.getElements('.all-container .items li.item_'+guid).addClass('is_active');
    });

    var selectedClick = function (){

      var guid = $(this).get('rev').substr(5);

      $container.getElements('.all-container .items li.item_' + guid).removeClass('is_active');
      $(this).destroy();
      self.options.selected.erase(guid);
      self.selectedPaging(0);
      self.checkCountSelected();
      self.$container.getElement('.selected_count').set('html', self.options.selected.length);

    };

    $container.getElements('.selected-container .items li.item').addEvent('click', selectedClick);

    $container.getElements('.all-container .items li.item').addEvent('click', function (){

      var guid = $(this).get('rev').substr(5);

      if ($(this).hasClass('is_active')){

        $(this).removeClass('is_active');

        $container.getElements('.selected-container .items li.item_' + guid).destroy();

        self.options.selected.erase(guid);

        // do not need
        // self.checkCountSelected();

        self.$container.getElement('.selected_count').set('html', self.options.selected.length);

        self.selectedPaging(0);

      } else {

        $(this).addClass('is_active');

        if (!self.options.selected.contains(guid)){
          self.options.selected[self.options.selected.length] = guid;
        }

        self.$container.getElement('.selected_count').set('html', self.options.selected.length);

        $(this).clone().inject($container.getElement('.selected-container .items')).addEvent('click', selectedClick);

      }

    });



  }

});






Wall.slideshows = new Wall.Storage();

Wall.Slideshow = new Class({

  feed_uid: '',

  Implements : [Events, Options],
  options : {
    item_width: 107,
    item_count: 2
  },
  url: '',

  page: '',

    loader : {},

  src: '',
  is_loading: false,

  viewClose: function ()
  {
    // Kirill's mod
//    document.ontouchmove = function(e){
//        return e.state;
//    }
    // Kirill's mod
    this.slideshow.destroy();
    Wall.slideshows.remove(this.feed_uid);
    $$('html')[0].removeClass('wall_hidescrool');
  },

  createBox: function ()
  {
    this.slideshow.inject(Wall.externalDiv());
    $$('html')[0].addClass('wall_hidescrool');
    this.build();
  },

  createDom: function ()
  {

    this.slideshow = new Element('div', {'class': 'wall-slideshow wall-window'});
    this.container = new Element('div', {'class': 'container'});
    this.close = new Element('a', {'class': 'close wall_blurlink', 'href': 'javascript:void(0);'});
    this.content = new Element('div', {'class': 'content'});

    var html = '<table cellpadding="0" cellspacing="0" style="margin: 0 auto;" align="center"><tr><td valign="middle" align="center" style="text-align: center;"><a href="javascript:void(0);"><img class="_img" src="' + this.src + '" alt=""/></a></td></tr>';
    this.preview = new Element('div', {'class': 'preview is_active', 'html': html});

    if (!Wall.isLightTheme()){
      this.slideshow.addClass('night_theme');
    }
    if (!!navigator.userAgent.match(/MSIE/)){
      this.slideshow.addClass('ie');
    }

    this.close.inject(this.container);
    this.preview.inject(this.container);
    this.content.inject(this.container);
    this.container.inject(this.slideshow);
  },

  initialize: function (src, guid, element)
  {
    var self = this;

    this.feed_uid = $random(1111,9999);
    this.src = src;

    Wall.setKeyEvent(function (e){
      if (e.key == 'right' || e.key == 'up'){
        Wall.slideshows.getAll().each(function (item){
          item.next.fireEvent('click');
        });
      }
      if (e.key == 'left' || e.key == 'down'){
        Wall.slideshows.getAll().each(function (item){
          item.prev.fireEvent('click');
        });
      }
      if (e.key == 'esc'){
        Wall.slideshows.getAll().each(function (item){
          item.viewClose();
        });
      }
    });
    Wall.slideshows.add(this.feed_uid, this);
    Wall.slideshows.taggers = new Wall.Storage();

    this.createDom();
    self.tmp_elemnt = element;

    window.addEvent('resize', function (){
        if(window.getSize().x > 500){
            self.build();
            return;
        }
        self.resize(self.tmp_elemnt);
    });


    this.slideshow.addEvent('click', function (e){

      if (e){
        e.stop();
        if (!$(e.target).getParent('body') || $(e.target).getParent('.container') || $(e.target).hasClass('container')){
          return ;
        }
      }
      self.viewClose();
    });

    Wall.globalBind();

    var thumb = '';
    if (element){
      element = $(element);
      thumb = $(element).get('src');
      if (!thumb){
        thumb = $(element).getElement('img').get('src');
      }
    }

    this.createBox();
    self.build();

    this.preview.getElement('img').addEvent('load', function (){
      self.build();
    });
    this.close.addEvent('click', function (){
      self.viewClose();
    });

    this.url = en4.core.baseUrl + 'wall/photo/index?subject=' + guid;

    Wall.requestHTML(this.url, function (html){

      if (!html){
        return  ;
      }

      self.content.setStyle('display', '');
      self.content.set('html', html);

      self.preview.removeClass('is_active');
      self.content.addClass('is_active');

      self.prev = new Element('a', {'class': 'prev navigation wall_blurlink', 'href': 'javascript:void(0);', 'style': 'display: none'});
      self.next = new Element('a', {'class': 'next navigation wall_blurlink', 'href': 'javascript:void(0);', 'style': 'display: none'});

      self.prev.inject(document.body.getElement('div.wall-slideshow').getElement('div.photo'));
      self.next.inject(document.body.getElement('div.wall-slideshow').getElement('div.photo'));

      self.prev_page = self.content.getElement('.set_prev_page').get('html');
      self.next_page = self.content.getElement('.set_next_page').get('html');

        if(self.prev_page != 0) {
            self.prev.setStyle('display', 'block');
        }
        if(self.next_page != 0) {
            self.next.setStyle('display', 'block');
        }

        self.resize(element);

        if(self.loader )
            self.loader = new Wall.SlideLoader(self.content.getElement('._img'), 'loader2');

      self.prev.addEvent('click', function (){
          self.loader.show();
       Wall.request(en4.core.baseUrl + 'wall/photo/index?subject=' + guid + '&p='+self.prev_page, {},  function (obj){
           self.content.getElement('._img').set('src', obj.photo_url);
           self.loader.hide();
           if(obj.prev == 0) {
               self.prev.setStyle('display', 'none');
           } else {
               self.prev.setStyle('display', 'block');
           }
           if(obj.next == 0) {
               self.next.setStyle('display', 'none');
           } else {
               self.next.setStyle('display', 'block');
           }
           self.prev_page = obj.prev;
           self.next_page = obj.next;
           obj.comment_html.stripScripts(true);
           self.content.getElement('.photos_info .item').set('html', obj.comment_html);
       });
      });

      self.next.addEvent('click', function (obj){
          self.loader.show();
       Wall.request(en4.core.baseUrl + 'wall/photo/index?subject=' + guid + '&p='+self.next_page, {}, function (obj){
           self.content.getElement('._img').set('src', obj.photo_url);
           self.loader.hide();
           if(obj.prev == 0) {
               self.prev.setStyle('display', 'none');
           } else {
               self.prev.setStyle('display', 'block');
           }
           if(obj.next == 0) {
               self.next.setStyle('display', 'none');
           } else {
               self.next.setStyle('display', 'block');
           }
           self.prev_page = obj.prev;
           self.next_page = obj.next;
           obj.comment_html.stripScripts(true);
           self.content.getElement('.photos_info .item').set('html', obj.comment_html);
       });
      });

    });
  },

  initItems: function (container)
  {
    var self = this;

    if (!container){
      container = this.content;
    }
    container.getElements('.header .description').enableLinks();

    container.getElements('.photo a').addEvent('click', function (e){
      self.next.fireEvent('click');
    });


    Wall.globalBind();

  },

  'resize' : function(element) {
      var self = this;
      var w = window.getSize().x;
      var h = window.getSize().y;
      var windowH = window.getSize().y; // 401  158
      var windowW = window.getSize().x; // 320  480

      var mx = 1;
      if(windowW > 500)
        mx = 2;
      else
        mx = 1.5;

      self.container.setStyle('width', windowW/mx);
      self.container.getElements('img._img').setStyle('width', windowW/mx);

      var pos = self.container.getCoordinates();
      var total = self.slideshow.getCoordinates();

      var global = $('global_bind').getCoordinates();
      self.slideshow.setStyle('height', global.height);

      var top = 20;
      if (element != null && mx < 2){
          top = element.getCoordinates().top - 50;
          var border = self.slideshow.getCoordinates().bottom;
          var container_bottom = self.container.getCoordinates().height + top;

          if(container_bottom >= border)
            top -= (container_bottom - border);

      }

      self.container
          .setStyle('position', 'absolute')
          .setStyle('left', (total.width/2 - pos.width/2))
          .setStyle('top', top)
      ;
    },

  build: function ()
  {
    var pos = this.container.getCoordinates();
    var total = this.slideshow.getCoordinates();



  this.container
    .setStyle('position', 'absolute')
    .setStyle('left', (total.width/2 - pos.width/2))
    .setStyle('top', 20)
//    .setStyle('top', (total.height/2 - pos.height/2))
      ;
  }

});




Wall.liketips = new Wall.Storage();
Wall.loaders = [];


Wall.LikeTips = new Class({

  Extends: Wall.TipFx,
  name: 'Wall.LikeTips',
  is_loading: false,

  options: {
    class_var: 'wall-liketips',
    html: '<div class="data"><span class="wall-loading"></span>' + en4.core.language.translate('WALL_LOADING') + '</div>',
    delay: 250
  },

  initialize: function (element, options)
  {
    this.parent(element, options);

    var self = this;
    var guid = element.get('rev');

    var loadContent = function (){

      var element = Wall.liketips.get(guid);
      if (element){

        element.inject(Wall.externalDiv());
        self.$inner.empty();
        element.inject(self.$inner);

        self.build();
        if (self.mouseActive){
          self.$container.setStyle('display', 'block');
        }
        return ;
      }

      if (Wall.loaders.contains(guid)){
        return ;
      }
      Wall.loaders[Wall.loaders.length] = guid;

      Wall.requestHTML(en4.core.baseUrl + 'wall/tips/index/subject/' + guid, function (html){

        var element = new Element('div', {'class': ''});
        element.set('html', html);

        Wall.liketips.add(guid, element);
        loadContent();

      });
    };


    this.addEvent('mouseover', loadContent);

  }

});


Wall.Items = new Class({

  Implements : [Events, Options],
  options: {
    params: {}
  },
  loader: null,

  $container: null,

  initialize: function (container, options)
  {
    this.setOptions(options);

    var self = this;

    this.$container = container = $(container);
    Wall.globalBind();

    this.loader = new Wall.OverLoader(container.getElement('.items-container'), 'loader2');


    container.getElement('.prev').addEvent('click', function (){

      if ($(this).hasClass('disabled')){
        return ;
      }
      self.options.params.page--;
      self.getItems(self.options.params);

    });

    container.getElement('.next').addEvent('click', function (){

      if ($(this).hasClass('disabled')){
        return ;
      }
      self.options.params.page++;
      self.getItems(self.options.params);

    });

    var searchSubmit = function (){
      self.options.params.page = 1;
      self.options.params.search = container.getElement('.search-form input').value;
      self.getItems(self.options.params);
    };

    container.getElement('.search-form form').addEvent('submit', function (e){
      e.stop();
      searchSubmit();
    });
    container.getElement('.search-form a').addEvent('click', function (){
      searchSubmit();
    });

  },

  getItems: function ()
  {
    var self = this;

    this.loader.show();

    var url = en4.core.baseUrl + 'wall/items';

    Wall.request(url, self.options.params, function (obj){

      self.$container.getElements('.items').set('html', obj.html);

      if (obj.prev){
        self.$container.getElement('.prev').removeClass('disabled');
      } else {
        self.$container.getElement('.prev').addClass('disabled');
      }

      if (obj.next){
        self.$container.getElement('.next').removeClass('disabled');
      } else {
        self.$container.getElement('.next').addClass('disabled');
      }

      self.loader.hide();


    });
  }



});



Wall.Comment = new Class({

  Implements: [Options, Events],

  options: {
    'element_key': ''
  },

  $container: null,
  $form: null,

  initialize: function (options)
  {
    this.setOptions(options);
    var self = this;
    self.init(); // domready
  },

  init: function ()
  {
    var $container = this.$container = $(this.options.element_key);
    var $form = this.$form = $container.getElement('.comment-form');

    var $post = $container.getElement('.post-comment');

    if ($post){

      $container.getElement('.post-comment').addEvent('click', function (){
        $form.style.display = '';
        $form.body.focus();
      });

      $form.body.autogrow();
      this.attachCreateComment($form);

    }

    Wall.globalBind();

  },


  loadComments : function(type, id, page){

    var self = this;

    Wall.requestHTML(en4.core.baseUrl + 'wall/comment/list', function (html){
      self.$container.set('html', html);
      self.init();
    }, null, {
      type : type,
      id : id,
      page : page
    });

  },

  attachCreateComment : function(formElement){
    var bind = this;

    formElement.getElements('button[type=submit]').addEvent('click', function(e){

      e.stop();

      var form_values  = formElement.toQueryString();
          form_values += '&format=json';
          form_values += '&id='+formElement.identity.value;

      Wall.request(en4.core.baseUrl + 'wall/comment/create', form_values, function (obj){
        bind.$container.set('html', obj.body);
        bind.init();
      });

    });

    this.$container.getElements('.load-comments').addEvent('click', function (){
      var data = JSON.decode($(this).get('rev'));
      bind.loadComments.apply(bind, data);
    });

    this.$container.getElements('.delete-comment').addEvent('click', function (){
      var data = JSON.decode($(this).get('rev'));
      bind.deleteComment.apply(bind, data);
    });

    this.$container.getElements('.like, .comment-like').addEvent('click', function (){
      var data = JSON.decode($(this).get('rev'));
      bind.like.apply(bind, data);
    });

    this.$container.getElements('.unlike, .comment-unlike').addEvent('click', function (){
      var data = JSON.decode($(this).get('rev'));
      bind.unlike.apply(bind, data);
    });


  },

  comment : function(type, id, body){

    var self = this;
    Wall.request(en4.core.baseUrl + 'wall/comment/create', {
      type : type,
      id : id,
      body : body
    }, function (obj){
      self.$container.set('html', obj.body);
      self.init();
    });

  },

  like : function(type, id, comment_id) {

    var self = this;
    Wall.request(en4.core.baseUrl + 'wall/comment/like', {
      type : type,
      id : id,
      comment_id : comment_id
    }, function (obj){
      self.$container.set('html', obj.body);
      self.init();
    });

  },

  unlike : function(type, id, comment_id) {

    var self = this;
    Wall.request(en4.core.baseUrl + 'wall/comment/unlike', {
      type : type,
      id : id,
      comment_id : comment_id
    }, function (obj){
      self.$container.set('html', obj.body);
      self.init();
    });

  },


  deleteComment : function(type, id, comment_id) {
    if( !confirm(en4.core.language.translate('Are you sure you want to delete this?')) ) {
      return;
    }


    var self = this;
    Wall.request(en4.core.baseUrl + 'wall/comment/delete', {
      type : type,
      id : id,
      comment_id : comment_id
    }, function (obj){
        if( self.$container.getElement('.comment-' + comment_id) ) {
          self.$container.getElement('.comment-' + comment_id).destroy();
        }
        try {
          var commentCount = self.$container.getElement('.comments_options span');
          var m = commentCount.get('html').match(/\d+/);
          var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
          commentCount.set('html', commentCount.get('html').replace(m[0], newCount));
        } catch( e ) {}
    });

  }

});




Wall.Like = new Class({

  Implements: [Events, Options],
  options: {},

  initialize: function (element, options)
  {
    var self = this;

    this.setOptions(options);


    element.addEvent('click', function (){

      var is_unlike = (element.hasClass('unlike')) ? 1 : 0;

      Wall.request(en4.core.baseUrl + 'wall/tips/like', {'is_unlike': is_unlike, 'subject': self.options.guid}, function (obj){
        if (obj.result){

          if (!is_unlike){
            element.set('html', en4.core.language.translate('WALL_UNLIKE'));
            element.addClass('unlike');
          } else {
            element.set('html', en4.core.language.translate('WALL_LIKE'));
            element.removeClass('unlike');
          }

        }
      });
    });

  }

});


Wall.getActionInfo = function (item){
  var $item = $(item);
  if (!$item){
    return false;
  }
  var $action = $item.getParent('.wall-action-item');
  if (!$action){
    return false;
  }
  var action_id = $action.get('rev').substr(5);

  return {
    'action_id': action_id,
    'action': $action,
    'comment_pagination': ($action.hasClass('action_comment_pagination')) ? 1 : 0,
    'checkin': ($action.getParent('.checkins')) ? 1 : 0
  };
};

Wall.getCommentInfo = function (item)
{
  var $item = $(item);
  if (!$item){
    return false;
  }
  var $comment = $item.getParent('.wall-comment-item');
  if (!$comment){
    return false;
  }
  var comment_id = $comment.get('rev').substr(5);

  return {
    'comment_id': comment_id
  };

};


Wall.UpdateHandler = new Class({

  Implements : [Events, Options],
  options : {
      debug : false,
      baseUrl : '/',
      identity : false,
      delay : 5000,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      last_date: Touch.isTimeline()?'':null,
      subject_guid : null,
      feed_uid: null
  },
  state : true,
  activestate : 1,
  fresh : true,
  lastEventTime : false,
  title: document.title,

  initialize : function(options) {
    this.setOptions(options);
  },

  start : function() {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout : this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive' : function() {
        this.activestate = 1;
        this.state= true;
      }.bind(this),
      'onStateIdle' : function() {
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });
    this.loop();
  },

  stop : function() {
    this.state = false;
  },

  checkFeedUpdate : function(){

    var feed = Wall.feeds.get(this.options.feed_uid);

    var data = $merge(feed.params, {
      'minid': this.options.last_id + 1,
      'mindate':Touch.isTimeline()?this.options.last_date: null,
      'checkUpdate': true
    });
    feed.feed.getElements('.container-get-last').destroy();
    feed.loadFeed(data, 'top');

  },


  loop : function() {
    if( !this.state) {
      this.loop.delay(this.options.delay, this);
      return;
    }

    try {
      this.checkFeedUpdate().addEvent('complete', function() {
        this.loop.delay(1250, this);
      }.bind(this));
    } catch( e ) {
      this.loop.delay(this.options.delay, this);
      this._log(e);
    }
  },
  // Utility

  _log : function(object) {
    if( !this.options.debug ) {
      return;
    }


  }
});



function array_reverse (array, preserve_keys) {
    // Return input as a new array with the order of the entries reversed
    //
    // version: 1107.2516
    // discuss at: http://phpjs.org/functions/array_reverse
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Karol Kowalski
    // *     example 1: array_reverse( [ 'php', '4.0', ['green', 'red'] ], true);
    // *     returns 1: { 2: ['green', 'red'], 1: 4, 0: 'php'}
    var arr_len = array.length,
        newkey = 0,
        tmp_arr = {},
        key = '';
    preserve_keys = !! preserve_keys;

    for (key in array) {
        newkey = arr_len - key - 1;
        tmp_arr[preserve_keys ? key : newkey] = array[key];
    }

    return tmp_arr;
}





Wall.feeds = new Wall.Storage();

Wall.Feed = new Class({
  Implements: [Events, Options],
  options: {
    feed_uid: '',
    enableComposer: true,
    subject_guid: ''
  },
  compose: {},
  params: {},
  watcher: null,

  initialize: function (options)
  {
    this.setOptions(options);
    Wall.feeds = new Wall.Storage();
    Wall.feeds.add(this.options.feed_uid, this);

    this.feed = $(this.options.feed_uid);
    this.initializeComposer();
    this.initializeFeed();
    Wall.globalBind();
    this.fireEvent('complete');

  },

  initializeComposer: function ()
  {
   if ($type(this.feed) != 'element'){
     return ;
   }
   this.compose = new Wall.Composer({
     container: this.feed.getElement('.wall-social-composer'),
     feed_uid: this.options.feed_uid
   });
  },

  initializeFeed: function ()
  {
   var self = this;
   if ($type(this.feed) != 'element'){
     return ;
   }

    Wall.services.getAll().each(function (service){
      service.addEvent('change', function (){
        self.checkServiceLogin(service.options.provider);
      });
    });

    self.addEvent('share', function (service){

      var s_obj = Wall.services.get(service);

      if (s_obj.isEnabled()){
        this.setShareEnabled(service, 1, s_obj.options.object_name);
        Wall.request(en4.core.baseUrl + 'wall/index/service-share', {'provider': service, 'status': 1}, function (){});
      }
    });

    self.addEvent('activity-share', function (service){

      var w_list = window.frames;
      for (var i=0; i<w_list.length; i++){

        if ($type(w_list[i].wallShareActive) == 'function'){
          w_list[i].wallShareActive(service);
        }

      }

    });


   this.initShare();
   this.initStream();
   this.loadList();
   this.initPrivacy();
   this.initAction();

  },

  initShare: function ()
  {
    var self = this;
    var $share = this.feed.getElements('.wallShareMenu a');

    if (!$share){
      return ;
    }

    $share.addEvent('click', function (e){
      e.stop();

      var service = $(this).get('rev');

      if ($(this).hasClass('disabled')){

        var serv_obj = Wall.services.get(service);

        if (serv_obj.isEnabled()){
          self.setShareEnabled(service, 1, serv_obj.options.object_name);
          Wall.request(en4.core.baseUrl + 'wall/index/service-share', {'provider': service, 'status': 1}, function (){});
        } else {
          Wall.services.get(service).auth({'task': 'share'});
        }

      } else {

        var title = en4.core.language.translate('WALL_SHARE_' +  service.toUpperCase());

        $(this).getParent('.service').getElement('.share_input').set('value', 0);
        $(this).addClass('disabled');

        Wall.request(en4.core.baseUrl + 'wall/index/service-share', {'provider': service, 'status': 0}, function (){});

      }


    });
  },

  setShareEnabled: function (provider, enabled, object_name)
  {
    var $share = this.feed.getElement('.wall-share-' + provider);
    if (!$share){
      return ;
    }
    $share.removeClass('disabled');
    this.feed.getElement('input[name="share['+provider+']"]').set('value', enabled);
  },

  initStream: function ()
  {
	var self = this;
    var $link = this.feed.getElement('.wall-list-button');

    if (!$link){
      return ;
    }
    var $types = this.$types = Wall.injectAbsolute($link, this.feed.getElement('.wall-types'), true);
    $link.addEvent('click', function (){
      if ($(this).hasClass('is_active')){
        $(this).removeClass('is_active');
        $types.removeClass('is_active');
      } else {
	  $link.getTop();

		$types.setStyle('right', (window.getWidth() - $link.getLeft()- $link.getWidth())+'px')
		$types.setStyle('top', ($link.getTop()+ $link.getHeight())+'px')
        $(this).addClass('is_active');
        $types.addClass('is_active');
      }

    });

    $$('body')[0].addEvent('click', function (e){
      if (!$(e.target).getParent('.wall-lists')){
        $link.removeClass('is_active');
        $types.removeClass('is_active');
      }
    });


   this.feed.getElements('.wall-stream-type').addEvent('click', function (){
     var type = $(this).get('rev');
     self.feed.getElements('.wall-stream-type').removeClass('is_active');
     $(this).addClass('is_active');
     self.feed.getElements('.wall-stream-option').removeClass('is_active');
     self.feed.getElement('.wall-stream-option-'+type).addClass('is_active');
     self.feed.getElements('.wall-stream').removeClass('is_active');
     self.feed.getElements('.wall-stream-'+type).addClass('is_active');
     if (type != 'social'){
       self.checkServiceLogin(type);
     }
   });

  },

  initPrivacy: function ()
  {
    var self = this;
    var $link = this.feed.getElement('.wallComposer .wall-privacy-link');
    if (!$link){
      return ;
    }

    var $privacy = this.$privacy = Wall.injectAbsolute($link, this.feed.getElement('.wallComposer .wall-privacy'));

    $link.addEvent('click', function (){

  	  window.fireEvent('resize');

      if ($(this).hasClass('is_active')){
        $(this).removeClass('is_active');
        $privacy.removeClass('is_active');
        //var last_pos = parseFloat(sessionStorage.getItem('lastpos'));
        //Touch.scroll.start(0, last_pos);
//        //Touch.scroll.toElement(this);
      } else {
        $privacy.setStyle('left', '0')
        $privacy.setStyle('top', ($link.getTop()+ $link.getHeight())+'px')
        $(this).addClass('is_active');
        $privacy.addClass('is_active');
        //sessionStorage.setItem('lastpos', window.pageYOffset)
        //Touch.scroll.toElement(this);
      }

    });

    $$('body')[0].addEvent('click', function (e){
      if (!$(e.target).getParent('.wall-privacy-container')){
        $link.removeClass('is_active');
        $privacy.removeClass('is_active');
        var last_pos = parseFloat(sessionStorage.getItem('lastpos'));
       // Touch.scroll.start(0, last_pos);
      }
    });

    $privacy.getElements('a').addEvent('click', function (){
      var value = $(this).get('rev');
      self.feed.getElement('.wallComposer .wall_privacy_input').set('value', value);
      $privacy.getElements('a').removeClass('is_active');
      $(this).addClass('is_active');
      //$link.set('text', $(this).getElement('.wall_text').get('text'));
      var last_pos = parseFloat(sessionStorage.getItem('lastpos'));
 //     Touch.scroll.start(0, last_pos);
    });

  },

  initActionPrivacy: function (action)
  {
    var self = this;
    var $link = action.getElement('.wall-privacy-action-link');
    if (!$link){
      return ;
    }

    var $privacy = Wall.injectAbsolute($link, action.getElement('.wall-privacy'), true);

    $link.addEvent('click', function (){

  	  window.fireEvent('resize');

      if ($(this).hasClass('is_active')){
        $(this).removeClass('is_active');
        $privacy.removeClass('is_active');
        var last_pos = parseFloat(sessionStorage.getItem('lastpos'));
    //    Touch.scroll.start(0, last_pos);
      } else {
        $privacy.setStyle('left', '0')
        $privacy.setStyle('top', ($link.getTop()+ $link.getHeight())+'px')
        $(this).addClass('is_active');
        $privacy.addClass('is_active');
        sessionStorage.setItem('lastpos', window.pageYOffset)
      }

    });

    $$('body')[0].addEvent('click', function (e){
      if (!$(e.target).getParent('.wall-privacy-container')){
        $link.removeClass('is_active');
        $privacy.removeClass('is_active');
        var last_pos = parseFloat(sessionStorage.getItem('lastpos'));
    //    Touch.scroll.start(0, last_pos);
      }
    });

    $privacy.getElements('a').addEvent('click', function (){
      var value = $(this).get('rev');
      action.getElement('.wall_privacy_input').set('value', value);
      $privacy.getElements('a').removeClass('is_active');
      $(this).addClass('is_active');
      var element_info = Wall.getActionInfo($link);
      $link.set('text', $(this).getElement('.wall_text').get('text'));
      var last_pos = parseFloat(sessionStorage.getItem('lastpos'));
   //   Touch.scroll.start(0, last_pos);
      Wall.request(en4.core.baseUrl + 'wall/index/change-privacy', {'action_id': element_info.action_id, 'privacy': value});
    });
  },

  initAction: function ($container, params)
  {
    var self = this;

    if (!$container){
      $container = this.getFeed();
    }
    if (!params){
      params = {};
    }

    // Delete action
    $container.getElements('.action-remove').addEvent('click', function (){

      var element = Wall.getActionInfo(this);
      var data = {
        'action_id': element.action_id
      };

      var func = function (){
        var destroyed = false;
        if(Touch.isTimeline()){
          var date = element.action.retrieve('date');
          var date_arr = timeline.tools.dateToArray(date);
          var key = date_arr.year + '-' + date_arr.month;
          var month_els = timeline.feed.get().getElements('li[class*='+key+']');
          if(month_els.length == 0){
            var sep = timeline.separator.get(key);
            if($type(sep) == 'element'){
              sep.destroy();
            }
            destroyed = true;
          }
          var year_els = timeline.feed.get().getElements('li[class*='+date_arr.year+']');
          var i = 0;
          year_els.each(function(el){
            if( !el.hasClass('sep') ){
              i++;
            }
          });
          if(year_els.length > 0 && i == 0){
            timeline.separator.get(date_arr.year).destroy();
            destroyed = true;
          }
          timeline.items.align(element.action.retrieve('date'));
        }
        Wall.request(en4.core.baseUrl + 'wall/index/delete', data, function (obj){
          if (obj.result){
            element.action.destroy();
            if(destroyed){
              timeline.scroller.reload();
            }
            self.checkEmptyFeed();
          }
          Wall.dialog.message(obj.message, obj.result);
        });
      };

      Wall.dialog.confirm('action_remove', func);

    });

    // Comment
    $container.getElements('.action-comment').addEvent('click', function (){

      var element = Wall.getActionInfo(this);
      var $form = element.action.getElement('.wall-comment-form');

      $form.setStyle('display', 'block');
      $form.body.focus();

    });

    // Action Like
    $container.getElements('.action-like').addEvent('click', function (){

      var element = Wall.getActionInfo(this);
      var data = {
        'action_id': element.action_id,
        'checkin': element.checkin
      };

      Wall.request(en4.core.baseUrl + 'wall/index/like/subject/' + self.options.subject_guid, data, function (obj){
        if (obj.status){
          element.action.set('html', obj.body);
          self.initAction(element.action);
        }
      });

    });

    // Action Unlike
    $container.getElements('.action-unlike').addEvent('click', function (){

      var element = Wall.getActionInfo(this);

      var data = {
        'action_id': element.action_id,
        'checkin': element.checkin
      };

      Wall.request(en4.core.baseUrl + 'wall/index/unlike/subject/' + self.options.subject_guid, data, function (obj){
        if (obj.status){
          element.action.set('html', obj.body);
          self.initAction(element.action);
        }
      });

    });

    $container.getElements('.wall-comment-form textarea').autogrow();

    // Post Comment
    $container.getElements('.wall-comment-form').addEvent('submit', function (e){

      e.stop();

      var element = Wall.getActionInfo(this);
      var action_element = Wall.getActionInfo(this);
      var values = $(this).toQueryString();

      values += '&comment_pagination=' + action_element.comment_pagination + '&checkin=' + element.checkin;

      Wall.request(en4.core.baseUrl + 'wall/index/comment/subject/' + self.options.subject_guid, values, function (obj){
        if (obj.status){
          element.action.set('html', obj.body);
          self.initAction(element.action);
        }
      });
    });

    // Comment Like
    $container.getElements('.comment-like').addEvent('click', function (){

      var element = Wall.getCommentInfo(this);
      var action_element = Wall.getActionInfo(this);

      var data = {
        'action_id': action_element.action_id,
        'comment_id': element.comment_id,
        'comment_pagination': action_element.comment_pagination,
        'checkin': action_element.checkin
      };

      Wall.request(en4.core.baseUrl + 'wall/index/like/subject/' + self.options.subject_guid, data, function (obj){
        if (obj.status){
          action_element.action.set('html', obj.body);
          self.initAction(element.action);
        }
      });

    });

    // Comment Unlike
    $container.getElements('.comment-unlike').addEvent('click', function (){

      var element = Wall.getCommentInfo(this);
      var action_element = Wall.getActionInfo(this);

      var data = {
        'action_id': action_element.action_id,
        'comment_id': element.comment_id,
        'comment_pagination': action_element.comment_pagination,
        'checkin': action_element.checkin
      };

      Wall.request(en4.core.baseUrl + 'wall/index/unlike/subject/' + self.options.subject_guid, data, function (obj){
        if (obj.status){
          action_element.action.set('html', obj.body);
          self.initAction(element.action);
        }
      });

    });

    // Comment Delete
    $container.getElements('.comment-remove').addEvent('click', function (){

      var element = Wall.getCommentInfo(this);
      var action_element = Wall.getActionInfo(this);

      var data = {
        'action_id': action_element.action_id,
        'comment_id': element.comment_id,
        'comment_pagination': action_element.comment_pagination,
        'checkin': action_element.checkin
      };

      Wall.dialog.confirm('comment_remove', function (){
        Wall.request(en4.core.baseUrl + 'wall/index/delete', data, function (obj){
          if (obj.result){
            action_element.action.set('html', obj.body);
            self.initAction(element.action);
          }
        });
      });

    });

    // Comment Pagination

    $container.getElements('.comment_next').addEvent('click', function (){

      var $item = $(this);
      var element = Wall.getActionInfo(this);

      element.loader.show();

      var page = $(this).get('rev').substr(5).toInt();

      var data = {
        'action_id': element.action_id,
        'comment_pagination': true,
        'comment_page': page
      };

      Wall.request(en4.core.baseUrl + 'wall/index/view/subject/' + self.options.subject_guid, data, function (obj){

        if (obj.result){

          var html = obj.html.stripScripts(true);
          var $comments = $item.getParent('ul');


          var $container = new Element('div', {'html': html});
          self.initAction($container);

          var $items = $container.getChildren();

          var counter = 0;
          var max = $items.length-1;
          var new_items = [];

          $items.each(function (item){
            new_items[max-counter] = item;
            counter++;
          });

          $comments.getElements('.pagination, .container-comment_likes').destroy();

          new_items.each(function (item){
            item.inject($comments, 'top');
          });

        }

      });

    });


    // View All
    $container.getElements('.utility-viewall .pagination a').addEvent('click', function (){

      var data;
      if(Touch.isTimeline()){
        var rev = $(this).get('rev').split('|');
        data = $merge(self.params, {
          'maxid':(rev[0].substr(6).toInt() - 1),
          'maxdate':rev[1].substr(8)
        });
      } else{
        data= $merge(self.params, {
          'maxid': $(this).get('rev').substr(5).toInt()
        });
      }

      var $loader = $(this).getParent('.utility-viewall').getElement('.loader');
      var loader = new Wall.Loader($loader);

      self.getFeed().getElements('.utility-viewall .pagination').setStyle('display', 'none');
      loader.show();

      self.loadFeed(data, 'bottom', function () {
        loader.hide();
      }, {'viewall': true, preInject: function () {
        self.getFeed().getElements('.utility-viewall').destroy();
      }
      });

    });


    try{
    if (!params.viewall){
        $container.getElements('.utility-setlast').each(function (item){
          if(Touch.isTimeline()){
            var rev = item.get('rev').split('|');
            var last_id = rev[0].substr(5).toInt();
            var last_date = rev[1];
            timeline.feed.object.setLasts(last_date, last_id);
          } else {
          self.setLastId(item.get('rev').substr(5).toInt());
          }
        });
    } else {
    }
    } catch(e){
      alert(e.toSource());
    }

    $container.getElements('.utility-setlast').destroy();

    $container.getElements('.utility-getlast .link').addEvent('click', function (){

      var data;
      if(Touch.isTimeline()){
        data = $merge(self.params, {
          'minid':self.watcher.options.last_id + 1,
          'mindate':self.watcher.options.last_date,
          'getUpdate':true
        });
      } else{
        data = $merge(self.params, {
          'minid': self.watcher.options.last_id+1,
          'getUpdate': true
        });
      }
      $$(this).getParent('.utility-getlast').destroy();

      self.loadFeed(data, 'top');

    });


    // Grouping
//    $container.getElements('.wall_grouped_other').each(function (item){
//    });
    $container.getElements('.comments_comment_likes').addEvent('mouseover', function(event) {

      var el = $(event.target);

      if( !el.retrieve('tip-loaded', false) ) {

        el.store('tip-loaded', true);
        el.store('tip:title', en4.core.language.translate('WALL_LOADING'));
        el.store('tip:text', '');

        var element = Wall.getCommentInfo(this);
        var action_element = Wall.getActionInfo(this);

        Wall.request(en4.core.baseUrl + 'activity/index/get-likes', {
            action_id : action_element.action_id,
            comment_id : element.comment_id
        }, function (responseJSON){
            el.store('tip:title', responseJSON.body);
            el.store('tip:text', '');
        });
      }

    });
    $container.getElements('.wall-privacy-container').each(function (item){
      self.initActionPrivacy(item);
    });
    // Execute scripts
    $container.get('html').stripScripts(true);

    Wall.globalBind();

  },


  setLastId: function (last_id)
  {
    if (!last_id){
      return ;
    }
    this.options.last_id = last_id;
    if (this.watcher){
      this.watcher.options.last_id = last_id;
    }
  },
  setLastDate:function (last_date) {
    this.options.last_date = last_date;
    if (this.watcher) {
      this.watcher.options.last_date = last_date;
    }
  },


  getFeed: function ()
  {
    return this.feed.getElement('.wall-feed');
  },

  checkEmptyFeed: function ()
  {
    if (!this.getFeed().getElements('.wall-action-item').length){
      if (!this.getFeed().getElement('.wall-empty-feed')){
        var element = new Element('li', {'class': 'wall-empty-feed', html: '<div class="tip"><span>' + en4.core.language.translate('WALL_EMPTY_FEED') + '</span></div>'});
        element.inject(this.getFeed(), 'bottom');
      }
    } else {
      this.getFeed().getElements('.wall-empty-feed').destroy();
    }

  },

  loadList: function (html)
  {
    var self = this;

    if (!this.$types){
      return ;
    }

    if (html){
      this.$types.set('html', html);
    }

    this.$types.getElements('li').addEvent('mouseover', function (){
      var $options = $(this).getElement('.options');
      if ($options){
        $options.addClass('is_active');
      }
    });

    this.$types.getElements('li').addEvent('mouseout', function (){
      var $options = $(this).getElement('.options');
      if ($options){
        $options.removeClass('is_active');
      }
    });

    this.$types.getElements('.edit').addEvent('click', function (e){

      e.stop();

      var list_id = $(this).get('rev').substr(5);
      var url = en4.core.baseUrl + 'wall/list/index' + '?list_id=' + list_id;

      Smoothbox.open(new Element('a', {
        'href': url
      }));

    });

    this.$types.getElements('.remove').addEvent('click', function (e){

      e.stop();

      var $element = $(this);

      var list_id = $element.get('rev').substr(5);
      var url = en4.core.baseUrl + 'wall/list/remove' + '?list_id=' + list_id;

      Wall.dialog.confirm('list_remove', function (){
        Wall.request(url, {}, function (obj){
          Wall.dialog.message(obj.message, obj.result);
          if (obj.result){
            self.loadList(obj.html);
            if (list_id == self.params.list_id){
              self.$types.getElements('.item[rev=recent]').fireEvent('click');
            }
          }
      });
      });

    });


    var $loader = self.feed.getElement('.wall-list-button');

    this.$types.getElements('.item').addEvent('click', function (){

      var rev = $(this).get('rev');

      if (rev.substr(0, 4) == 'type'){

        self.$types.getElements('a').removeClass('is_active');
        $(this).addClass('is_active');

        var type = rev.substr(5);

        self.params = {};
        self.params.mode = 'type';
        self.params.type = type;
        self.params.list_id = 0;

        $loader.addClass('loading');
        self.loadFeed(self.params, null, function (){
          $loader.removeClass('loading');
        });

      } else if (rev.substr(0, 4) == 'list'){

        self.$types.getElements('a').removeClass('is_active');
        $(this).addClass('is_active');

        var list_id = rev.substr(5);

        self.params = {};
        self.params.mode = 'list';
        self.params.type = '';
        self.params.list_id = list_id;

        $loader.addClass('loading');
        self.loadFeed(self.params, null, function (){
          $loader.removeClass('loading');
        });

      } else if (rev == 'create-new'){
        en4.core.runonce.trigger();
        Smoothbox.open(new Element('a', {
          'href': en4.core.baseUrl + 'wall/list/index'
        }));

      } else if (rev == 'recent'){

        self.$types.getElements('a').removeClass('is_active');
        $(this).addClass('is_active');

        self.params = {};
        self.params.mode = 'recent';
        self.params.type = '';
        self.params.list_id = 0;

        $loader.addClass('loading');
        self.loadFeed(self.params, null, function (){
          $loader.removeClass('loading');
        });

      }

    });
  },



  loadFeed: function (options, where, oncomplete, params, element)
  {
    var self = this;

    var data = $merge(options, {
      'subject': self.options.subject_guid,
      'feedOnly': true,
      'format': 'html'
    });

    if (!params){
      params = {};
    }

    // set default title
    Wall.activityCount(false);

    var request_fn = function (html){

      var $feed = self.getFeed();

      if (where){

        var $container = new Element('div', {'html': html});
        self.initAction($container, params);

        var $items = $container.getChildren();

        if ($type(params.preInject) == 'function'){
          params.preInject();
        }
        $feed.getElements('.utility-setlast, .utility-getlast').destroy();

        if (where == 'top'){
          $items.reverse();
        }
        // Timeline mods {
        var ranges = '';
        if (element != null && element.hasClass('sep')) {
          if (null != (l = element.getNext()) && l.hasClass('loader')) {
            l.destroy();
          }

          $items.each(function (item) {
            item.inject(element, where);
            element = item;
            if (null != (date = timeline.tools.itemDate(item)) && null != (date_arr = timeline.tools.dateToArray(date))) {
              var str = date_arr.year + '-' + date_arr.month;
              if (ranges.indexOf(str) == -1) {
                ranges += str + ' ';
              }
            }
          });
        } else {
          // } Timeline mods
          $items.each(function (item) {
            item.inject($feed, where);
            // Timeline mods {
            if (Touch.isTimeline() && null != (date = timeline.tools.itemDate(item)) && null != (date_arr = timeline.tools.dateToArray(date))) {
              var str = date_arr.year + '-' + date_arr.month;
              if (ranges.indexOf(str) == -1) {
                ranges += str + ' ';
              }
            }
          });
        }
        if (Touch.isTimeline())
          timeline.separator.scan(ranges);
        // } Timeline mods

//        $items.each(function (item){
//          item.inject($feed, where);
//        });

      } else {
        $feed.set('html', html);
        self.initAction($feed, params);
      }

      if ($type(oncomplete) == 'function'){
        oncomplete();
      }

      self.checkEmptyFeed();

    };

    // Send Request
    Wall.requestHTML(self.options.url_wall, request_fn, null, data);

  },

  checkServiceLogin: function (stream)
  {
    var self = this;

    var $container = self.feed.getElement('.wall-stream-' + stream);

    if (!$container || !$container.getElement('.service-login')) {

      var login_text = en4.core.language.translate('WALL_STREAM_' + stream.toUpperCase() + '_LOGIN', '<a href="javascript:void(0);" class="login" rev="item_' + stream + '">', '</a>');
      var service_login = new Element('div', {'class': 'service-login', 'html': '<div class="tip"><span>' + login_text + '</span></div>'});

      service_login.getElements('.login').addEvent('click', function () {
        Wall.services.get($(this).get('rev').substr(5)).auth({'task': 'stream'});
      });
      service_login.inject($container);
    }
    $container.getElement('.stream-container').setStyle('display', '');
    $container.getElement('.service-login').setStyle('display', 'none');

    var service = Wall.services.get(stream);

    if (!service.isEnabled()) {
      $container.getElement('.stream-container').setStyle('display', 'none');
      $container.getElement('.service-login').setStyle('display', '');
    }
  }



});




Wall.services = new Wall.Storage();

Wall.Service = {};
Wall.Service.Abstract = new Class({

 Implements : [Events, Options],
  options : {
    enabled: false,
    object_id: '',
    object_name: ''
  },

  initialize: function (options)
  {
    this.setOptions(options);
  },

  auth: function (data)
  {
    data = new Hash(data);

    var query = '/';
    data.each(function (item, key){
      query += '' + key + '/' + item + '/';
    });
    window.open(en4.core.baseUrl + 'wall/' + this.getName() + '/index' + query, '', 'HEIGHT=600,WIDTH=850');
  },

  setServiceOptions: function (options)
  {
    this.setOptions(options);
    this.fireEvent('change');
  },

  isEnabled: function ()
  {
    return this.options.enabled;
  },

  getName: function ()
  {
    return this.name;
  }

});



Wall.Service.Facebook = new Class({
  Extends : Wall.Service.Abstract,
  name: 'facebook'
});

Wall.Service.Twitter = new Class({
  Extends : Wall.Service.Abstract,
  name: 'twitter'
});




Wall.Stream = {};

Wall.Stream.Abstract = new Class({

  Implements : [Events, Options],
   options : {
     url: {},
     feed_uid: null
   },
  $container: null,
  $refresh: null,
  is_composer_opened: false,

  name: 'abstract',

  getName: function ()
  {
    return this.name;
  },

  initialize: function (options)
  {
    var self = this;
    this.setOptions(options);

    var feed = Wall.feeds.get(this.options.feed_uid);
    this.$container = feed.feed.getElement('.wall-stream-'+this.getName()+'');
    this.$refresh = feed.feed.getElement('.wall-stream-option-'+this.getName()+' a');

    this.$refresh.addEvent('click', function (){
      this.refresh();
    }.bind(this));

    this.initComposer();
    Wall.globalBind();

    var service = Wall.services.get(this.getName());
    service.addEvent('change', function (){
      if (service.isEnabled()){
        self.refresh(true);
      }
    });


    this.initAction();
    this.init();

  },

  init: function (){},

  initAction: function (){},

  getFeed: function ()
  {
    return this.$container.getElement('.service-feed');
  },

  initComposer: function ()
  {
    var self = this;

    var container = this.$container.getElement('.wallComposer');

    container.getElement('textarea').autogrow();

    container.getElement('.labelBox').addEvent('click', function (){
      this.open();
    }.bind(this));

    container.getElement('.textareaBox .close').addEvent('click', function (){
      this.close();
    }.bind(this));

    container.getElement('.inputBox').addEvent('click', function (){
      $(this).getElement('textarea').focus();
    });

    var loader = new Wall.OverLoader(container.getElement('.wallTextareaContainer'), 'loader2');

    container.getElement('form').addEvent('submit', function (e){

      e.stop();

      loader.show();
      Wall.request(en4.core.baseUrl + 'wall/' + self.getName() + '/post', $(this).toQueryString(), function (){
        loader.hide();
        self.close();
        self.deactivate();
        self.refresh();
      });
    });

  },

  open: function ()
  {
    this.deactivate();

    if (this.is_composer_opened){
      return ;
    }
    this.is_composer_opened = true;

    this.$container.getElements('.toolsBox a').addClass('is_active');
    this.$container.getElement('.labelBox').removeClass('is_active');

    var textarea_box = this.$container.getElement('.textareaBox');

    var fx = new Fx.Morph(textarea_box, {'duration': 50});
    fx.addEvent('onStart', function (){
      textarea_box.setStyles({
        'height': 30,
        'overflow': 'hidden'
      });
      textarea_box.addClass('is_active');
    }.bind(this));

    fx.addEvent('onComplete', function (){
      textarea_box.setStyles({
        'height': 'auto',
        'overflow': 'visible'
      });
      this.$container.getElement('.submitMenu').addClass('is_active');

    }.bind(this));

    fx.start({'height': [30,64]});


  },

  close: function ()
  {
    this.deactivate();

    if (!this.is_composer_opened){
      return ;
    }
    this.is_composer_opened = false;

    var textarea_box = this.$container.getElement('.textareaBox');

    textarea_box.getElement('textarea').set('value', '');

    var fx = new Fx.Morph(textarea_box, {'duration': 50});
    fx.addEvent('onStart', function (){
      this.$container.getElements('.toolsBox a').removeClass('is_active');
      this.$container.getElement('.submitMenu').removeClass('is_active');
      textarea_box.setStyles({
        'height': 64,
        'overflow': 'hidden'
      });
    }.bind(this));

    fx.addEvent('onComplete', function (){
      textarea_box.setStyles({
        'height': 30,
        'overflow': 'visible'
      });
      textarea_box.removeClass('is_active');


      var label = this.$container.getElement('.labelBox');
      var labelFx = new Fx.Morph(label, {'duration': 1000});

      labelFx
          .addEvent('onStart', function (){
            label
                .addClass('is_active')
                .setStyle('opacity', 0);
          })
          .addEvent('onComplete', function (){

          }.bind(this))
          .start({'opacity': [0, 1]});


    }.bind(this));

    fx.start({'height': [64,30]});
  },

  deactivate: function ()
  {
    this.$container.getElement('.textareaBox textarea').set('value', '');
  },

  refresh: function (hide_actions)
  {
    var self = this;

    if (this.$refresh.hasClass('loading')){
      return ;
    }
    this.$refresh.addClass('loading');

    if (hide_actions){
      this.getFeed().getChildren().setStyle('display', 'none');
    }

    Wall.request(en4.core.baseUrl + "wall/" + this.getName() + "/stream", {}, function (obj){

      self.$refresh.removeClass('loading');

      if (!obj.enabled){
        Wall.services.get(self.getName()).setServiceOptions({'enabled': false});
        return ;
      }

      obj.html.stripScripts(true);

      self.getFeed().set('html', obj.html);
      self.initAction(self.getFeed());

    });
  }


});

Wall.Stream.Facebook = new Class({

  Extends : Wall.Stream.Abstract,
  name: 'facebook',

  initAction: function ($container)
  {
    var self = this;

    var $feed = this.getFeed();

    if (!$container){
      $container = $feed;
    }

    $container.getElements('.utility-viewall .pagination a').addEvent('click', function (){

      var loader = $(this).getParent('.utility-viewall').getElement('.loader');
      $feed.getElements('.utility-viewall .pagination').setStyle('display', 'none');

      var next = $(this).get('rev').substr(5);

      loader.show();

      Wall.request(en4.core.baseUrl + "wall/facebook/stream", {'next': next, 'viewall': true}, function (obj){

        if (!obj.enabled){
          Wall.services.get(self.getName()).setServiceOptions({'enabled': false});
          return ;
        }

        obj.html.stripScripts(true);

        var div = new Element('div', {'html': obj.html});
        self.initAction(div);

        var children = div.getChildren();

        $feed.getElements('.utility-viewall').destroy();

        if (children.length){
          children.each(function (item){
            item.inject($feed, 'bottom');
          });
        }

      });
    });

  }


});




Wall.Stream.Twitter = new Class({

  Extends : Wall.Stream.Abstract,
  name: 'twitter',

  initAction: function ($container)
  {
    var self = this;

    var $feed = this.getFeed();

    if (!$container){
      $container = $feed;
    }

    var intentRegex = /twitter\.com(\:\d{2,4})?\/intent\/(\w+)/,
        windowOptions = 'scrollbars=yes,resizable=yes,toolbar=no,location=yes',
        width = 550,
        height = 420,
        winHeight = screen.height,
        winWidth = screen.width;

    var handle = function (e) {
      e = e || window.event;
      var target = e.target || e.srcElement,
          m, left, top;

      while (target && target.nodeName.toLowerCase() !== 'a') {
        target = target.parentNode;
      }

      if (target && target.nodeName.toLowerCase() === 'a' && target.href) {
        m = target.href.match(intentRegex);
        if (m) {
          left = Math.round((winWidth / 2) - (width / 2));
          top = 0;

          if (winHeight > height) {
            top = Math.round((winHeight / 2) - (height / 2));
          }

          window.open(target.href, 'intent', windowOptions + ',width=' + width +
                                             ',height=' + height + ',left=' + left + ',top=' + top);
          e.returnValue = false;
          e.preventDefault && e.preventDefault();
        }
      }
    };

    $container.getElements('a').addEvent('click', handle);


    $container.getElements('.utility-viewall .pagination a').addEvent('click', function (){

      var loader = $(this).getParent('.utility-viewall').getElement('.loader');
      $feed.getElements('.utility-viewall .pagination').setStyle('display', 'none');

      var next = $(this).get('rev').substr(5);

      loader.show();

      Wall.request(en4.core.baseUrl + "wall/twitter/stream", {'next': next, 'viewall': true}, function (obj){

        if (!obj.enabled){
          Wall.services.get(self.getName()).setServiceOptions({'enabled': false});
          return ;
        }

        obj.html.stripScripts(true);

        var div = new Element('div', {'html': obj.html});
        self.initAction(div);
        var children = div.getChildren();

        $feed.getElements('.utility-viewall').destroy();

        if (children.length){
          children.each(function (item){
            item.inject($feed, 'bottom');
          });
        }

      });
    });


  }


});







Wall.Composer = new Class({
  Implements: [Events, Options],
  options: {
    container: '',
    feed_uid: ''
  },
  container: null,
  textarea: null,
  elements: {},
  is_opened: false,
  composer: {},
  is_posted: false,

  initialize: function (options)
  {
    this.setOptions(options);
    this.elements = new Hash(this.elements);
    this.plugins = new Hash(this.plugins);
    this.composer = new Hash(this.composer);
    var container = this.container = $(this.options.container);

    var self = this;

    if ($type(container) != 'element'){
      return ;
    }

    container.getElement('.labelBox').addEvent('click', function (){
      this.open();
    }.bind(this));

    container.getElement('.textareaBox .close').addEvent('click', function (){
      this.close();
    }.bind(this));

    container.getElement('.inputBox').addEvent('click', function (){
      $(this).getElement('textarea').focus();
    });

    container.getElement('textarea').autogrow();

    this.elements.textarea = this.container.getElement('textarea');
    this.elements.textarea.store('Composer');

    this.attach();
    this.getTray();
    this.getMenu();

    this.pluginReady = false;

    var loader = new Wall.OverLoader(container.getElement('.wallTextareaContainer'), 'loader2');

    this.getForm().addEvent('submit', function(e) {

      e.stop();

      if (this.getContent() == '' && !this.pluginReady){
        return ;
      }
      self.saveContent();
      self.fireEvent('editorSubmit');

      self.is_posted = true;

      loader.show();
      Wall.request(en4.core.baseUrl + 'wall/index/post', this.getForm().toQueryString(), function (obj){
        loader.hide();
        // Timeline Mods {
        var element = new Element('div', {'html':obj.body});

        if (Touch.isTimeline() && self.is_timeline) {
          var feed = timeline.feed.object.get();
          feed.initAction(element);

          element.getChildren().each(function (item) {
            timeline.composer.inject(item);
          });

          if (timeline.feed.object.setLasts(obj.last_date, obj.last_id)) {
            feed.checkEmptyFeed();
          }
        } else {
        // } Timeline Mods
        var feed = Wall.feeds.get(this.options.feed_uid);
          // Timeline Mods {
//          element = new Element('div', {'html': obj.body});
          // } Timeline Mods
        feed.initAction(element);
        element.getChildren().each(function (item){
          item.inject(feed.getFeed(), 'top');
        });
        feed.checkEmptyFeed();
        feed.setLastId(obj.last_id);
        // Timeline Mods {
          if(Touch.isTimeline())
            feed.setLastDate(obj.last_date);
        }
        // } Timeline Mods

        this.signalPluginReady(false);
        this.close();
      }.bind(this));
    }.bind(this));

  },


  open: function ()
  {
    this.deactivate();

    if (this.is_opened){
      return ;
    }
    this.is_opened = true;

    this.container.getElements('.toolsBox a').addClass('is_active');
    this.container.getElement('.labelBox').removeClass('is_active');

    var textarea_box = this.container.getElement('.textareaBox');

    var fx = new Fx.Morph(textarea_box, {'duration': 50});
    fx.addEvent('onStart', function (){
      textarea_box.setStyles({
        'height': 30,
        'overflow': 'hidden'
      });
      textarea_box.addClass('is_active');
    }.bind(this));

    fx.addEvent('onComplete', function (){
      textarea_box.setStyles({
        'height': 'auto',
        'overflow': 'visible'
      });
      this.container.getElement('.submitMenu').addClass('is_active');

    }.bind(this));
    if(Touch.isTimeline()){
      var feed = Wall.feeds.get(this.options.feed_uid);
      fx.start({'height':[30, 64]}).chain(function () {
        if (feed.compose.is_timeline) {
          feed.compose.getPlugin('date').activate();
        }
      });
    } else {
      fx.start({'height': [30,64]});
    }
  },

  close: function (post)
  {
    var close_date = Touch.isTimeline();
    this.deactivate(close_date);

    if (!this.is_opened){
      return ;
    }
    this.is_opened = false;

    var textarea_box = this.container.getElement('.textareaBox');

    textarea_box.getElement('textarea').set('value', '');

    var fx = new Fx.Morph(textarea_box, {'duration': 50});
    fx.addEvent('onStart', function (){
      this.container.getElements('.toolsBox a').removeClass('is_active');
      this.container.getElement('.submitMenu').removeClass('is_active');
      textarea_box.setStyles({
        'height': 64,
        'overflow': 'hidden'
      });
    }.bind(this));

    fx.addEvent('onComplete', function (){
      textarea_box.setStyles({
        'height': 30,
        'overflow': 'visible'
      });
      textarea_box.removeClass('is_active');


      var label = this.container.getElement('.labelBox');
      var labelFx = new Fx.Morph(label, {'duration': 1000});

      labelFx
          .addEvent('onStart', function (){
            label
                .addClass('is_active')
                .setStyle('opacity', 0);
          })
          .addEvent('onComplete', function (){

          }.bind(this))
          .start({'opacity': [0, 1]});


    }.bind(this));
    if(Touch.isTimeline()){
      var feed = Wall.feeds.get(this.options.feed_uid);
      fx.start({'height':[64, 30]}).chain(function () {
        if (feed.compose.is_timeline) {
          timeline.composer.close();
        }
      });
    } else
      fx.start({'height': [64,30]});
  },


  getMenu : function() {
    if( !$type(this.elements.menu) ) {

      if( !$type(this.elements.menu) ) {
        this.elements.menu = this.container.getElement('.toolsBox')
      }
    }
    return this.elements.menu;
  },

  getTray : function() {
    if( !$type(this.elements.tray) ) {

      if( !$type(this.elements.tray) ) {
        this.elements.tray = this.container.getElement('.wall-compose-tray');
      }
    }
    return this.elements.tray;
  },

  getInputArea : function() {
    if( !$type(this.elements.inputarea) ) {
      var form = this.elements.textarea.getParent('form');
      this.elements.inputarea = new Element('div', {
        'styles' : {
          'display' : 'none'
        }
      }).inject(form);
    }
    return this.elements.inputarea;
  },

  getForm : function() {
    return this.elements.textarea.getParent('form');
  },
  makeFormInputs : function(data) {

    $H(data).each(function(value, key) {
      this.setFormInputValue(key, value);
    }.bind(this));
  },
  setFormInputValue : function(key, value) {
    var elName = 'composerForm' + key.capitalize();
    if( !this.composer.has(elName) ) {
      this.composer.set(elName, new Element('input', {
        'type' : 'hidden',
        'name' : 'composer[' + key + ']',
        'value' : value || ''
      }).inject(this.getInputArea()));
    }
    this.composer.get(elName).value = value;
  },



  // Editor

  attach : function() {

    ['MouseUp', 'MouseDown', 'ContextMenu', 'Click', 'Dblclick', 'KeyPress', 'KeyUp', 'KeyDown'].each(function(eventName) {
      var method = (this['editor' + eventName] || function(){}).bind(this);
      this.elements.textarea.addEvent(eventName.toLowerCase(), method);
    }.bind(this));

    this.fireEvent('attach', this);

    this.plugins.each(function(){
    });

  },

  detach : function() {
    this.fireEvent('detach', this);
    return this;
  },

  focus: function(){
    // needs the delay to get focus working
    (function(){
      this.elements.textarea.focus();
      this.fireEvent('focus', this);
    }).bind(this).delay(10);
    return this;
  },



  // Content

  getContent: function(){
    return this.cleanup(this.elements.textarea.get('value'));
  },

  setContent: function(newContent){
    this.elements.textarea.set('text', newContent);
    return this;
  },

  saveContent: function(){
    return this;
  },

  cleanup : function(html) {
    // @todo
    return html
      .replace(/<(br|p|div)[^<>]*?>/ig, "\r\n")
      .replace(/<[^<>]+?>/ig, ' ')
      .replace(/(\r\n?|\n){3,}/ig, "\n\n")
      .trim();
  },



  // Plugins

  addPlugin : function(plugin) {
    var key = plugin.getName();
    this.plugins.set(key, plugin);
    plugin.setComposer(this);
    return this;
  },

  addPlugins : function(plugins) {
    plugins.each(function(plugin) {
      this.addPlugin(plugin);
    }.bind(this));
  },

  getPlugin : function(name) {
    return this.plugins.get(name);
  },

  activate : function(name) {
    this.deactivate();
    this.plugins.get(name).activate();
  },

  deactivate : function(close_date) {
    this.plugins.each(function(plugin) {
        if (plugin.name != 'date' || close_date == true) {
        plugin.detach();
        plugin.deactivate();
        }
    });
    if (close_date) {
      this.getTray().empty();
      this.getTray().setStyle('display', 'none');
      this.signalPluginReady(false);
    }
  },

  signalPluginReady : function(state) {
    this.pluginReady = state;
  },

  hasActivePlugin : function() {
    var active = false;
    this.plugins.each(function(plugin) {
      active = active || plugin.active;
    });
    return active;
  },



  // Key events

  editorMouseUp: function(e){
    this.fireEvent('editorMouseUp', e);
  },

  editorMouseDown: function(e){
    this.fireEvent('editorMouseDown', e);
  },

  editorContextMenu: function(e){
    this.fireEvent('editorContextMenu', e);
  },

  editorClick: function(e){
    this.fireEvent('editorClick', e);
  },

  editorDoubleClick: function(e){
    this.fireEvent('editorDoubleClick', e);
  },

  editorKeyPress: function(e){
    this.keyListener(e);
    this.fireEvent('editorKeyPress', e);
  },

  editorKeyUp: function(e){
    this.fireEvent('editorKeyUp', e);
  },

  editorKeyDown: function(e){
    if (e.key == 'enter'){
      /*
      if (this.options.paragraphise && !e.shift){
        if (Browser.Engine.gecko || Browser.Engine.webkit){
          var node = this.selection.getNode();
          var blockEls = /^(H[1-6]|P|DIV|ADDRESS|PRE|FORM|TABLE|LI|OL|UL|TD|CAPTION|BLOCKQUOTE|CENTER|DL|DT|DD)$/;
          var isBlock = node.getParents().include(node).some(function(el){
            return el.nodeName.test(blockEls);
          });
          if (!isBlock) this.execute('insertparagraph');
        }
      } else {
        if (Browser.Engine.trident){
          var r = this.selection.getRange();
          var node = this.selection.getNode();
          if (node.get('tag') != 'li'){
            if (r){
              this.selection.insertContent('<br>');
              this.selection.collapse(false);
            }
          }
          e.preventDefault();
        }
      }
      */
    }

    this.fireEvent('editorKeyDown', e);
  },

  keyListener: function(e){

  },


  _lang : function() {
    try {
      if( arguments.length < 1 ) {
        return '';
      }

      var string = arguments[0];
      if( $type(this.options.lang) && $type(this.options.lang[string]) ) {
        string = this.options.lang[string];
      }

      if( arguments.length <= 1 ) {
        return string;
      }

      var args = new Array();
      for( var i = 1, l = arguments.length; i < l; i++ ) {
        args.push(arguments[i]);
      }

      return string.vsprintf(args);
    } catch( e ) {
    }
  }











});

Wall.Composer.Plugin = {};

Wall.Composer.Plugin.Interface = new Class({

  Implements : [Options, Events],

  name : 'interface',

  active : false,

  composer : false,

  options : {
    loadingImage : 'application/modules/Core/externals/images/loading.gif'
  },

  elements : {},

  persistentElements : ['activator', 'loadingImage'],

  params : {},

  initialize : function(options) {
    this.params = new Hash();
    this.elements = new Hash();
    this.reset();
    this.setOptions(options);
  },

  getName : function() {
    return this.name;
  },

  setComposer : function(composer) {
    this.composer = composer;
    this.attach();
    return this;
  },

  getComposer : function() {
    if( !this.composer ) throw "No composer defined";
    return this.composer;
  },

  attach : function() {
    this.reset();
  },

  detach : function() {
    this.reset();
    if( this.elements.activator ) {
      this.elements.activator.destroy();
      this.elements.erase('menu');
    }
  },

  reset : function() {

    this.elements.each(function(element, key) {
      if( $type(element) == 'element' && !this.persistentElements.contains(key) ) {
        element.destroy();
        this.elements.erase(key);
      }
    }.bind(this));
    this.params = new Hash();
    this.elements = new Hash();
  },



  activate : function() {
    if( this.active ) return;
    this.active = true;

    this.getComposer().open();
    this.getComposer().getTray().setStyle('display', 'block');


    var submitButtonEl = $(this.getComposer().options.submitElement);
    if( submitButtonEl ) {
      submitButtonEl.setStyle('display', 'none');
    }

    switch( $type(this.options.loadingImage) ) {
      case 'element':
        break;
      case 'string':
        this.elements.loadingImage = new Asset.image(this.options.loadingImage, {
          'class' : 'wall-compose-loading-image wall-compose-' + this.getName() + '-loading-image'
        });
        break;
      default:
        this.elements.loadingImage = new Asset.image('loading.gif', {
          'class' : 'wall-compose-loading-image wall-compose-' + this.getName() + '-loading-image'
        });
        break;
    }
  },

  deactivate : function() {
    if( !this.active ) return;
    this.active = false;

    this.reset();

    var submitButtonEl = $(this.getComposer().options.submitElement);
    if( submitButtonEl ) {
      submitButtonEl.setStyle('display', '');
    }
    this.getComposer().signalPluginReady(false);
  },

  ready : function() {

    this.getComposer().signalPluginReady(true);

    var submitEl = $(this.getComposer().options.submitElement);
    if( submitEl ) {
      submitEl.setStyle('display', '');
    }
  },


  // Utility

  makeActivator : function() {
    if( !this.elements.activator ) {

      var t = new Element('a', {
        'class' : 'delimiter',
        'html' : '&nbsp;'
      }).inject(this.getComposer().getMenu());

      this.elements.activator = new Element('a', {
        'class' : 'wall-compose-activator wall-compose-' + this.getName() + '-activator wall_blurlink',
        'href' : 'javascript:void(0);',
        'html' : '&nbsp;',
        'title': this._lang(this.options.title),
        'events' : {
          'click' : this.activate.bind(this)
        }
      }).inject(this.getComposer().getMenu());

//      new Wall.Tips(this.elements.activator);
      new Wall.BlurLink(this.elements.activator);

    }
  },

  makeMenu : function() {
    if( !this.elements.menu ) {
      var tray = this.getComposer().getTray();

      this.elements.menu = new Element('div', {
        'class' : 'wall-compose-container wall-compose-tray-headline  wall-compose-' + this.getName() + '-menu'
      }).inject(tray);

      this.elements.menuSpanWrapper = new Element('div', {
        'class' : 'test'
      }).inject(this.elements.menu);

      this.elements.menuTitle = new Element('span', {
        'html' : this._lang(this.options.title) + ' ('
      }).inject(this.elements.menuSpanWrapper);

      this.elements.menuClose = new Element('a', {
        'href' : 'javascript:void(0);',
        'html' : en4.core.language.translate('cancel'),
        'events' : {
          'click' : function(e) {
            e.stop();

            this.getComposer().deactivate();

          }.bind(this)
        }
      }).inject(this.elements.menuTitle);

      this.elements.menuTitle.appendText(')');
    }
  },

  makeBody : function() {
    if( !this.elements.body ) {
      var tray = this.getComposer().getTray();
      this.elements.body = new Element('div', {
        'class' : 'wall-compose-body wall-compose-' + this.getName() + '-body'
      }).inject(tray);
    }
  },

  makeLoading : function(action) {
    if( !this.elements.loading ) {
      if( action == 'empty' ) {
        this.elements.body.empty();
      } else if( action == 'hide' ) {
        this.elements.body.getChildren().each(function(element){ element.setStyle('display', 'none')});
      } else if( action == 'invisible' ) {
        this.elements.body.getChildren().each(function(element){ element.setStyle('height', '0px').setStyle('visibility', 'hidden')});
      }

      this.elements.loading = new Element('div', {
        'class' : 'wall-compose-loading wall-compose-' + this.getName() + '-loading'
      }).inject(this.elements.body);

      var image = this.elements.loadingImage || (new Element('img', {
        'class' : 'wall-compose-loading-image wall-compose-' + this.getName() + '-loading-image'
      }));

      image.inject(this.elements.loading);

      new Element('span', {
        'html' : this._lang('Loading...')
      }).inject(this.elements.loading);
    }
  },

  makeError : function(message, action) {
    if( !$type(action) ) action = 'empty';
    message = message || 'An error has occurred';
    message = this._lang(message);

    this.elements.error = new Element('div', {
      'class' : 'wall-compose-error wall-compose-' + this.getName() + '-error',
      'html' : message
    }).inject(this.elements.body);
  },

  makeFormInputs : function(data) {
    this.ready();

    this.getComposer().getInputArea().empty();

    data.type = this.getName();

    $H(data).each(function(value, key) {

      this.setFormInputValue(key, value);
    }.bind(this));
  },

  setFormInputValue : function(key, value) {
    var elName = 'attachmentForm' + key.capitalize();
    if( !this.elements.has(elName) ) {
      this.elements.set(elName, new Element('input', {
        'type' : 'hidden',
        'name' : 'attachment[' + key + ']',
        'value' : value || ''
      }).inject(this.getComposer().getInputArea()));
    }
    this.elements.get(elName).value = value;
  },

  _lang : function() {
    try {
      if( arguments.length < 1 ) {
        return '';
      }

      var string = arguments[0];
      if( $type(this.options.lang) && $type(this.options.lang[string]) ) {
        string = this.options.lang[string];
      }

      if( arguments.length <= 1 ) {
        return string;
      }

      var args = new Array();
      for( var i = 1, l = arguments.length; i < l; i++ ) {
        args.push(arguments[i]);
      }

      return string.vsprintf(args);
    } catch( e ) {
    }
  }

});


Wall.camera_list = new Wall.Storage();

Wall.Camera = new Class({

  Implements: [Events, Options],
  options: {
  },

  shootEnabled: true,

  initialize: function (options)
  {
    var self = this;

    this.createDom();

//    window.addEvent('resize', function (){
//      self.build();
//    });

    Wall.setKeyEvent(function (e){
      if (e.key == 'esc'){
        Wall.camera_list.getAll().each(function (item){
          item.viewClose();
        });
      }
      if (e.key == 'space'){
        Wall.camera_list.getAll().each(function (item){
          item.fireEvent('freeze');
        });
      }
    });
    Wall.camera_list.add($random(1111,9999), this);

    this.camera.addEvent('click', function (e){

      if (e){
        e.stop();
        if (!$(e.target).getParent('body') || $(e.target).getParent('.container') || $(e.target).hasClass('container')){
          return ;
        }
      }
      self.viewClose();
    });


    webcam.set_hook('onLoad',function(){
      self.shootEnabled = true;
    });

    webcam.set_hook('onComplete', function(msg){

      self.upload.removeClass('loading');

      msg = JSON.decode(msg);

      if(msg.error){
      }
      else {
        self.fireEvent('success', msg);
        self.viewClose();
      }
    });

    webcam.set_hook('onError',function(e){
    });


    this.close.addEvent('click', function (){
      self.viewClose();
    });
    this.cancel.addEvent('click', function (){
      self.togglePane();
      webcam.reset();
    });
    this.freeze.addEvent('click', function (){
      self.togglePane();
      if(!self.shootEnabled){
        return false;
      }
      webcam.freeze();
    });
    this.upload.addEvent('click', function (){
      self.upload.addClass('loading');
      webcam.upload();
    });


    this.createBox();

    Wall.globalBind();

  },

  viewClose: function ()
  {
    this.fireEvent('cancel');
    this.camera.destroy();
    $$('html')[0].removeClass('wall_hidescrool');
  },

  createBox: function ()
  {
    this.camera.inject(Wall.externalDiv());

    webcam.set_swf_url(en4.core.basePath + 'application/modules/Wall/externals/webcam/webcam.swf');
    webcam.set_api_url( en4.core.baseUrl + 'wall/camera/upload?format=json');	// The upload script
    webcam.set_quality(80);				// JPEG Photo Quality
    webcam.set_shutter_sound(true, en4.core.basePath + 'application/modules/Wall/externals/webcam/shutter.mp3');
    $('wall_camera_embed').set('html', webcam.get_html(600, 480));


    $$('html')[0].addClass('wall_hidescrool');
    this.build();
  },

  togglePane: function ()
  {
    var self = this;

    if (this.upload.isVisible()){

      var fx = new Fx.Morph(this.freeze, {'duration': 500});
      this.freeze.setStyle('display', 'none');
      fx.start({'opacity': [0,1]});
      fx.addEvent('complete', function (){
        self.freeze.setStyle('display', 'block');
      });

      var fx2 = new Fx.Morph(this.cancel, {'duration': 500});
      this.cancel.setStyle('display', 'block');
      fx2.start({'opacity': [1,0]});
      fx2.addEvent('complete', function (){
        self.cancel.setStyle('display', 'none');
      });

      var fx3 = new Fx.Morph(this.upload, {'duration': 500});
      this.upload.setStyle('display', 'block');
      fx3.start({'opacity': [1,0]});
      fx3.addEvent('complete', function (){
        self.upload.setStyle('display', 'none');
      });


    } else {

      var out_fx = new Fx.Morph(this.freeze, {'duration': 500});
      this.freeze.setStyle('display', 'block');
      out_fx.start({'opacity': [1,0]});
      out_fx.addEvent('complete', function (){
        self.freeze.setStyle('display', 'none');
      });

      var out_fx2 = new Fx.Morph(this.cancel, {'duration': 500});
      this.cancel.setStyle('display', 'none');
      out_fx2.start({'opacity': [0,1]});
      out_fx2.addEvent('complete', function (){
        self.cancel.setStyle('display', 'block');
      });

      var out_fx3 = new Fx.Morph(this.upload, {'duration': 500});
      this.upload.setStyle('display', 'none');
      out_fx3.start({'opacity': [0,1]});
      out_fx3.addEvent('complete', function (){
        self.upload.setStyle('display', 'block');
      });

    }
  },

  createDom: function ()
  {
    this.camera = new Element('div', {'class': 'wall-camera wall-window'});
    this.container = new Element('div', {'class': 'container'});

    if (!Wall.isLightTheme()){
      this.container.addClass('night_theme');
    }
    if (!!navigator.userAgent.match(/MSIE/)){
      this.camera.addClass('ie');
    }

    this.close = new Element('a', {'href': 'javascript:void(0);', 'class': 'close wall_blurlink'});
    this.embed = new Element('div', {'class': 'embed', 'id': 'wall_camera_embed'});



    this.submit_menu = new Element('div', {'class': 'submit-menu'});
    this.freeze = new Element('a', {'href': 'javascript:void(0);', 'class': 'freeze wall-button wall_blurlink', 'html': '<span class="icon"></span>' + en4.core.language.translate('WALL_CAMERA_FREEZE')});
    this.cancel = new Element('a', {'href': 'javascript:void(0);', 'style': 'display:none', 'class': 'cancel wall-button wall_blurlink', 'html': '<span class="icon"></span>' + en4.core.language.translate('WALL_CAMERA_CANCEL')});
    this.upload = new Element('a', {'href': 'javascript:void(0);', 'style': 'display:none', 'class': 'upload wall-button wall_blurlink', 'html': '<span class="icon"></span>' + en4.core.language.translate('WALL_CAMERA_UPLOAD')});

    this.close.inject(this.camera);
    this.embed.inject(this.container);

    this.freeze.inject(this.submit_menu);
    this.cancel.inject(this.submit_menu);
    this.upload.inject(this.submit_menu);
    this.submit_menu.inject(this.container);

    this.container.inject(this.camera);


  },

  build: function ()
  {
    var pos = this.container.getCoordinates();
    var total = this.camera.getCoordinates();

    this.container
      .setStyle('position', 'absolute')
      .setStyle('left', (total.width/2 - pos.width/2))
      .setStyle('top', 20)
      //.setStyle('top', (total.height/2 - pos.height/2))
      ;

  }


});




Wall.lightHexColor = function (hex)
{
  if (hex[0]=="#") hex=hex.substr(1);
  if (hex.length==3) {
    var temp=hex; hex='';
    temp = /^([a-f0-9])([a-f0-9])([a-f0-9])$/i.exec(temp).slice(1);
    for (var i=0;i<3;i++) hex+=temp[i]+temp[i];
  }
  var triplets = /^([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i.exec(hex).slice(1);

  return 0.213 * parseInt(triplets[0],16) + 0.715 * parseInt(triplets[1],16) + 0.072 * parseInt(triplets[2],16);
};


Wall.lightDiv = null;

Wall.isLightTheme = function ()
{
  if (!Wall.lightDiv){
    Wall.lightDiv = new Element('div', {style: 'display:none', 'class': 'wall-theme-foreground'});
    Wall.lightDiv.inject(Wall.externalDiv());
  }
  var hex = Wall.lightDiv.getStyle('background-color');
  if (hex == 'transparent'){
    hex = Wall.lightDiv
      .removeClass('wall-theme-foreground')
      .addClass('wall-theme-background')
      .getStyle('background-color');
  }

  return Wall.lightHexColor(hex)>100;

};
