/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 30.03.11
 * Time: 15:48
 * To change this template use File | Settings | File Templates.
 */
//Ajax loading
var TouchajaxClass = new Class({
	loading: null,
	content: null,
	lcontent: null,
	rcontent: null,
	baseUrl: "/",
	requestRand: 0,
  change_dir: false,
	bind : function (block)
  {
		var self = this;
		var elements;

		if ($type(block) != 'string' && $type(block) != 'element'){

			self.baseUrl = location.protocol + '//' + location.host;

			//Contents;
			self.content = $('global_content');
			self.lcontent = $('global_content');
			self.rcontent = $('global_content');
			self.loading = $('global_content_loading');

		}

		block = Touch.getBlock(block);
    if(!Touch.isBlackBerry() && !Touch.isOperaMini()){
      elements = block.getElements("a.touchajax");
  		elements.each(function(el){
  			if( el.get('tag') != 'a' || el.retrieve('touchajaxed', false))
        {
          return;
        }
        if (!new RegExp(window.location.protocol + "//" + window.location.host,'i').test(el.href)){
          return ;
        }
  			var params = {'url':el.href, 'method':'get', 'data':{'format':'html'}};
  			el.store('touchajax', params);
        el.store('touchajaxed', true);
  			el.addEvent('click', function(event)
        {
          event.stop(); // Maybe move this to after next line when done debugging
          try{
            location.hash = el.get('href');
//            self.request(el);
          } catch(e){
            if(el.href)
              window.location.href = el.href;
          }
  			});
  		});
    }
	},

	prepareParams: function(el, params){
		var self = this;
		var touch = Touch;

		//Params
		if( $type(params) != 'object' ){
			params = el.retrieve('touchajax');
		}

		if( $type(params) != 'object' ){
			params = {};
		}

		if ($type(params.url) != 'string' && $type(el.get('href')) == 'string'){
			params.url = el.get('href');
		}

    if ($type(params.url) != 'string' && $type(el.get('action')) == 'string'){
  			params.url = el.get('action');
  		}

		if ($type(params.url) != 'string'){
			return false;
		}

		if ($type(params.method) != 'string' ){
			params.method = 'get';
		}

		if ($type(params.data) != 'object'){
			params.data = {'format':'touchajax'};
		} else
		if (params.data.format != 'touchajax'){
			params.data.format = 'touchajax';
		}

//		params.data.nocache = Math.random();
		
		//Contents
		if ($type(params.loading) == 'string' ){
			params.loading = $(params.loading);
		} else
		if ($type(params.loading) != 'element' ){
			params.loading = self.loading;
		}

		if($type(params.loading_content) == 'string'){
			params.lcontent = $(params.loading_content);
		}	else
		if($type(params.loading_content) != 'element'){
			params.lcontent = self.lcontent;
		}

		if ($type(params.replace_content) == 'string'){
			params.rcontent = $(params.replace_content);
		} else
		if ($type(params.replace_content) == 'element'){
			params.rcontent = params.replace_content;
		} else {
      params.rcontent = self.rcontent;
    }

    if (params.noChangeHash == undefined){
      params.noChangeHash = 0;
    }
    if(params.change_dir == undefined){
      params.change_dir = false;
    }
		return params;
	},

	request: function(el, params, silence){
    var p = this.prepareParams(el, params);
    var page = _Cache.getPage(p);
    if(Touch.DPage && Touch.DPage.isComplete()){
//      alert('Touch.DPage = null;');
      Touch.DPage = null;
    }
    if(page){
      this.simulateRequest(page, el, p, silence);
    } else {
      var self = this;
      var touch = Touch;
      var r = new Request.HTML({
        url: p.url,
        method: p.method,
        data: p.data,
        evalScripts : true,

        onRequest:function(){
          touch.hash = p.url.replace(self.baseUrl,'');

          if (!p.noChangeHash){
            touch.hash = p.url.replace(self.baseUrl,'');
            location.hash = touch.hash;
          }

          if ($type(Smoothbox.box) == 'element' && Smoothbox.box.retrieve('opened', false))
          {
            Smoothbox.close();
          }

          if ($type(p.lcontent) == 'element' && !silence){
            var y = (p.lcontent.getSize().y > 100) ? p.lcontent.getSize().y : 100;
            p.loading.setStyle('height', y + 'px');
            p.lcontent.setStyle('display', 'none');
          }

          if ($type(p.loading) == 'element' && !silence){
            p.loading.setStyle('display', 'block');
          }
        },

        onComplete : function(responseTree, responseElements, responseHTML, responseJavaScript){
          if (this.requestRand == self.requestRand){
            if(!silence){
              p.loading.setStyle('display', 'none');
              p.rcontent.setStyle('display', '');
            }
            touch.updateMainMenu(el);
          }
        },

        onFailure:function(){
//				Touch.message('An error has occurred!!!', 'error', '1000');
        },

        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          if (this.requestRand == self.requestRand){
            if($type(Touch.DPage)== 'object'){
              Touch.DPage.set(p.rcontent);
            }
            if('string' == $type(responseHTML) && responseHTML.length > 4){
              p.rcontent.set('html', responseHTML);
              var respContent = p.rcontent.getElement('.content_global');
              if(respContent){
                p.rcontent.set('html', respContent.innerHTML);
              }
            }
            touch.hash = touch.getHash();
            p.rcontent.setStyle('display', 'block');
            touch.bind(p.rcontent.getProperty('id'));
            var page = {
              body: responseHTML,
              js: responseJavaScript
            }
            _Cache.cachePage(p, page);
          }
        }
      })

      self.requestRand = r.requestRand = Math.random();
      r.send();
    } 
	},
  simulateRequest: function(page, el, params, silence)
  {
    var self = this;
    var responseHTML = page.body;
    var touch = Touch;
    var p = params;

    // on request
    touch.hash = p.url.replace(self.baseUrl,'');
    if (!p.noChangeHash){
      touch.hash = p.url.replace(self.baseUrl,'');
      location.hash = touch.hash;
    }
    if ($type(Smoothbox.box) == 'element' && Smoothbox.box.retrieve('opened', false))
    {
      Smoothbox.close();
    }

    //on complete
    touch.updateMainMenu(el);
    if(p.loading)
      p.loading.setStyle('display', 'none');
    $exec(page.js);

    // on success
    touch.hash = touch.getHash();
    if(p.rcontent){
      if(!silence)
        p.rcontent.addClass('content_global_hide');
      if($type(Touch.DPage) == 'object'){
        Touch.DPage.set(p.rcontent);
      }
      if('string' == $type(responseHTML) && responseHTML.length > 4){
        p.rcontent.set('html', responseHTML);
        var respContent = p.rcontent.getElement('.content_global');
        if(respContent){
          p.rcontent.set('html', respContent.innerHTML);
          console.log(respContent);
        }
      }
      if(!silence)
        p.rcontent.setStyle('display', 'block');
      touch.bind(p.rcontent.getProperty('id'));
      if(!silence){
        p.rcontent.removeClass('content_global_hide');
        p.rcontent.addClass('content_global_effect');
        setTimeout(function(){
          p.rcontent.removeClass('content_global_effect');
        }, 300);
      }
    }
  }
});