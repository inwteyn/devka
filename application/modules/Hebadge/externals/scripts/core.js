/* $Id: core.js 02.04.12 09:12 michael $ */


Hebadge = {};


Hebadge.is_request = false;
Hebadge.request = function (url, data, callback)
{
  if (typeof(data) == 'object'){
    data.format = 'json';
  } else if (typeof(data) == 'string'){
    data += '&format=json';
  } else {
    data = {};
    data.format = 'json';
  }
 Hebadge.is_request = true;

 (new Request.JSON({
    secure: false,
    url: url,
    method: 'post',
    data: data,
    onSuccess: function(obj) {
      Hebadge.is_request = false;
      if ($type(callback) == 'function') {
        callback(obj);
      }
      en4.core.runonce.trigger();
    }
  })).send();

};

Hebadge.requestHTML = function (url, callback, $container, data)
{
  if ($type(data) == 'object'){
    data = $merge({'format': 'html'}, data);
  } else if ($type(data) == 'string'){
    data += '&format=html';
  }

  Hebadge.is_request = true;

  var request = new Request.HTML({
    'url': url,
    'method': 'get',
    'data': data,
    'evalScripts' : false,
    'onComplete': function (responseTree, responseElements, responseHTML, responseJavaScript){

      Hebadge.is_request = false;

      if ($container && $type($container) == 'element'){
        $container.set('html', responseHTML);
      }
      if ($type(callback) == 'function'){
        callback(responseHTML);
      }
      eval(responseJavaScript);
      en4.core.runonce.trigger();
    }
  });
  request.send();
};




Hebadge.TipFx = new Class({

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

    this.$container = new Element('div', {'class': 'hebadges-tips ' + (this.options.class_var || ''), style: 'display:none'});
    this.$inner = new Element('div', {'class': 'container', 'html': (this.options.html || '')});


    if (this.options.is_arrow){
      this.$arrow_container = new Element('div', {'class': 'arrow_container'});
      this.$arrow = new Element('div', {'class': 'arrow'});
    }

    this.$inner.inject(this.$container);

    if (this.options.is_arrow){
      this.$arrow.inject(this.$arrow_container);
      this.$arrow_container.inject(this.$container);
    }

    this.$container.inject(Hebadge.externalDiv());

    window.addEvent('resize', function (){
      this.build();
    }.bind(this));

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

    var dir = 'ltr';
    if ($$('html')[0]){
      dir = $$('html')[0].get('dir');
    }

    this.$container.setStyle('display', '');

    var e_pos;

    if ($type(this.options.relative_element) == 'element'){
      e_pos = this.options.relative_element.getCoordinates();
    } else {
      e_pos = this.element.getCoordinates();
    }


    var c_pos = this.$container.getCoordinates();

    this.$container
      .setStyle('display', 'none')
      .setStyle('padding-bottom', 2);

    var rebuild = function (left, top){

      if (left){
        this.$container.setStyle('left', e_pos.left);
        if (this.options.is_arrow){
          var left = (e_pos.width/2-2.5).toInt();
          if (left>c_pos.width/2-2.5){
            left = 10;
          }
          this.$arrow.setStyle('left', left);
        }
      } else {
        this.$container.setStyle('left', e_pos.left-(c_pos.width-e_pos.width));
        if (this.options.is_arrow){
          var right = (e_pos.width/2-2.5).toInt();
          if (right>c_pos.width/2-2.5){
            right = 10;
          }
          this.$arrow.setStyle('right', right);
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

      if (dir == 'rtl'){
        rebuild(0,1);
      } else {
        rebuild(1,1);
      }
    }


  }

});

Hebadge.Tips = new Class({

  Extends: Hebadge.TipFx,

  name: 'Hebadge.Tips',

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

Hebadge.elementClass = function ()
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
    var instance = Hebadge.elements.get(key);

    if (instance){
      return instance;
    }

    newClass._prototyping = true;
    newClass.$prototyping = true;
    instance = new newClass();
    delete newClass._prototyping;
    delete newClass.$prototyping;

    newClass.prototype.initialize.apply(instance, options.slice(1));

    Hebadge.elements.add(key, instance);

    return instance;

  }
};



Hebadge.Storage = new Class({

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



Hebadge.elements = new Hebadge.Storage();

Hebadge.$external_div = null;

Hebadge.externalDiv = function (){
  if (!this.$external_div || $type(this.$external_div) != 'element'){
    this.$external_div = new Element('div', {'class': 'hebadges-element-external'});
    this.$external_div.inject($$('body')[0]);
  }
  return this.$external_div;
};



Hebadge.attachBadge = function ()
{
  $$('.hebadge_item_photo:not(.hebadge_item_photo_active)').each(function (item){

    item.addClass('hebadge_item_photo_active');

    if (!item.get('onclick')){
      return ;
    }
    try {
      var data = eval('(function(){' + item.get('onclick') + '})()');
    } catch (e){}

    if (!data){
      return
    }

    var parent = item.getParent();

    if (parent.get('tag') != 'a' && !parent.hasClass('he_rate_thumb')){
      return ;
    }
    if (parent.getParent('#global_search_form_container')){
      return ;
    }

    parent.setStyle('position', 'relative');
    parent.setStyle('display', 'inline-block');

    item.inject(parent, 'top');

    var badge = new Element('span', {'class': 'hebadge_profile_icon', 'html': '&nbsp;'});

    badge.setStyle('background-position', '0 16px');

    badge.inject(parent, 'bottom');

    badge.tween('background-position', ['0 16px', '0 0px']);

  });


};

window.addEvent('load', function()
{
  (function(){
    Hebadge.attachBadge();
  }).periodical(1000);
});