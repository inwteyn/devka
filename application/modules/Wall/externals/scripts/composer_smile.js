/* $Id: composer_smile.js 18.06.12 10:52 michael $ */




Wall.Composer.Plugin.Smile = new Class({

  Extends : Wall.Composer.Plugin.Interface,

  name : 'smile',

  options : {
    smiles: {}
  },
  container: null,
  type_emoticon: 'heemoticon_post',
  emoticon_id: 0,
  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },

  activate : function()
  {
    if (this.container){
      this.container.destroy();
      this.container = null;
      return ;
    }



    var create_function = function () {

      var self = this;
      var link = this.getComposer().container.getElement('.wall-compose-smile-o-activator');
      if (this.options.emoticon == 1) {

          var Heemoticons =  new Heemoticon();
        Heemoticons.smiles = this.options.smiles;
        Heemoticons.second = 0;
        this.makeBody();
        this.getComposerSmiles(link,Heemoticons);

      }else{
        var container = new Element('div', {'class': 'wall-smile-container', 'html': '<div class="wall_data"></div>'});
        this.container = container = Wall.injectAbsolute(link, container, true);

        var arrow = new Element('div', {'class': 'wall_arrow_container', 'html': '<div class="wall_arrow"></div>'});
        arrow.inject(container, 'top');

        var ul = new Element('ul');

        for (var i = 0; i < this.options.smiles.length; i++) {
          var item = this.options.smiles[i];
          var a = new Element('a', {
            'title': item.title,
            'href': 'javascript:void(0)',
            'html': item.html,
            'rev': item.index_tag
          });
          var li = new Element('li', {});
          a.inject(li);
          li.inject(ul);

          a.addEvent('click', function () {
            self.getComposer().editor.setContent(self.getComposer().editor.getContent() + '&nbsp;' + $(this).get('rev') + '&nbsp;');
            self.getComposer().editor.moveCaretToEnd();

          });
        }

        ul.inject(container.getElement('.wall_data'));

      }

    }.bind(this);

    if (this.getComposer().is_opened){
      create_function();
    } else {
      this.getComposer().open(create_function);
    }



  },
  getComposerSmiles: function (links,Heemoticons){
    var composer =  this.getComposer().editor;
    var tray  =  this.getComposer().getTray();
    var body = this.elements.body;
    Heemoticons.composer_open = 1;
    var self = this;

    if(!Heemoticons.second && links ){
      Heemoticons.hideSmile();
      if ($('wall_comment_smile')) {
        var elem = document.getElementById("wall_comment_smile");
        elem.parentNode.removeChild(elem);
      }
      $$('body')[0].addEvent('click', function (e) {
        if (!e.target.getParent('#wall_comment_smile') && !e.target.hasClass('emoticon-icons') && !e.target.hasClass('wall-compose-smile-o-activator')  && !e.target.hasClass('wall-compose-smile-o-activator') && !e.target.getParent('#smile_composer_comment-element') && e.target.get('id') != 'addEmoticonContanerBackground' && !e.target.getParent('#addEmoticonContaner') && !e.target.getParent('.header-smiles-contaner') && !e.target.getParent('.tabs_smiles')) {
          Heemoticons.hideSmile();
        }
      });

      var link = links;
      var form = link.getParent('form');

      var contaner = new Element('div');

      var container = Heemoticons.injectAbsoluteCommentSmile(link, contaner, 0);
      console.log(container);
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

          Heemoticons.hideSmile();
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

          self.makeLoading();
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
              contaner_img.set('html','');
              elem.inject(contaner_img);
              contaner_img.setStyle('display', 'block');
              var used_id = 0;
              var row = response.trim().split('?').pop();
              if(row){used_id=row.split('=').pop();}
              var delete_button = new Element('div', {
                'id': 'delete_' + 0,
                'class': 'wpClose hei hei-times delete_photo_in_comment_button',
                'style':'color:#fff'
              }).inject(contaner_img);

              this.emoticon_id =  sticker_id;
              this.type_emoticon = 'heemoticon_post';
              this.name = 'heemoticon_post';
              var data  = {
                'emoticon_id':sticker_id,
                'type':'heemoticon_post'
              }
              $H(data).each(function (value, key) {
                self.setFormInputValue(key, value);
              });
              self.ready();
              delete_button.addEvent('click', function(e){
                Heemoticons.deleteImageComposer(used_id,sticker_id,0);
              });
              Heemoticons.hideSmile();

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
  makeFormInputs: function (){
    this.ready();
    this.parent({
      'file_id': this.emoticon_id,
      'type': 'heemoticon_post'
    });
  },
  deactivate : function (){
    if (this.container){
      this.container.destroy();
      this.container = null;
    }
    this.parent();
  },

  poll : function() {

  }


});






