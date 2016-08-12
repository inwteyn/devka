
/* $Id: composer_photo.js 7244 2010-09-01 01:49:53Z Kirill $ */


Wall.Composer.Plugin.Photo = new Class({

  Extends : Wall.Composer.Plugin.Interface,

  name : 'photo',

  photo_id : 0,
  photo_src : '',
  src_url : '',

  fresh : true,

  options : {
    title : 'Add Photo',
    lang : {},
    requestOptions : false,
    fancyUploadEnabled : true,
    fancyUploadOptions : {}
  },

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

  activate : function() {
    var self = this;
    if( this.active ) return;
    
    this.parent();

    this.makeMenu();
    this.makeBody();

    if(this.getComposer().is_posted)
      this.photo_id = 0;

    if(this.photo_id == 0){
      // Generate form
      var fullUrl = this.options.requestOptions.url;

      var my_class = 'wall-compose-form wall-compose-photo-form';

      if(Touch.isIPhone()){
        my_class += ' touchupload';
      }

      this.elements.form = new Element('form', {
        'class' : my_class,
        'method' : 'post',
        'id' : 'wall-touch-upload-form',
        'action' :  en4.core.baseUrl + 'wall/photo/photo',
        'enctype' : 'multipart/form-data'
      }).inject(this.elements.body);

      this.elements.url = new Element('input', {
        'type' : 'hidden',
        'name' : 'src_url',
        'id' : 'src_url',
        'value' : "'" + this.src_url + "'"
      }).inject(this.elements.form);

      this.elements.url = new Element('input', {
        'type' : 'hidden',
        'value' : en4.core.baseUrl,
        'name' : 'text',
        'id' : 'photo-comment-text'
      }).inject(this.elements.form);

  //    this.elements.formFancyFile = new Element('a', {
  //        'href': en4.core.baseUrl + 'wall/photo/photo/',
  //        'class' : 'touchajax',
  //        'html' : this._lang('Click here to upload.'),
  //        'events' : {
  //          'click' : function(event){
  //            event.stop();
  //            Touchajax.request(this);
  //          }
  //        }
  //      }).inject(this.elements.form);

      // Добавление file

      this.elements.formInput = new Element('input', {
        'class' : 'wall-compose-form-input wall-compose-photo-form-input',
        'type' : 'file',
        'name' : 'file',
        'id' : 'file'
      }).inject(this.elements.form);
      Picup.responseCallback = function(response){
        $('wall-touch-upload-form').destroy();
        if( null != response){
          if(response.photo_src != ''){

            var wrapper = new Element('div', {
              'id' : 'touch-wall-photo-post-result-wrapper'
            });
            var img = new Element('img', {
              'src' : response.photo_src
            }).inject(wrapper);
            var msg = new Element('div', {
              'html' : 'Photo is uploaded. Now you can share it.'
            }).inject(wrapper);

            wrapper.inject(self.elements.body);
            self.makeFormInputs({
            'photo_id' : response.photo_id,
            'type' : 'type'
            });
            
          }
        }
      };
      Touchform.bind();

      this.elements.message = new Element('div', {
      }).inject(this.elements.form);
      
      if(!Touch.isIPhone()){
        this.elements.formSubmit = new Element('button', {
            'id' : 'wall-compose-photo-form-submit',
            'class' : 'wall-compose-form-submit',
            'type' : 'submit',
            'html' : en4.core.language.translate('Attach'),
            'events' : {
              'click' : function(event) {
                var t = document.getElement('div.textareaBox').getElement('textarea').value;
                if(!t)
                    t = 'null';
                    $('photo-comment-text').value = t;
                
                if($('file').value == '')
                  return false;

              }
            }
        }).inject(this.elements.message);
      }

    }else{

      this.elements.photo_id = new Element('input', {
        'type' : 'hidden',
        'value' : this.photo_id,
        'name' : 'attachment[photo_id]'
      }).inject(this.elements.body);
      this.elements.type = new Element('input', {
        'type' : 'hidden',
        'value' : 'photo',
        'name' : 'attachment[type]'
      }).inject(this.elements.body);

      var result = this.showImage(this.photo_src);
      result.inject(this.elements.body);
      this.makeFormInputs();
    }
  },

  showImage : function (src) {
    var wrapper = new Element('div', {
      'id' : 'touch-wall-photo-post-result-wrapper'
    });
    var img = new Element('img', {
      'src' : src
    }).inject(wrapper);
    var msg = new Element('div', {
      'html' : 'Photo is uploaded. Now you can share it.'
    }).inject(wrapper);
    return wrapper;
  },

  deactivate : function() {

    if( !this.active ) return;

    if(this.getComposer().is_posted){
      this.photo_id = 0;
    }

    if(!this.fresh && this.photo_id != 0){
      this.photo_id = 0;
    }else{
      this.fresh = false;
    }
  
    this.parent();
  },

  doRequest : function() {
    this.elements.iframe = new IFrame({
      'name' : 'composePhotoFrame',
      'src' : 'javascript:false;',
      'styles' : {
        'display' : 'none'
      },
      'events' : {
        'load' : function() {
          this.doProcessResponse(window._composePhotoResponse);
          window._composePhotoResponse = false;
        }.bind(this)
      }
    }).inject(this.elements.body);

    window._composePhotoResponse = false;
    this.elements.form.set('target', 'composePhotoFrame');

    // Submit and then destroy form
    this.elements.form.submit();
    this.elements.form.destroy();

    // Start loading screen
    this.makeLoading();
  },

  doProcessResponse : function(responseJSON) {
    // An error occurred
    if( ($type(responseJSON) != 'hash' && $type(responseJSON) != 'object') || $type(responseJSON.src) != 'string' || $type(parseInt(responseJSON.photo_id)) != 'number' ) {
      //this.elements.body.empty();
      this.makeError(this._lang('Unable to upload photo. Please click cancel and try again'), 'empty');
      return;
      //throw "unable to upload image";
    }

    // Success
    this.params.set('rawParams', responseJSON);
    this.params.set('photo_id', responseJSON.photo_id);
    this.elements.preview = Asset.image(responseJSON.src, {
      'class' : 'wall-compose-preview-image wall-compose-photo-preview-image',
      'onload' : this.doImageLoaded.bind(this)
    });
  },

  doImageLoaded : function() {
    if( this.elements.loading ) this.elements.loading.destroy();
    if( this.elements.formFancyContainer ) this.elements.formFancyContainer.destroy();
    this.elements.preview.erase('width');
    this.elements.preview.erase('height');
    this.elements.preview.inject(this.elements.body);
    this.makeFormInputs();
  },

  makeFormInputs : function(data) {
    this.ready();
    this.parent(data);
  }

})