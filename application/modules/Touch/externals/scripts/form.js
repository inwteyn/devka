/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 30.03.11
 * Time: 16:43
 * To change this template use File | Settings | File Templates.
 */

var TouchformClass = new Class({
	bind: function(block){

		var self = Touchform;

		var elements;

		block = Touch.getBlock(block);

		var touchForms = block.getElements("form.touchform");
		var uploadForms = block.getElements("form.touchupload");

    touchForms.each(function(bindingForm){

			if ($type(bindingForm) == 'element')
			{
				var formatter = new Element('input', {'type':'hidden', 'name':'format', 'value':'html'});
				formatter.inject(bindingForm);

				if (Smoothbox.box.retrieve('opened', false)){
					var sm = new Element('input', {'type':'hidden', 'name':'smoothbox', 'value':true});
					sm.inject(bindingForm);
				}
        self.initCaptcha(bindingForm);
				var loader = new Element('div', {'class':'hidden global-form-posting'});

				var logger = bindingForm;
				var div = new Element('div');
				div.inject(logger.getParent());
				logger.inject(div);
				loader.inject(div);
				
				bindingForm.addEvent( 'submit', function(e){
					new Event(e).stop();

					var blurs = bindingForm.getElements('.filter_default_value');

					blurs.each(function(el){
						el.removeClass('filter_default_value');
						el.set('value', '');
					});

					new Request.HTML({
						url: bindingForm.get('action'),
						evalScripts : true,

						onRequest:function(){
							loader.setStyle('height', logger.getStyle('height'));
							logger.addClass('hidden');
							loader.removeClass('hidden');
						},

						onFailure:function(){
							alert('An error has occurred!!!');
						},

						onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
							if (responseHTML.trim().length > 0 )
							{
								var el = bindingForm.getParent();
								el.set('html', responseHTML);
								Touch.bind(el);
							}
						},

						onComplete:function(responseTree, responseElements, responseHTML, responseJavaScript){
							loader.addClass('hidden');
							logger.removeClass('hidden');
						}
					}).post(bindingForm);
				});
			}
		});

		uploadForms.each(function(bindingForm){

			var a = bindingForm.getProperty('action').replace('format=html', '');
      if(Touch.isIPhone())
			  bindingForm.setProperty('action', location.href.replace(location.hash, ''));
      else {
        bindingForm.setProperty('action', location.href.replace(location.pathname, location.hash.replace('#', '')));
      }
			var input = new Element('input', {'type':'hidden', 'name':'touch-upload-action', 'value':a});

			input.inject(bindingForm);
			bindingForm.setProperty('action', location.href.replace(location.hash, ''));

			if (bindingForm.hasClass('touch-multi-upload')){
				bindingForm.addEvent('submit', function(e){
					var elements = bindingForm.getElements('input[type=file]');

					elements.each(function(el){
						if (el.get('value').trim().length == 0){
							el.set('disabled', true);
						};
					});
				});
			}

      self.initCaptcha(bindingForm);

			//Convert input[type=file] to iPhone/iPad uploader
			if (!Touch.isIPhone()){
				return false;
			}
      if(bindingForm.hasClass('touch_profile_photo_upload')){
        a = a.replace(location.hash.substr(1), en4.core.baseUrl + 'touch/uploadprofilephoto');
      }
			var params = {
				callbackURL:escape(location.href)+ '/status/',
				debug: false,
				postUrl: escape(bindingForm.get('action')),
				returnStatus:true,
				postValues: escape('format=json&touch-upload-action=' + a +'&owner_id=' + en4.user.viewer.id+'&chash=' + Touch.getHash()),
				postImageParam:'picup-image-upload',
				purpose: en4.core.language.translate("TOUCH_Select a sample image to add to %1$s", en4.core.siteTitle.substr(0, 10)),
				referrerName: escape(en4.core.siteTitle.substr(0, 10)),
				returnServerResponse: true
			}

      if (Touch.referrerFavicon){
        params.referrerFavicon = Touch.referrerFavicon;
      }

			var files = bindingForm.getElements('input[type=file]');
      
			files.each(function(file){
        if (!file.hasClass('iphone-ignore')){
				  Picup.convertFileInput(file, params);
        }
			});
		});

	},

	show_errors:function($errors){
		for(var field in $errors){
			for(var error in $errors[field]){
				var msg = field+ ' - '+error+'<br/>'+$errors[field][error];
				Touch.message(msg, 'error');
			}
		}
	},
  initCaptcha: function(form){
    var input = false;
    var refreshButton = false;
    var form_php_class_name = null;
    if($type(form) == 'element'){
      if(form.getElement('input.form_php_class_name'))
        form_php_class_name = form.getElement('input.form_php_class_name').get('value');
      var captcha = form.getElement('#captcha-wrapper');
      if(captcha){
        input = captcha.getElement('#captcha-input');
        refreshButton = new Element('span', {'class': 'button captcha-refresh', 'text': '   '});
        refreshButton.inject(input, 'after');
        refreshButton.addEvent('click', function(e){

          var jsonRequest = new Request.JSON({
            url: $$('head base[href]')[0].get('href') + 'touch/utility/refresh-captcha',
            data: {
              format: 'json',
              class_name: form_php_class_name
            },
            onSuccess: function(captcha) {
              if(captcha && captcha.id && captcha.src){
                $('captcha-id').set('value', captcha.id);
                $$('#captcha-element img').set('src', captcha.src);
              } else {
                alert('Whoops! Something went wrong!');
              }
            }
          }).send();
        });
        input.setStyle('width', ($('captcha-element').getWidth()-71)+'px');
      }
    }
  }
});