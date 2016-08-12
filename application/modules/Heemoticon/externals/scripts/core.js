/**
 * Created by bolot on 04.03.2015.
 */


var Heemoticon = new Class({

  second:0,
  composer_open :0,
  smiles: window.emoticons,
  initSmiles: function () {
    var self = this;
    var contaners = $$('.heemoticons:not(.hasEvent)');

    contaners.each(function (el) {
      el.set('onclick','');
      el.addEvent('click', function () {
        if(window.opensmile == 1 && $('wall_emoticon_contaner')){
          return;
        }
        window.opensmile = 1;
        self.getSmiles(this);
      });
      el.addClass('hasEvent');
    });
    this.initTimeOut();
  },

  initTimeOut: function(){
    var interval_id = setInterval(function(){

    },5000);
  },

  getSmiles: function (links) {

    var self = this;
    if (!this.second && links) {
      self.hideSmile();
      if ($('wall_comment_smile')) {
        var elem = document.getElementById("wall_comment_smile");
        elem.parentNode.removeChild(elem);
      }
      $$('body')[0].addEvent('click', function (e) {
        if (!e.target.getParent('#wall_comment_smile') && !e.target.hasClass('emoticon-icons') && !e.target.getParent('#smile_composer_comment-element') && e.target.get('id') != 'addEmoticonContanerBackground' && !e.target.getParent('#addEmoticonContaner') && !e.target.getParent('.header-smiles-contaner') && !e.target.getParent('.tabs_smiles')
          && !e.target.hasClass('wall-compose-smile-o-activator')  && !e.target.hasClass('wall-compose-smile-o-activator') && !e.target.getParent('#smile_composer_comment-element') && e.target.get('id') != 'addEmoticonContanerBackground' && !e.target.getParent('#addEmoticonContaner') && !e.target.getParent('.header-smiles-contaner') && !e.target.getParent('.tabs_smiles')) {
          self.hideSmile();
        }
      });
      var link = links;
      var form = link.getParent('form');

      var contaner = new Element('div');
      var container = self.injectAbsoluteCommentSmile(link, contaner, 15);
      var toptest = $(link).getCoordinates();
      if(window.getSize().y - (toptest.top - window.scrollY) <= 360){
        container.set('html', '<div class="wall_arrow_container"><div class="wall_arrow" style="top: 339px;box-shadow: 3px 5px 10px -1px rgba(0, 0, 0, 0.5);"></div></div><div class="loader_smiles_ajax"></div>');
        var type_mode = 'bottom';
      }else{
        container.set('html', '<div class="wall_arrow_container"><div class="wall_arrow"></div></div><div class="loader_smiles_ajax"></div>');
        var type_mode = 'top';
      }

    } else {
      var form = $($('wall_emoticon_contaner').get('par'));
      contaner = $('wall_emoticon_contaner');
      container = $('wall_emoticon_contaner');

    }
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'heemoticon/index/index?format=html&type='+type_mode,

      onComplete: function (response) {
        container.set('html', '');
        contaner.innerHTML = response;

        contaner.getElements('.smiles_standart').addEvent('click', function () {
          var body_in = form.getChildren('#body')[0].value;
          form.getChildren('#body')[0].value = body_in + ' ' + $(this).get('rev') + ' ';
          form.getChildren('#submit')[0].setStyle('display', 'block');
          self.hideSmile();
        });
        var cont = contaner.getElements('.wall_data_comment');
        var opt = {
          autoHide: 1,
          fade: 1,
          className: 'scrollbar',
          proportional: true,
          proportionalMinHeight: 15,
          left: 295,
          top: 50
        }

          var myScrollable = new Scrollable(cont,opt);

        contaner.getElements('.smiles_NEW').addEvent('click', function () {


          var comment_id = form.get('id').split('-').pop();
          form.getChildren('#submit').setStyle('display','block');
          var load = $('comment_attach_loading_wall'+comment_id);
          load.setStyle('display','block');
          var contaner_img = $('comment_attach_preview_image_wall' + comment_id);
          contaner_img.set('html','');
          var sticker_id = this.get('data-id');
          var req = new Request({
            method: 'get',
            url: en4.core.baseUrl +  'heemoticon/index/poststicker?format=html&sticker_id='+ sticker_id+'&comment_id='+comment_id,
            onComplete: function (response) {
              var elem = new Element('a', {
                'class':'smiles_NEW'
              });
              var img = new Element('img', {
                'src':response.trim(),
                  'sticker_id':sticker_id
              });
              img.inject(elem);

              elem.inject(contaner_img);
              contaner_img.setStyle('display', 'block');
              var used_id = 0;
              var row = response.trim().split('?').pop();
              if(row){used_id=row.split('=').pop();}
              var delete_button = new Element('div', {
                'id': 'delete_' + comment_id,
                'class': 'wpClose hei hei-times delete_photo_in_comment_button'
              }).inject(contaner_img);

              delete_button.addEvent('click', function(e){
                self.deleteImage(used_id,sticker_id,comment_id);
              });
              self.hideSmile();
              load.setStyle('display','none');

            }
          }).send();

        });

        window.opensmile = 0;
        if($$('.tabs_smiles').length) {
          $$('.tabs_smiles')[0].getChildren('a').each(function (el) {
            new Wall.Tips(el);
            new Wall.BlurLink(el);
          });
        }
      }
    }).send();


  },
  getComposerSmiles: function (links,wallcomposer){
    var composer =  wallcomposer.getComposer().editor;
    var tray  =  wallcomposer.getComposer().getTray();
    var body = wallcomposer.elements.body;
    this.composer_open = 1;
    var self = this;

    if(!this.second && links ){
      self.hideSmile();
      if ($('wall_comment_smile')) {
        var elem = document.getElementById("wall_comment_smile");
        elem.parentNode.removeChild(elem);
      }
      $$('body')[0].addEvent('click', function (e) {
        if (!e.target.getParent('#wall_comment_smile') && !e.target.hasClass('emoticon-icons') && !e.target.hasClass('wall-compose-smile-o-activator') && !e.target.hasClass('wall-compose-smile-o-activator') && !e.target.getParent('#smile_composer_comment-element') && e.target.get('id') != 'addEmoticonContanerBackground' && !e.target.getParent('#addEmoticonContaner') && !e.target.getParent('.header-smiles-contaner') && !e.target.getParent('.tabs_smiles')
          && !e.target.hasClass('wall-compose-smile-o-activator')  && !e.target.hasClass('wall-compose-smile-o-activator') && !e.target.getParent('#smile_composer_comment-element') && e.target.get('id') != 'addEmoticonContanerBackground' && !e.target.getParent('#addEmoticonContaner') && !e.target.getParent('.header-smiles-contaner') && !e.target.getParent('.tabs_smiles')) {
          self.hideSmile();
        }
      });
      var link = links;
      var form = link.getParent('form');

      var contaner = new Element('div');

      var container = self.injectAbsoluteCommentSmile(link, contaner, 0);
      container.set('html', '<div class="wall_arrow_container"><div class="wall_arrow"></div></div><div class="loader_smiles_ajax"></div>');
    }else{
      var form =  $($('wall_emoticon_contaner').get('par'));
      contaner = $('wall_emoticon_contaner');
      container = $('wall_emoticon_contaner');

    }
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'heemoticon/index/index?format=html',

      onComplete: function (response) {
        container.set('html', '');
        contaner.innerHTML = response;

        contaner.getElements('.smiles_standart').addEvent('click', function () {

          composer.setContent(composer.getContent() + '&nbsp;' + $(this).get('rev') + '&nbsp;');
          composer.moveCaretToEnd();

          self.hideSmile();
        });
        var cont = contaner.getElements('.wall_data_comment');
        var opt = {
          autoHide: 1,
          fade: 1,
          className: 'scrollbar',
          proportional: true,
          proportionalMinHeight: 15,
          left: 295,
          top: 50
        }

        var myScrollable = new Scrollable(cont,opt);

        contaner.getElements('.smiles_NEW').addEvent('click', function () {




          var contaner_img = body;
          tray.setStyle('display','block');
          contaner_img.set('html','');
          var sticker_id = this.get('data-id');
          var req = new Request({
            method: 'get',
            url: en4.core.baseUrl +  'heemoticon/index/poststicker?format=html&sticker_id='+ sticker_id+'&comment_id='+0,
            onComplete: function (response) {
              var elem = new Element('a', {
                'class':'smiles_NEW'
              });
              var img = new Element('img', {
                'src':response.trim(),
                'sticker_id':sticker_id
              });
              img.inject(elem);

              elem.inject(contaner_img);
              contaner_img.setStyle('display', 'block');
              var used_id = 0;
              var row = response.trim().split('?').pop();
              if(row){used_id=row.split('=').pop();}
              var delete_button = new Element('div', {
                'id': 'delete_' + 0,
                'class': 'wpClose hei hei-times delete_photo_in_comment_button'
              }).inject(contaner_img);
              wallcomposer.parent({
                'emoticon_id': sticker_id,
                'type': 'heemoticon_post'
              });
              delete_button.addEvent('click', function(e){
                self.deleteImageComposer(used_id,sticker_id,0);
              });
              self.hideSmile();

            }
          }).send();

        });

        window.opensmile = 0;
        if($$('.tabs_smiles').length) {
          $$('.tabs_smiles')[0].getChildren('a').each(function (el) {
            new Wall.Tips(el);
            new Wall.BlurLink(el);
          });
        }

      }
    }).send();


  },
  hideSmile: function () {
    if ($('wall_emoticon_contaner')) {
      var elem = document.getElementById("wall_emoticon_contaner");
      elem.parentNode.removeChild(elem);
    }
  },
  injectAbsoluteCommentSmile: function (element, container,plus) {
    element = $(element);
    container = $(container);

    if ($type(element) != 'element' || $type(container) != 'element') {
      return;
    }

    var build = function () {
      var pos = element.getCoordinates();
      var form = element.getParent('form');

      container
        .setStyle('position', 'absolute')
        .setStyle('height', '331px')
        .setStyle('z-index', '999')
        .setStyle('right', ($$('body')[0].getCoordinates().width - pos.left - pos.width) - plus);
      container.set('id', 'wall_emoticon_contaner')
      container.set('par', form.get('id'));

      if(window.getSize().y - (pos.top - window.scrollY) <= 360){

        container.setStyle('top', pos.top - 365 );
      }else{
        container.setStyle('top', pos.top + pos.height);
      }
    };

    container.inject(Wall.externalDiv(), 'bottom');
    build();


    return container;

  },

  showEmoticonById: function (id,element) {
    $$('.smiles_contaner').each(function (el) {
      el.setStyle('display', 'none');
    });
    if (id == -1) {
      $('smiles_colection_standart').setStyle('display', 'block');
    } else {
      var cont = $('smiles_colection_' + id);
      cont.setStyle('display', 'block');


    }
    $$('.heemoticon_smiles').each(function (el) {
      el.set('class','heemoticon_smiles')
    });
    if(element){
      element.set('class','heemoticon_smiles active')
    }



  },
  addNewSmiles: function () {
    var self = this;
    if (!$('addEmoticonContanerBackground')) {
      var background = new Element('div', {
        'style': 'position:fixed; top:0; left:0; z-index:1000;background:rgba(0,0,0,0.4); height: 100%;width:100%',
        'class': 'addEmoticonContaner',
        'id': 'addEmoticonContanerBackground'
      });
    } else {
      var background = $('addEmoticonContanerBackground');
    }
    background.addEvent('click', function () {
      self.hideSelectSmile();
    })
    background.inject(Wall.externalDiv(), 'bottom');
    var window_width = window.getSize().x;
    var hecontaner = (window_width - 570) / 2;
    if (!$('addEmoticonContaner')) {
      var container = new Element('div', {
        'style': 'position:fixed; top:100px; z-index:1001;left:' + hecontaner + 'px;  width:570px',
        'class': 'addEmoticonContaner',
        'id': 'addEmoticonContaner'
      });
    } else {
      var container = $('addEmoticonContaner');
    }
/*    if ($('wall_emoticon_contaner')) {
      var elem = document.getElementById("wall_emoticon_contaner");
      elem.parentNode.removeChild(elem);

    }*/

    container.inject(Wall.externalDiv(), 'bottom');
    container.set('html', '<div class="loader_smiles_ajax_load_news"></div>');
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'heemoticon/index/addsmiles?format=html',
      onComplete: function (response) {

        container.set('html', response);
        var window_height = window.getSize().y;
        var heContent = window_height - 400;
        if ($('addEmoticonContaner')) {
          var contaner = $('addEmoticonContaner').getElement('.smile_store_content');
          contaner.setStyle('height', heContent + 'px');
          var myScrollable = new Scrollable(contaner);
        }
      }
    }).send();
  },

  addCollectionSticers: function (id, elem) {
    var self = this;
    elem.set('html', 'Loading');
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'heemoticon/index/setcollection?id=' + id + '&status=1&format=html',
      onComplete: function (response) {
        if(response.trim().toInt() == 0){
          $$('.smile_store_content').set('html','<div style="margin: 10px; font-size: 20px">'+en4.core.language.translate('Your level does not permit to see this collection.')+'<a href="javascript:void(0)" onclick="window.heemotion.addNewSmiles()">'+en4.core.language.translate('Back')+'</a></div>');
        }else {
          elem.set('html', en4.core.language.translate('Remove'));
          elem.set('onclick', 'window.heemotion.removeCollectionSticers(' + id + ',this)');
          self.second = 1;
          if(this.composer_open == 1){
            self.getComposerSmiles(false);
          }else {
            self.getSmiles(false);
          }
        }
      }
    }).send();
  },

  buyCollectionSticers: function (id, elem) {
    var self = this;
    var buttonName = elem.get('html');
    elem.set('html', 'Loading');

    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'heemoticon/index/setcollection?id=' + id + '&status=1&format=html',
      onComplete: function (response) {
        if (response.trim().toInt() == 0) {
          $$('.smile_store_content').set('html', '<div style="margin: 10px; font-size: 20px">' + en4.core.language.translate('Your level does not permit to see this collection.') + '<a href="javascript:void(0)" onclick="window.heemotion.addNewSmiles()">' + en4.core.language.translate('Back') + '</a></div>');
        } else {
          if (response.trim().toInt() < 0) {
            elem.set('html', buttonName);
            return;
          }
          elem.set('html', en4.core.language.translate('Remove'));
          elem.set('onclick', 'window.heemotion.removeCollectionSticers(' + id + ',this)');
          self.second = 1;
          if (this.composer_open == 1) {
            self.getComposerSmiles(false);
          } else {
            self.getSmiles(false);
          }
        }
      }
    }).send();
  },
  removeCollectionSticers: function (id, elem) {
    var self = this;
    elem.set('html', 'Loading');
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'heemoticon/index/setcollection?id=' + id + '&status=2&format=html',
      onComplete: function (response) {
        elem.set('html', en4.core.language.translate('Add'));
        elem.set('onclick', 'window.heemotion.addCollectionSticers(' + id + ',this)');
        self.second = 1;
        if (this.composer_open == 1) {
          self.getComposerSmiles(false);
        } else {
          self.getSmiles(false);
        }
      }
    }).send();
  },
  hideSelectSmile: function () {
    if ($('addEmoticonContanerBackground')) {
      var elem = document.getElementById("addEmoticonContanerBackground");
      elem.parentNode.removeChild(elem);

    }
    if ($('addEmoticonContaner')) {
      var elem = document.getElementById("addEmoticonContaner");
      elem.parentNode.removeChild(elem);

    }
    this.composer_open = 0;
  },
  viewEmoticonsDetails: function (id) {
    var elem = $('addEmoticonContaner');
    elem.set('html', '<div class="loader_smiles_ajax_load_news"></div>');
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'heemoticon/index/view?id=' + id + '&format=html',
      onComplete: function (response) {
        elem.set('html', response);
        var window_height = window.getSize().y;
        var heContent = window_height - 400;
        var cont = elem.getElement('#smiles_colection');
        var top =  elem.getElement('.sticker-description').getSize().y;
        cont.setStyle('height',heContent+'px');
        var opt = {
          autoHide: 1,
          fade: 1,
          className: 'scrollbar',
          proportional: true,
          proportionalMinHeight: 15,
          left: 557,
          top: top + 45
        }
        var myScrollable = new Scrollable(cont,opt);
      }
    }).send();
  },
  viewBuyEmoticonsDetails: function (id) {
    var elem = $('addEmoticonContaner');
    elem.set('html', '<div class="loader_smiles_ajax_load_news"></div>');
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'heemoticon/index/view-buy?id=' + id + '&format=html',
      onComplete: function (response) {
        elem.set('html', response);
        var window_height = window.getSize().y;
        var heContent = window_height - 400;
        var cont = elem.getElement('#smiles_colection');
        var top =  elem.getElement('.sticker-description').getSize().y;
        cont.setStyle('height',heContent+'px');
        var opt = {
          autoHide: 1,
          fade: 1,
          className: 'scrollbar',
          proportional: true,
          proportionalMinHeight: 15,
          left: 557,
          top: top + 45
        };
        var myScrollable = new Scrollable(cont,opt);
      }
    }).send();
  },
  scrollRightSmiles: function () {
    var elem = $$('.tabs_smiles');
    var max = ($$('.tabs_smiles').getChildren('a')[0].length / 4).toInt();
    var check = ($$('.tabs_smiles').getChildren('a')[0].length / 4);
    if (elem[0].getStyle('right') == 'auto') {
      var right = 0;
    } else {
      var right = elem[0].getStyle('right').toInt();
    }
    var count_all = right / 185;
    if ((check + "").indexOf(".") == -1) {
      max = max - 1;
    }
    if (max > count_all) {
      elem.setStyle('right', (right + 185) + 'px')
      $$('.smile_left_scroll').setStyle('opacity','1');
    }
    if((max-1) <= count_all){
      $$('.smile_right_scroll').setStyle('opacity','0.3');
    }
  },
  scrollleftSmiles: function () {
    var elem = $$('.tabs_smiles');
    var right = elem[0].getStyle('right').toInt();
    if (right && right != 0) {
      elem.setStyle('right', (right - 185) + 'px');
      $$('.smile_right_scroll').setStyle('opacity','1');
    }


    if((right - 185)<=0){
      $$('.smile_left_scroll').setStyle('opacity','0.3');
    }
  },
  deleteImage: function (used_id, sticker_id,comment_id){
  var photo_id = 0;
  var container = $('comment_attach_preview_image_wall'+comment_id);
  if(!container) {
    return;
  }
  if(!used_id || !sticker_id){
    return;
  }
  if (window.load_image_deletes_comment == 1) {
    return;
  }
  var loading = $('comment_attach_loading_wall'+comment_id);
  loading.setStyle('display','block');
  container.setStyle('display', 'none');
  window.load_image_deletes_comment = 1;


  var req = new Request({
    method: 'get',
    url: en4.core.baseUrl +'heemoticon/index/deletefromcomment',
    data: {
      'used_id': used_id,
      'sticker_id': sticker_id
    },
    onComplete: function (response) {
      container.set('html','');
      loading.setStyle('display','none');
      window.load_image_deletes_comment = 0;
    }
  }).send();
},
  deleteImageComposer: function (used_id, sticker_id){
    var photo_id = 0;
    var container = $$('.wall-compose-smile-o-body')[0];
    if(!container) {
      return;
    }
    if(!used_id || !sticker_id){
      return;
    }
    if (window.load_image_deletes_comment == 1) {
      return;
    }
    var loading = en4.core.language.translate('Loading...');
    container.set('html', loading);
    window.load_image_deletes_comment = 1;


    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl +'heemoticon/index/deletefromcomment',
      data: {
        'used_id': used_id,
        'sticker_id': sticker_id
      },
      onComplete: function (response) {
        container.set('html','');
        container.setStyle('display','none');
        window.load_image_deletes_comment = 0;
      }
    }).send();
  }


});

