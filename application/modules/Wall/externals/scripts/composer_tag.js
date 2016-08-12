/* $Id: composer_tag.js 18.06.12 10:52 michael $ */



Wall.Composer.Plugin.Tag = new Class({

  Extends : Wall.Composer.Plugin.Interface,

  name : 'tag',

  options : {

    'suggestParam' : [

    ],
    'suggestUsers' : [],

    'enabled' : true,
    'suggestProto' : 'request.json',
    requestOptions : {},
    'suggestOptions' : {
      'minLength': 0,
      'maxChoices' : 100,
      'delay' : 250,
      'selectMode': 'pick',
      'multiple': false,
      'filterSubset' : true,
      'tokenFormat' : 'object',
      'tokenValueKey' : 'label',
      'injectChoice': $empty,
      'onPush' : $empty,

      'prefetchOnInit' : true,
      'alwaysOpen' : false,
      'ignoreKeys' : false

    }
  },

  linkAttached: false,

  initialize : function(options) {
    this.params = new Hash(this.params);
    this.parent(options);
  },

  suggest : false,

  attach : function() {
    if( !this.options.enabled ) return;
    this.parent();

    var self = this;


    var handle = function (e)
    {
      var stopped = false;
      if (self.suggest && e){
        self.suggest.onCommand(e);
        if (self.suggest.break_event){
          e.stop();
          stopped = true;
        }

      }
      if (!stopped){
        self.monitorKey.bind(self)(e);
      }
    };
    this.activateComment();

    // Key Events
    this.getComposer().addEvent( (Browser.Engine.trident || Browser.Engine.webkit) ? 'editorKeyDown' : 'editorKeyPress', handle);

    // Paste
    this.getComposer().addEvent('editorPaste', function (){
      (function (){
        self.checkLink(self.getComposer().getContent());
      }).delay(100);
    });

    // Submit
    this.getComposer().addEvent('editorSubmit', function (){
      self.linkAttached = false;
      self.getComposer().makeFormInputs({tags: self.getTagsFromComposer().toQueryString()});
    });

    return this;
  },
  second_activete : function(){
    var self = this;
    this.activateComment();
    setTimeout(function(){
      self.activateComment();
    },1000);
  },
  getTagsFromComposer: function ()
  {
    var tags = new Hash();

    this.getComposer().elements.body.getElements('.wall_tag').each(function (item){
      tags[item.get('rev')] = item.get('text');
    });

    return tags;

  },

  checkTagsFromComposer: function ()
  {
    this.getComposer().elements.body.getElements('.wall_tag').each(function (item){
      if (item.get('text') != item.get('rel')){
        item.removeClass('wall_tag');
      }
    });
  },

  detach : function() {
    if( !this.options.enabled ) return;
    this.parent();
    this.endSuggest();
    return this;
  },
  activateComment:function() {
    var self = this;
    $$('.wall-comment-form').each(function (form) {
      var textarea = form.getElement('#body');
      if (textarea) {
        textarea.addEvent('keyup', function () {


          var text = this.value;
          var pos = self.doGetCaretPosition(this);

          var s = self.getWordTest(text, pos)
          var re = /(?:^|\W)@(\w+)(?!\w)/g, match, matches = [];
          while (match = re.exec(s)) {
            matches.push(match[1]);
          }
          if (matches.length) {
            self.getSuggestComment(matches,this);
          }
        })
      }
    });
      self.activateReplyforComment();
  },
  activateReplyforComment:function() {
    var self = this;
    $$('.wall-comment-form-reply').each(function (form) {
      var textarea = form.getElement('#body');
      if (textarea) {
        textarea.addEvent('keyup', function () {


          var text = this.value;
          var pos = self.doGetCaretPosition(this);

          var s = self.getWordTest(text, pos)
          var re = /(?:^|\W)@(\w+)(?!\w)/g, match, matches = [];
          while (match = re.exec(s)) {
            matches.push(match[1]);
          }
          if (matches.length) {
            self.getSuggestComment(matches,this);
          }
        })
      }
    })
  },
   doGetCaretPosition: function(ctrl) {
  var CaretPos = 0;	// IE Support
  if (document.selection) {
    ctrl.focus ();
    var Sel = document.selection.createRange ();
    Sel.moveStart ('character', -ctrl.value.length);
    CaretPos = Sel.text.length;
  }
  // Firefox support
  else if (ctrl.selectionStart || ctrl.selectionStart == '0')
    CaretPos = ctrl.selectionStart;
  return (CaretPos);
},
 getWordTest:function(str, pos) {

  // Perform type conversions.
  str = String(str);
  pos = Number(pos) >>> 0;

  // Search for the word's beginning and end.
  var left = str.slice(0, pos + 1).search(/\S+$/),
    right = str.slice(pos).search(/\S+\s*/);

  // The last word in the string is a special case.
  if (right < 0) {
    return str.slice(left);
  }

  // Return the word, using the located bounds to extract it from the string.
  return str.slice(left, right + pos);

},
  getWordTestall:function(str, pos) {

    // Perform type conversions.
    str = String(str);
    pos = Number(pos) >>> 0;

    // Search for the word's beginning and end.
    var left = str.slice(0, pos + 1).search(/\S+$/),
      right = str.slice(pos).search(/\s/);

    // The last word in the string is a special case.
      if (right < 0) {
      return str.slice(left);
    }

    // Return the word, using the located bounds to extract it from the string.
    return str.slice(left, right + pos);

  },
  activate: $empty,

  deactivate : $empty,

  poll : function() {

  },

  keyTimeOut: null,

  monitorKey: function(e) {

    if (e && (e.key == 'enter' || e.key == 'up' || e.key == 'down' || e.key == 'esc')){
      return ;
    }

    var monitor = function (){

      var info = this.getComposer().editor.getCaretAndText();
      var caret = info.caret;
      var value = info.text;

      this.checkTagsFromComposer();

      if (e && e.key == 'space'){
        this.checkLink();
      }

      this.endSuggest();
      var segment = this.detectTag(caret, value);

      if (segment && this.getTagsFromComposer().getLength() <= 10){
        this.doSuggest(segment);
      }

    }.bind(this);

    window.clearTimeout(this.keyTimeOut);
    this.keyTimeOut = window.setTimeout(monitor, 100);

  },

  doSuggest : function(text) {
    this.currentText = text;
    var suggest = this.getSuggest();
    var input = this.getHiddenInput();
    input.set('value', text);
    input.value = text;
  },

  endSuggest : function() {
    this.currentText = '';
    this.positions = {};
    if( this.suggest ) {
      this.getSuggest().destroy();
      delete this.suggest;
    }
  },

  getHiddenInput : function() {
    if( !this.hiddenInput ) {
      this.hiddenInput = new Element('input', {
        'type' : 'text',
        'styles' : {
          'display' : 'none'
        }
      }).inject(this.getComposer().container.getElement('.wallTextareaContainer'));
    }
    return this.hiddenInput;
  },

  getSuggest : function() {

    if( !this.suggest ) {

        this.choices = new Element('ul', {
        'class': 'wall-autosuggestion',
        'styles': {
          'position': 'absolute',
          'width': this.getComposer().container.getElement('.wallComposerContainer').getCoordinates().width - 2 // 2px borders
        }
      }).inject(this.getComposer().container.getElement('.wallComposerContainer'), 'bottom');

      var self = this;
      var options = $merge(this.options.suggestOptions, {
        'customChoices' : this.choices,
        'injectChoice' : function(token) {
          if (self.getTagsFromComposer().has(token.guid)){
            return ;
          }
          if (!token.guid){
            return ;
          }
          var choice = new Element('li', {
            'class': 'autocompleter-choices',
            //'value': token.id,
            'html': token.photo || '',
            'id': token.guid
          });
          new Element('div', {
            'html' : this.markQueryValue(token.label),
            'class' : 'autocompleter-choice'
          }).inject(choice);
          new Element('input', {
            'type' : 'hidden',
            'value' : JSON.encode(token)
          }).inject(choice);
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        },
        'onChoiceSelect' : function(choice) {

          var data = JSON.decode(choice.getElement('input').value);
          var body = self.getComposer().elements.body;

          var replaceString = '@' + self.currentText;
          var newString = '<span rev="'+data.guid+'" rel="'+data.label+'" class="wall_tag">'+data.label+'</span>&nbsp;';
          var content = body.get('html');

          content = content.replace(/\<span\>\<\/span\>/ig, ''); // IE
          content = content.replace(new RegExp(replaceString, 'i'), newString);
          body.set('html', content);

          var lastElement = null;
          body.getElements('.wall_tag').each(function (item){
            if (item.get('text') == data.label.replace(/&#039;/ig, '\'')){
              lastElement = item;
            }
          });

          self.getComposer().editor.setCaretAfterElement(lastElement);

        },
        'emptyChoices' : function() {
          this.fireEvent('onHide', [this.element, this.choices]);
        },
        'onCommand' : function(e) {
          switch (e.key) {
            case 'enter':
              break;
          }
        }
      });

      if( this.options.suggestProto == 'request.json' ) {
        this.suggest = new Wall.Autocompleter.Request.JSON(this.getHiddenInput(), en4.core.baseUrl + 'wall/index/suggest?includeSelf=true ', options);
      }
      if( this.options.suggestProto == 'local' ) {
        this.suggest = new Wall.Autocompleter.Local(this.getHiddenInput(), this.options.suggestParam, options);
      }
    }

    return this.suggest;

  },

  getSuggestComment : function(text,elem) {
    var self = this;

    $$('body')[0].addEvent('click', function (e) {
      if (!e.target.getParent('#wall-autosuggestion-contaner-for-usercomment')) {
        self.hideComposerTags();
      }
    });
    if( this.options.suggestProto == 'request.json' && !this.options.suggestUsers.length) {
      var users = [];
      var req = new Request({
        method: 'get',
        url: en4.core.baseUrl +'wall/index/suggestuser?includeSelf=true',
        onComplete: function (response) {
          var jsonParse =  JSON.parse(response);
          jsonParse.each(function(a){
            if (a.username.indexOf(text) > -1 || a.label.indexOf(text) > -1) {
              users.push(a);
            }
          });
          self.options.suggestUsers = users;
            if($('wall-autosuggestion-contaner-for-usercomment')){
              var contaner = $('wall-autosuggestion-contaner-for-usercomment');
              contaner.set('html','');
            }else {
              var contaner = new Element('ul', {
                'class': 'wall-autosuggestion-contaner-comment',
                'id': 'wall-autosuggestion-contaner-for-usercomment',
                'styles': {
                  'position': 'absolute',
                  'width': (elem.getSize().x-2)+'px',
                  'border': '1px solid #666'
                }
              });
              self.injectAbsoluteCommentSuggest(elem,contaner);

            }

            for(var t in users) {
              if (users[t].type == 'user') {
                var choice = new Element('li', {
                  'class': 'autocompleter-choices',
                  'html': users[t].photo || '',
                  'id': users[t].guid,
                  'rev': users[t].username
                }).inject(contaner);
                choice.addEvent('click',function(){
                  elem.focus();
                  var pos = self.doGetCaretPosition(elem);
                  var s = self.getWordTestall(text, pos);


                  elem.value = elem.value.replaceAt(elem.value.slice(0,pos).search(/\S+$/), ' @'+this.get('rev')+' ');
                  self.hideComposerTags();
                });
                new Element('div', {
                  'html': '<a class="autocompleter-queried" href="javascript:void(0)">' + users[t].label + '</a>',
                  'class': 'autocompleter-choice'
                }).inject(choice);
              }
            }



        }
      }).send();

    }
    if(this.options.suggestProto == 'local' || this.options.suggestUsers.length) {
      var users = this.findUser(text);

      if ($('wall-autosuggestion-contaner-for-usercomment')) {
        var contaner = $('wall-autosuggestion-contaner-for-usercomment');
        contaner.set('html', '');
      } else {
        var contaner = new Element('ul', {
          'class': 'wall-autosuggestion-contaner-comment',
          'id': 'wall-autosuggestion-contaner-for-usercomment',
          'styles': {
            'position': 'absolute',
            'width': (elem.getSize().x - 2) + 'px',
            'border': '1px solid rgba(0, 0, 0, 0.3)'
          }
        });
        this.injectAbsoluteCommentSuggest(elem, contaner);

      }

      for (var t in users) {
        if (users[t].type == 'user') {
          var choice = new Element('li', {
            'class': 'autocompleter-choices heemoticon',
            'html': users[t].photo || '',
            'id': users[t].guid,
            'rev': users[t].username
          }).inject(contaner);
          choice.addEvent('click', function () {
            elem.focus();
            var pos = self.doGetCaretPosition(elem);
            var s = self.getWordTestall(text, pos);


            elem.value = elem.value.replaceAt(elem.value.slice(0, pos).search(/\S+$/), ' @' + this.get('rev') + ' ');
            self.hideComposerTags();
          });
          new Element('div', {
            'html': '<a class="autocompleter-queried" href="javascript:void(0)">' + users[t].label + '</a>',
            'class': 'autocompleter-choice'
          }).inject(choice);
        }
      }


    }

  },
  hideComposerTags: function(){
    if ($('wall-autosuggestion-contaner-for-usercomment')) {
      var elem = document.getElementById("wall-autosuggestion-contaner-for-usercomment");
      elem.parentNode.removeChild(elem);

    }
  },
  injectAbsoluteCommentSuggest: function (element, container,plus) {
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
        .setStyle('top', pos.top + pos.height)
        .setStyle('min-height', '50px')
        .setStyle('z-index', '999')
        .setStyle('right', ($$('body')[0].getCoordinates().width - pos.left - pos.width));


    };

    container.inject(Wall.externalDiv(), 'bottom');
    build();

    return container;

  },
    findUser: function (text) {

        var search = text.toString().toLowerCase();

        var users = this.options.suggestUsers;
        var rArr = [];

        for (var t in users) {
            if (t.toInt() > 0) {
                if (users[t].username && (users[t].username.toLowerCase().indexOf(search) > -1 || users[t].label.toLowerCase().indexOf(search) > -1)) {
                    rArr.push(users[t]);
                }
            }
        }
        return (rArr);
    },
  checkLink: function (text)
  {
    if (this.linkAttached){
      return ;
    }

    var caret = 0;
    var value = '';

    if (text){

      caret = text.length;
      value = text;

    } else {
      var info = this.getComposer().editor.getCaretAndText();
      caret = info.caret;
      value = info.text;
    }


    value = value.replace('&amp;', '&');
    var link_matches = this.detectLink(caret, value);

    if (link_matches){

      var video = this.getComposer().getPlugin('video');
      var link = this.getComposer().getPlugin('link');
      var avp = this.getComposer().getPlugin('avp');

      if ((link_matches[0].match(/(http|https)\:\/\/(www\.|)youtube\.com\/watch/ig) || link_matches[0].match(/(http|https)\:\/\/(www\.|)youtu\.be/ig)) && video && video.options.autoDetect){

        try {
          this.linkAttached = true;

          video.activate(true);
          this.getComposer().container.getElement('#wall-compose-youtube-video').click();
          video.updateVideoFields.bind(video)();
          this.getComposer().container.getElement('.wall-compose-video-form-input, #compose-video-form-input').value = link_matches[0];
          video.doAttach();

          this.getComposer().editor.moveCaretToEnd();

        } catch (e){

        }


      } else if (link_matches[0].match(/(http|https)\:\/\/(www\.|)vimeo\.com\/[0-9]{1,}/ig) && video && video.options.autoDetect){

        try {

          this.linkAttached = true;

          video.activate(true);
          this.getComposer().container.getElement('#wall-compose-vimeo-video').click();
          video.updateVideoFields.bind(video)();
          this.getComposer().container.getElement('.wall-compose-video-form-input, #compose-video-form-input').value = link_matches[0];
          video.doAttach();

          this.getComposer().editor.moveCaretToEnd();

        } catch (e){

        }


      } else if ((link_matches[0].match(/(http|https)\:\/\/(www\.|)youtube\.com\/watch/ig) || link_matches[0].match(/(http|https)\:\/\/(www\.|)youtu\.be/ig)) && avp){

        try {

          this.linkAttached = true;

          var a = new Element('a', {'href': en4.core.baseUrl + 'vids/feed-import/?format=smoothbox'});
          Smoothbox.open(a);

          (function (){

            for (var i=0; i < window.frames.length; i++){
              var item = window.frames[i];
              if (item && item.location && item.location.href.indexOf('vids/feed-import') != -1){
                item.onload = function (){
                  item.$$('input[name=url]').set('value', link_matches[0].replace('&amp;', '&'));
                };
              }
            }
          }).delay(2000);



          this.getComposer().editor.moveCaretToEnd();

        } catch (e){
        }

      } else if (link_matches[0].match(/(http|https)\:\/\/(www\.|)vimeo\.com\/[0-9]{1,}/ig) && avp){

        try {

          this.linkAttached = true;

          var a = new Element('a', {'href': en4.core.baseUrl + 'vids/feed-import/?format=smoothbox'});
          Smoothbox.open(a);

          for (var i=0; i < window.frames.length; i++){
            var item = window.frames[i];
            if (item && item.location && item.location.href.indexOf('vids/feed-import') != -1){
              item.onload = function (){
                item.$$('input[name=url]').set('value', link_matches[0]);
              };
            }
          }


          this.getComposer().editor.moveCaretToEnd();

        } catch (e){
        }

      }





      else if (link && link.options.autoDetect) {

        try {

          this.linkAttached = true;

          link.activate(true);
          this.getComposer().container.getElement('.wall-compose-link-form-input').value = link_matches[0];
          link.doAttach();

          this.getComposer().editor.moveCaretToEnd();

      } catch (e){

      }


    }














    }



  },


  detectTag: function (caret, value)
  {
    if (!caret || !value){
      return ;
    }

    var pre_value = value.substr(0, caret);

    if (!pre_value){
      return ;
    }

    var last_index = pre_value.lastIndexOf('@');
    if (last_index == -1 || caret <= last_index || caret >= last_index+10){
      return ;
    }
    var segment = pre_value.substr(last_index+1); // after @
    if (!segment || segment.lastIndexOf(' ') != -1){
      return ;
    }

    return segment;

  },

  detectLink: function (caret, value)
  {
    if (!caret || !value){
      return ;
    }

    var pre_value = value.substr(0, caret);
    if (!pre_value){
      return ;
    }
    var last_index = pre_value.substr(0,pre_value.length-1).lastIndexOf(' ');
    if (last_index == -1){
      last_index = 0;
    } else {
      last_index++;
    }
    var segment = value.substr(last_index, (caret-last_index));

    var matches = segment.match(/(https?\:\/\/|www\.)+([a-zA-Z0-9._-]+\.[a-zA-Z.]{2,5})?[^\s]*/i);
    if (!matches){
      return ;
    }
    if (matches.length != 3){
      return ;
    }
    if (!matches[0] || !matches[1] || !matches[2]){
      return ;
    }
    return matches;
  }


});

String.prototype.replaceAt=function(index, character) {
  return this.substr(0, index) + character + this.substr(index+character.length);
}
