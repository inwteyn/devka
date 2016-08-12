var Picup = {
    
    customURLScheme : 'fileupload://',     
    windowname : en4.core.language.translate('TOUCH_File Upload'),
    activeFileInput : null,
    currentHash : location.hash,
    hashObserverId : null,
    postValues: '',

		responseCallback: null,
	// Override this as a function to handle hash changes
    callbackHandler : function(params){
			try{
				eval('var response = ' + unescape(params.serverResponse) + ';');
			} catch(e){
				alert(e);
			}

			if ($type(response) == 'object'){
        Touch.picup_up = true;
				Touch.hash = response.chash;
				location.hash = response.chash;
			} else {
				location.hash = "";
				var response = {};
			}

			if ($type(Picup.responseCallback) == 'function'){
				Picup.responseCallback(response);
        Touch.picup_up = false;
			}
		},

    convertFileInput : function(inputOrInputId, options){
			var Picup = this;

			if (!Touch.isIPhone){
				return false;
			}

      Picup.postValues = options.postValues;

			var input = inputOrInputId;
			if(typeof(inputOrInputId) == 'string'){
				 input = document.getElementById(inputOrInputId);
			}
			input.type = 'button';
			input.name = 'iPhone-file-button';
			input.id = 'iPhone-file-button';
			input.value = en4.core.language.translate("TOUCH_Choose Photo...");
			input.picupOptions = options;
			input.onclick = function(){
        if(Touch.isMaintenanceMode()){
          alert(en4.core.language.translate("TOUCH_We're down for maintenance. You cannot upload photo with Picup Application."))
        }
				Picup.activeFileInput = this;
				var postUrl = Picup.urlForOptions('new', this.picupOptions);
				window.open(Picup.urlForOptions('new', this.picupOptions), Picup.windowname);

    		if(Picup.callbackHandler){
	    		Picup.currentHash = window.location.hash;
	    		Picup.hashObserverId = setInterval('Picup.checkHash()', 500);
    		}
			};
			input.disabled = false;

			var urlStr = "<a href='http://itunes.apple.com/us/app/picup/id354101378?mt=8'>" + en4.core.language.translate('TOUCH_Available in the AppStore') +"</a>";
			var picupDesc = new Element('div', {
				'class':'app-install-description',
				'html':en4.core.language.translate("TOUCH_Picup application is required! If you can't upload photo, please follow the following url to install Picup application. %s", urlStr)
			});

			input.getParent().adopt(input, picupDesc);
    	return false;
    },
    
    checkHash : function(){
			var Picup = this;

    	if(window.location.hash != Picup.currentHash){
    		// The hash has changed
    		clearInterval(Picup.hashObserverId);
    		Picup.currentHash = window.location.hash;
    		
	    	var hash = window.location.hash.replace(/^\#/, '');
    		var paramKVs = hash.split('&');
				var paramHash = {};
				var paramOutput = [];
				for(var p=0;p<paramKVs.length;p++){
					var kvp = paramKVs[p];
					// we only want to split on the first =, since data:URLs can have = in them
					var kv = kvp.replace('=', '&').split('&');
					paramHash[kv[0]] = kv[1];
				}
				Picup.callbackHandler(paramHash);
    	}
    },
    
    urlForOptions : function(action, options){
			var Picup = this;

	    var url = Picup.customURLScheme+action+'?';
    	var params = [];
    	for(var param in options){
    		params.push(param+'='+options[param]);
    	}
    	var uploadURL = url + params.join('&');
    	return uploadURL;
    }
};