window.heemotion = new Heemoticon();


var Scrollable = new Class({
  Implements: [Options, Events],

  options: {
    autoHide: 1,
    fade: 1,
    className: 'scrollbar',
    proportional: true,
    proportionalMinHeight: 15,
    left: 557,
    top: 200
  },

  initialize: function(element, options) {
    this.setOptions(options);

    if (typeOf(element) == 'elements') {
      var collection = [];
      element.each(function(element) {
        collection.push(new Scrollable(element, options));
      });
      return collection;
    }
    else {
      var scrollable = this;

      this.element = document.id(element);
      if (!this.element) return 0;

      this.active = false;

      // Renders a scrollbar over the given element
      if(this.options.idName){
        this.container = new Element('div', {
          'class': this.options.className,
          'class': this.options.idName,
          'style': 'opacity:0;',
          html: '<div class="knob"></div>'
        }).grab(element, 'after');
      }else {
        this.container = new Element('div', {
          'class': this.options.className,
          html: '<div class="knob"></div>'
        }).grab(element, 'after');
      }
      element.grab(this.container, 'after');
      this.slider = new Slider(this.container, this.container.getElement('div'), {
        mode: 'vertical',
        onChange: function(step) {
          this.element.scrollTop = ((this.element.scrollHeight - this.element.offsetHeight) * (step / 100));
        }.bind(this)
      });
      this.knob = this.container.getElement('div');
      this.reposition();
      if (!this.options.autoHide) this.container.setStyle('opacity','0.7');

      if (this.scrollHeight > this.offsetHeight) {
        scrollable.showContainer();
      }else{
        scrollable.hideContainer();
      }
      this.element.addEvents({
        'mouseenter': function() {
          if (this.scrollHeight > this.offsetHeight) {
            scrollable.showContainer();
          }
          scrollable.reposition();
        },
        'mouseleave': function(e) {
          if (!scrollable.isInside(e) && !scrollable.active) {
            scrollable.hideContainer();
          }
        },
        // Making the element scrollable via mousewheel
        'mousewheel': function(event) {
          event.preventDefault();    // Stops the entire page from scrolling when mouse is located over the element
          if ((event.wheel < 0 && this.scrollTop < (this.scrollHeight - this.offsetHeight)) || (event.wheel > 0 && this.scrollTop > 0)) {
            this.scrollTop = this.scrollTop - (event.wheel * 30);
            scrollable.reposition();
          }
        },
        'Scrollable:contentHeightChange': function() {
          //this scrollable:contentHeightChange could be fired on the current element in order
          //to get a custom action invoked (implemented in onContentHeightChange option)
          scrollable.fireEvent('contentHeightChange');
        }
      });

      this.container.addEvent('mouseleave', function() {
        if (!scrollable.active) {
          /*scrollable.hideContainer();*/
        }
      });
      this.knob.addEvent('mousedown', function(e) {
        scrollable.active = true;
        window.addEvent('mouseup', function(e) {
          scrollable.active = false;
          if (!scrollable.isInside(e)) {
            scrollable.hideContainer();
          }
          this.removeEvents('mouseup');
        });
      });
      window.addEvents({
        'resize': function() {
          scrollable.reposition.delay(50,scrollable);
        },
        'mousewheel': function() {
          if (scrollable.element.isVisible()) scrollable.reposition();
        }
      });

      // Initial hiding of the scrollbar
      scrollable.container.setStyle('opacity','0');

      return this;
    }
  },
  reposition: function() {
    var self = this;
    // Repositions the scrollbar by rereading the container element's dimensions/position
    (function() {
      this.size = this.element.getComputedSize();
      this.position = this.element.getPosition();
      var containerSize = this.container.getSize();

      this.container.setStyle('height', this.size['height']).setPosition({
        x: self.options.left,
        y: self.options.top
      });
      this.slider.autosize();
    }).bind(this).delay(50);

    if (this.options.proportional === true) {
      if (isNaN(this.options.proportionalMinHeight) || this.options.proportionalMinHeight <= 0) {
        throw new Error('Scrollable: option "proportionalMinHeight" is not a positive number.');
      } else {
        var minHeight = Math.abs(this.options.proportionalMinHeight);
        var knobHeight = this.element.offsetHeight * (this.element.offsetHeight / this.element.scrollHeight);
        this.knob.setStyle('height', Math.max(knobHeight, minHeight));
      }
    }

    this.slider.set(Math.round((this.element.scrollTop / (this.element.scrollHeight - this.element.offsetHeight)) * 100));
  },

  /**
   * Scrolls the scrollable area to the bottommost position
   */
  scrollBottom: function() {
    this.element.scrollTop = this.element.scrollHeight;
    this.reposition();
  },

  /**
   * Scrolls the scrollable area to the topmost position
   */
  scrollTop: function() {
    this.element.scrollTop = 0;
    this.reposition();
  },

  isInside: function(e) {
    if (e.client.x > this.position.x && e.client.x < (this.position.x + this.size.totalWidth) && e.client.y > this.position.y && e.client.y < (this.position.y + this.size.totalHeight))
      return true;
    else return false;
  },
  showContainer: function(force) {
    if ((this.options.autoHide && this.options.fade && !this.active) || (force && this.options.fade)) this.container.setStyle('opacity','0.7');
    else if ((this.options.autoHide && !this.options.fade && !this.active) || (force && !this.options.fade)) this.container.setStyle('opacity','0.7');
  },
  hideContainer: function(force) {
    if ((this.options.autoHide && this.options.fade && !this.active) || (force && this.options.fade)) this.container.setStyle('opacity','0');
    else if ((this.options.autoHide && !this.options.fade && !this.active) || (force && !this.options.fade)) this.container.setStyle('opacity','0');
  },
  terminate: function() {
    this.container.destroy();
  }
});






