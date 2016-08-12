// preventing the bug of chootools
Brower = Browser;

function trim(string)
{
return string.replace(/(^\s+)|(\s+$)/g, "");
}
//Main Touch object
var TouchClass =new Class({

  referrerFavicon: '',
  scroll: null,
  picup_up: false,
  DPage: null,
  ajaxing: false,
	bind:function( block){
		var self = this;

		if ($type(block) != 'string' && $type(block) != 'element'){
			self.hash  = '';

			//Main Wrappers
			self.header = $('global_header');
			self.wrapper = $('global_wrapper');
			self.globalbind = $('global_bind');
			self.footer = $('global_footer');

			//Menus
			self.mainmenu = $$('.layout_touch_menu_main');
		}

		Touchajax.bind(block);
		Touchconfirm.bind(block);
		Smoothbox.bind(block);
		Touchform.bind(block);
    if(!this.scroll)
    this.scroll = new Fx.Scroll(document.body, {
            wait: false,
            duration: 1000,
            offset: {'x': 0, 'y': 0},
            transition: Fx.Transitions.Quad.easeInOut
          });
		self.navigation.bind();

		en4.core.runonce.trigger();

    // Implementing Element.hasEvent(string eventType, fn)
    Element.implement({
      hasEvent: function(eventType,fn) {
        //get the element's events
        var myEvents = this.retrieve('events');
        //can we shoot this down?
        return myEvents && myEvents[eventType] && (fn == undefined || myEvents[eventType].keys.contains(fn));
      }
    });
	},

	navigation:{
    navslide: null,
		bind: function(){
			var self = Touch;


			if ($type($('navigation-items')) == 'element')
			{
				self.navigator = $('navigation-selector');
				self.navigatorItems = $('navigation-items');
				self.navigatorContent = $('navigation_content');

				var h = self.navigatorItems.getSize().y;
				self.navigatorItems.setStyle('display', 'none');

				self.navigatorContent.setStyle('min-height', h);

				var myFx = new Fx.Slide('navigation-items', {'duration':200});
        this.navslide = myFx;
				myFx.slideOut().chain(function(){self.navigatorItems.setStyle('display', 'block')});
				self.navigator.addEvent('click', function(){
					mt = self.navigatorItems.getStyle('margin-top');
					ne = $('navigation_expandable');
					ne.erase('class');
					if($$('div.navigation-body>div')[0].getHeight()>0){
						ne.addClass('collapsed');
					} else {
						ne.addClass('expanded');
					}
					myFx.toggle().chain(function(){self.navigatorItems.setStyle('display', 'block')});
 
				});
				if($('navigation_expandable')==null){
				  navtmphtml = $('navigation-selector').innerHTML;
				  navtmphtml = '<span>'+navtmphtml+'</span><span id="navigation_expandable" class="collapsed"></span>';
				  $('navigation-selector').innerHTML = navtmphtml;
				}
			}
		},

		request: function(el){
			var self = Touch;
      if(this.navslide)
      this.navslide.slideOut();
      location.hash = el.get('href');

//			Touchajax.request(
//				el,
//				{
//					'loading_content':'navigation_content',
//					'loading':'navigation_loading'
//				}
//			);
		},

    subNavRequest: function(el){
      if($type($('sub_navigation_content')) != 'element')
       return this.request(el);
      var self = Touch;
      if(self.navigatorItems)
      self.navigatorItems.setStyle('display', 'none');

      Touchajax.request(
        el,
        {
          'loading_content':'sub_navigation_content',
          'loading':'sub_navigation_loading',
          'replace_content':'sub_navigation_content',
          'noChangeHash':true
		}
      );
      snia = $('sub_nav_item_active');
      if(snia)
      snia.erase('id');
      el.set('id', 'sub_nav_item_active');
    },

    subRequest: function(el, loading_container, replace_container){
      var self = Touch;

      Touchajax.request(
        el,
        {
          'loading_content':loading_container,
          'loading':loading_container,
          'replace_content':replace_container,
          'noChangeHash':true
        }
      );
    }

	},

	suggestTo: function(toValues) {
    var url = en4.core.baseUrl+'suggest/index/suggest';
		var request = new Request.JSON({
		  url : url,
      'method': 'post',
			data : {
        format: 'json',
        contacts: toValues,
        object_type: suggestOptions.params.object_type,
        suggest_type: suggestOptions.params.suggest_type,
        object_id: suggestOptions.params.object_id
			},
      onSuccess : function(response) {
        Touch.message(response.message, response.type, 3500);
      }
		});
	  request.send();
	},

	feed: {
		focus: function(el, default_class){
			if (el.hasClass(default_class)) {
				el.removeClass(default_class);
				el.value = '';
				el.getParent('div.activity-post-container').getElement('div.feed-submit').removeClass('feed-submit-hidden');
			}
		},

		blur: function(el, default_class, default_value){
			if (el.value.trim() == '') {
				el.addClass(default_class);
				el.value = default_value;

				el.getParent('div.activity-post-container').getElement('div.feed-submit').addClass('feed-submit-hidden');
			}
		},

		viewmore: function(next_id, subject_guid, url){
			var self = Touch;

			if( en4.core.request.isRequestActive() ) return;
			$('feed_viewmore').style.display = 'none';
			$('feed_loading').style.display = '';

			var request = new Request.HTML({
				url : url,
				data : {
					format : 'html',
					'maxid' : next_id,
					'feedOnly' : true,
					'nolayout' : true,
					'subject' : subject_guid
				},
				evalScripts : true,
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
					Elements.from(responseHTML).inject($('activity-feed'));
					self.bind('activity-feed');
				}
			});
			request.send();
		},

		post: function(form){
			var self = Touch;

			var url = form.getProperty('action');
			var body = form.getElement('textarea[name=body]').value;
			var return_url = form.getElement('input[name=return_url]').value;

			if($type(form.getElement('input[name=subject]'))) {
				var subject = form.getElement('input[name=subject]').value;
			}
       var share_txt = $('touch-compose-submit').get('text');
      var submit_span = $('touch-compose-submit').getElement('span');
			new Request.JSON({
				'url': url,
				'method': 'post',
				'data': {'format':'json', 'body': body, 'return_url': return_url, 'subject': subject, 'no_cache': Math.random()},
				'onRequest': function(){
				//	$('feed-post-loading').setStyle('display', 'inline-block');
          submit_span.set('text',$('feed-post-loading').get('text'));
          submit_span.addClass('posting');
				},
				'onSuccess': function(response){
					if(response.status){
						var action_id = response.action_id;
						var li = new Element('li', {'id':'activity-item-'+action_id});
						li.innerHTML = response.body;
						li.fade('out');

						setTimeout(function(){
              if($type($('activity-feed')) != 'element'){
                var feed_layout = document.body.getElement('.layout_touch_activity_feed');
                if(feed_layout && feed_layout.getElement('.tip'))
                  feed_layout.getElement('.tip').dispose();
                feed_layout.grab(new Element('ul', {'id':'activity-feed', 'class':'feed'}), 'bottom');
							$('activity-feed').grab(li, 'top');
              }
							$('activity-feed').grab(li, 'top');

							li.fade('in');
							$('activity-comment-body-' + action_id).autogrow();
							en4.activity.attachComment($('activity-comment-form-' + action_id));
							self.bind(li.getProperty('id'));
							}, '200');
					}
				},
				'onComplete':function(){
					$('feed-post-loading').setStyle('display', 'none');
					$('body').value = '';
					$('body').focus();
					$('body').blur();
					$('body').set('style', '');
          $('touch-compose-submit').getElement('span').set('text',share_txt);
          $('touch-compose-submit').getElement('span').removeClass('posting');
				}
			}).send();
		}
	},

	comment: {
		bindBox:function(form){
			$(form.body).autogrow();
			en4.core.comments.attachCreateComment(form);
		},

		bindLikes:function(likes, likesUrl, likesLoadingText){
			if (likes[0] == undefined){return; }
			if (likes[0].retrieve('mouseovered', false)){
				return;
			}

			var CommentLikesTooltips;
			likes[0].store('mouseovered', true);


			likes.addEvent('mouseover', function(event) {
				var el = $(event.target);
				if( !el.retrieve('tip-loaded', false) ) {
					el.store('tip-loaded', true);
					el.store('tip:title', likesLoadingText);
					el.store('tip:text', '');
					var id = el.get('id').match(/\d+/)[0];
					// Load the likes
					var req = new Request.JSON({
						url : likesUrl,
						data : {
							format : 'json',
							type : 'core_comment',
							id : id
						},
						onComplete : function(responseJSON) {
							el.store('tip:title', responseJSON.body);
							el.store('tip:text', '');
							CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
						}
					});
					req.send();
				}
			});

			CommentLikesTooltips = new Tips(likes, {
				fixed : true,
				className : 'comments_comment_likes_tips',
				offset : {
					'x' : 48,
					'y' : 16
				}
			});
		}
	},

	filter:function(el, url, search, params, form){
		var u = ($type(url) == 'string')? url:el.href;
		var s = ($type(search) == 'string')? search:el.value;
    var l = 'filter_loading';
    var lc = 'filter_block';
    var rc = Touchajax.rcontent;
    var noChangeHash = 0;

    if ($type(params) == 'object'){
      if (params.loading){ l = params.loading; }
			if (params.loading_content){ lc = params.loading_content; }
      if (params.replace_content){ rc = params.replace_content; }
      if (params.noChangeHash){ noChangeHash = params.noChangeHash; }
    }
    if(Touch.isOperaMini() || Touch.isBlackBerry()){
      if($type(form) == 'element'){
        form.getElement('#keyword').set('value', s);
        form.submit();
      }
      return;
    }
		Touchajax.request(
			el,
			{
				'url':u,
				'method':'post',
				'data': {'search': s, 'keyword': s},
				'loading_content':lc,
				'loading':l,
        'replace_content':rc,
        'noChangeHash': noChangeHash
			}
		);
	},

  subNavFilter:function(el, url, search, params){
    var u = ($type(url) == 'string')? url:el.href;
    var s = ($type(search) == 'string')? search:el.value;
    var l = 'filter_loading';
    var lc = 'filter_block';
    var rc = 'sub_navigation_content';
    var noChangeHash = 1;

    if ($type(params) == 'object'){
      if (params.loading){ l = params.loading; }
      if (params.loading_content){ lc = params.loading_content; }
      if (params.replace_content){ rc = params.replace_content; }
      if (params.noChangeHash){ noChangeHash = params.noChangeHash; }
    }

    Touchajax.request(
      el,
      {
        'url':u,
        'method':'post',
				'data': {'search': s},
				'loading_content':lc,
				'loading':l,
        'replace_content':rc,
        'noChangeHash': noChangeHash
			}
		);
	},

	refresh:function(silent){
			var self = this;
			var url = self.getHash();
			if (url.length > 0){
				Touchajax.request(null, {'url':url, 'noChangeHash': true}, silent);
			} else if(!silent){
				location.reload();
			}
	},

	goto:function(url){
		var $a = new Element('a', {'href':url});
		Touchajax.request($a);

	},

	getHash:function(){
		var url = location.hash.replace('#','/');
		url = url.replace('//', '/');
		return url;
	},

	updateMainMenu:function(el){
		var self = this;
		var flag = true;
    if(!self.mainmenu){
      self.mainmenu = $$('.layout_touch_menu_main');
    }

		var items = self.mainmenu.getElements('a');
		items[0].each(function(item){
			item.getParent('li').removeClass('selected');
			if (self.hash.indexOf(item.getProperty('href')) != -1){
				item.getParent('li').addClass('selected');
			}
		});

		if ($type(el) == 'element' && el.hasClass('main-menu-item')){
			el.getParent('li').addClass('selected');
		}
	},

	focus: function(el, blur_class){
		var b = ($type(blur_class) == 'string')?blur_class:'filter_default_value';
		if (el.hasClass(b)) {
			el.removeClass(b);
			el.value = '';
		}
	},

	blur: function(el, blur_class, default_value){
		var b = ($type(blur_class) == 'string')?blur_class:'filter_default_value';

		if (el.value.trim() == '') {
			el.addClass(b);
			el.value = default_value;
		}
	},

	getBlock: function(block){
		var self = this;

		if ($type(block) == 'element'){
			return block
		} else
		if ($type(block) == 'string'){
			return $(block);
		} else {
			return self.globalbind;
		}
	},

	message: function(message, type, delay) {
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
	},

    isFlash: function(){
    var search = 0;
    (new Hash(navigator.mimeTypes)).each(function (item){
        if (item.type == 'application/x-shockwave-flash'){
            search = 1;
        }
    });
    return search;
  },
  detectFlashPlayer: function(alertresult){
    var hasFlash = false;
    try {
      var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
      if(fo) hasFlash = true;
    } catch(e) {
      if(navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) hasFlash = true;
    }
    if(alertresult){
      if(hasFlash)
       alert('Flashplayer is installed on the browser');
      else
       alert('Flash is not installed or not support');
    }
    return hasFlash;
  },

	isIPhone:function(){
		return ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPad/i)));
	},
	isAndroid:function(){
		return ((navigator.userAgent.match(/android/i)) || (navigator.userAgent.match(/Android/i)));
	},
  isBlackBerry: function(){
    return ((navigator.userAgent.match(/blackberry/i)) || (navigator.userAgent.match(/Blackberry/i)) || (navigator.userAgent.match(/BlackBerry/i)));
  },
  isOperaMini: function(){
    return ((navigator.userAgent.match(/Opera Mini/i)) || (navigator.userAgent.match(/Opera Mobi/i)) || (navigator.userAgent.match(/opera mini/i)) || (navigator.userAgent.match(/opera mobi/i)));
  },
  getUserAgent:function(){
    var userAgent = {};
  },

	object_to_query_string:function(object, name){
		if ($type(name) != 'string') { name = 'params'; }
		var query = "";
		for(key in object){
			if ($type(object[key]) == 'string' || $type(object[key]) == 'number'){
				query += '&' + name + '['+key+']='+object[key];
			}
		}
		return query;
	},


	print_arr:function (object, flag){
		var type = typeof(object);
		var output = '';
		var property = null;

		switch (type){
			case 'object':{
				for (property in object){
					output += property + ': ' + this.print_arr(object[property], true)+'; ';
				}
			}
			break;
			case 'array':{
				for (var i = 0; i < object.length; i++){
					output += i + ': ' + this.print_arr(object[i], true)+'; ';
				}
			}
			break;
			case 'string': {
				output = '"' + object + '"';
			}
			break;
			case 'number':
			default: {
				output = object;
			}
		}

		if (flag){
			return output;
		}


		if (window.console !== undefined){
			window.console.log(output);
		}else{
			alert(output);
		}
	},
  getElementCSSPath: function(el){
    var names = [];
    while (el.parentNode){
      if (el.id){
        names.unshift('#'+el.id);
        break;
      }else{
        for (var c=1,e=el;e.previousElementSibling;e=e.previousElementSibling,c++);
        names.unshift(el.tagName+":nth-child("+c+")");
        el=el.parentNode;
      }
    }
    return names.join(" > ");
  },
  isMaintenanceMode : function(){
    return document.getElementsByName('is_maintenance')[0] && document.getElementsByName('is_maintenance')[0].get('content');
  },
  isTimeline: function(){
    return $type($('tl-cover')) == 'element'
  }

});

//Touch vars
var Touch = new TouchClass();
var Touchconfirm = new TouchconfirmClass();
var Touchajax = new TouchajaxClass();
var Touchform = new TouchformClass();
var Smoothbox = new SmoothboxClass();
var Photobox = new PhotoboxClass();
var old_hash = location.hash;

window.addEvents({
	'load':function(){

	},

	'domready':function()
  {

    if ("onhashchange" in window) {
      window.onhashchange = function(e){
        //if ((location.hash != '#'+location.pathname || (Touch.getHash() != Touch.hash && Touch.hash.length>0)) && location.hash.search('serverResponse')==-1 && !Touch.picup_up){
        if (location.hash != '#'+location.pathname && location.hash.search('serverResponse')==-1 && Touch.getHash() != Touch.hash){
          Touch.refresh();
        }
      }
    } else {
      window.addEvent('hashchange', function(e){
        if (location.hash != '#'+location.pathname && location.hash.search('serverResponse')==-1 && Touch.getHash() != Touch.hash){
          Touch.refresh();
        }
      });
      setInterval(function(){
        if(old_hash != location.hash)
          window.fireEvent('hashchange');
        old_hash = location.hash;
      }, 100);
    }
		Touch.bind();
		if (location.hash.trim().length > 0 && location.hash != '#'+location.pathname)
    {
			Touch.refresh();
		}
    else
    {
      if(location.pathname!= undefined)
        location.hash = location.pathname;
		}
	},

	'resize':function()
  {
		Photobox.resize();
		Smoothbox.resize();
	}

});

function toggle_page_edit_tab(link_hide, link_show, tab_id, tab_desc_id, visible)
{
	if (visible){
		$(tab_id).setStyle('display', 'block');
		$(tab_id).removeClass('hidden');
		$(tab_desc_id).setStyle('display', 'none');
		$(tab_desc_id).addClass('hidden');
	}else{
		$(tab_id).setStyle('display', 'none');
		$(tab_id).addClass('hidden');
		$(tab_desc_id).setStyle('display', 'block');
		$(tab_desc_id).removeClass('hidden');
	}
	$(link_hide).setStyle('display', 'none');
	$(link_hide).addClass('hidden');
	$(link_show).setStyle('display', 'inline');
	$(link_show).removeClass('hidden');
};

var page =
{
	page_id: 0,
	ajax_url: '',
	block: false,
  note: '',
  empty_note: '',

	init: function(){
		var self = this;
		$$('.team_title_input').addEvent('blur', function(){
			var admin_id = parseInt(this.id.substr(18));
			var title = this.value;
			if ($('admin_title_'+admin_id).innerHTML == title){
				self.hide_admin_edit(admin_id);
				self.show_admin_info(admin_id);
			}else{
				self.change_title(admin_id, title);
			}
		});
	},

	prepare_post: function(page_id){
		var self = this;
		this.page_id = page_id;
    if (!this.note) {
      this.note = '';
    }
		this.edit_mode(true);
	},

	edit_mode: function(flag){
		if (flag){
			$('profile_note_link').setStyle('display', 'none');
			$('profile_note_textarea').setStyle('display', 'block');
			$('profile_note_text').setStyle('display', 'none');
			$$('#profile_note_textarea textarea')[0].value = this.note;
			$$('#profile_note_textarea textarea')[0].focus();
		}else{
			$('profile_note_link').setStyle('display', 'block');
			$('profile_note_textarea').setStyle('display', 'none');
			if ($('profile_note_text').innerHTML){
				$('profile_note_text').setStyle('display', 'block');
			}
		}
	},

  getPages: function() {
    var params = {
      'm': 'page',
      'l': 'getPages',
      'c': 'page.addFavorites',
      't': 'Choose pages to add to favorites list',
      'params': {
        'approved': 1,
        'favorite': en4.core.subject.id,
        'team_id': en4.user.viewer.id
      }
    };

    var contacts = new HEContacts(params);
    contacts.box();
  },

  addFavorites: function(contacts) {
    new Request.JSON({
      'url': en4.core.baseUrl + 'page-team/add-favorites/' + en4.core.subject.id,
      'method': 'post',
      'data': {
        'favorites': contacts,
        'format': 'json'
      },
      onSuccess: function(response) {
        he_show_message(response.message, response.type, 3500);
      }
    }).send();
  },

	change_title: function(admin_id, title){
		var self = this;

		if (this.block){
			return ;
		}
		this.block = true;

		$('admin_title_input_'+admin_id).disabled = true;
		new Request.JSON({
			'url' : self.ajax_url,
			'method' : 'post',
			'data' : {
				'admin_id' : admin_id,
				'title' : title,
				'format': 'json',
				'task' : 'change_title',
				'page_id' : self.page_id
			},
			onSuccess : function(response) {
				$('admin_title_'+admin_id).innerHTML = title;
				self.hide_admin_edit(admin_id);
				self.show_admin_info(admin_id);
				self.block = false;
				$('admin_title_input_'+admin_id).disabled = false;
			}
		}).send();
	},

	post_note: function(note){
		var self = this;
		if (note.trim() == this.note){
			self.edit_mode(false);
			return ;
		}
		$$('#profile_note_textarea textarea')[0].disabled = true;
		new Request.JSON({
			'url': self.ajax_url+'?page='+self.page_id,
			'method': 'post',
			'data': {
				'note': note,
				'task' : 'post_note',
				'format': 'json'
			},
			onSuccess: function(response){
        self.note = note;
				if (!response.result){
					$('profile_note_text').innerHTML = en4.core.language.translate("There was error.");
				}else{
          if (self.note.trim() == ''){
					  $('profile_note_text').innerHTML = self.empty_note;
          }else{
            $('profile_note_text').innerHTML = response.note;
          }
				}
				$$('#profile_note_textarea textarea')[0].disabled = false;
				self.edit_mode(false);
			}
		}).send();
	},

	choose_admins: function(){
		he_contacts.box('page', 'getUsersForAdmin', 'page.add_admins', en4.core.language.translate('Add admins'), {page_id:this.page_id});
	},

	add_admins: function(admins){
		var self = this;
		if (this.block){
			return ;
		}
		this.block = true;
		new Request.JSON({
			'url': self.ajax_url,
			'method': 'post',
			'data': {
				'user_ids': admins,
				'format': 'json',
				'task' : 'add_admins',
				'page_id': self.page_id
			},
			onSuccess: function(response){
				self.block = false;
				window.location.href = window.location.href;
			}
		}).send();
	},

	hide_admin_info: function(admin_id){
		$('admin_title_'+admin_id).addClass('hidden');
		$('admin_title_edit_'+admin_id).addClass('hidden');

		$('admin_title_'+admin_id).removeClass('visible');
		$('admin_title_edit_'+admin_id).removeClass('visible');
	},

	show_admin_info: function(admin_id){
		$('admin_title_'+admin_id).addClass('visible');
		$('admin_title_edit_'+admin_id).addClass('visible');

		$('admin_title_'+admin_id).removeClass('hidden');
		$('admin_title_edit_'+admin_id).removeClass('hidden');
	},

	show_admin_edit: function(admin_id){
		$('admin_title_input_box_'+admin_id).addClass('visible');
		$('admin_title_input_box_'+admin_id).removeClass('hidden');
	},

	hide_admin_edit: function(admin_id){
		$('admin_title_input_box_'+admin_id).addClass('hidden');
		$('admin_title_input_box_'+admin_id).removeClass('visible');
	},

	focus_input: function(admin_id){
		$('admin_title_input_'+admin_id).focus();
	},

	edit_admin_title: function(admin_id){
		this.hide_admin_info(admin_id);
		this.show_admin_edit(admin_id);
		this.focus_input(admin_id);
	}

};
DynPage = new Class({
  title:'',
  body_id: '',
  headScripts: [],
  innerScript: '',
  oldHeadScripts: new Elements(),
  oldHeadScriptsEl: [],
  tmpBodyEl: null,
  HSCount: 0,
  HSLoad: 0,
  common_js_src: [],
  completed: false,

  // Initialize
  initialize:function(title, body_id, headscript, innerscript, headstyle, body){

    this.headScripts = [];
    this.oldHeadScripts = [];
    this.oldHeadScriptsEl = new Elements();
    this.common_js_src = [];
    this.innerScript = innerscript;
    var self = this;
    this.title = title;
    this.body_id = body_id;
    this.headScripts = headscript;

    this.tmpBodyEl = new Element('div', {'html': body});
    this.oldHeadScriptsEl = document.head.getElements('script[dynamic="1"]');
    this.oldHeadScripts = this.oldHeadScriptsEl.get('src');
    this.common_js_src = [];
    this.headScripts.each(function(script){
      if(self.oldHeadScripts.contains(script)){
        self.common_js_src.push(script);
      }
    });
    this.common_js_src.each(function(common){
      if(self.headScripts.contains(common))
        self.headScripts.erase(common);
    });
    this.HSCount = this.headScripts.length;
  },
  // Sets the loaded page
  set: function(replace_content){
    var self = this;
    if($type(replace_content) == 'string')
      replace_content = $(replace_content);
    if($type(replace_content) == 'element'){
      replace_content.set('html', this.tmpBodyEl.get('html'));
    }

    document.head.getElement('title').set('text', this.title);
    document.body.set('id', this.body_id);

    // Inserting new js files into head
    this.headScripts.each(function(script){
//      var fileref = Asset.javascript(script, {
//        dynamic: '1',
////        defer: 'defer',
//        onload: function(e){
//                self.onScriptLoad(this, e);
//              }
//      });

        new Request.HTML({
          url: script,
          method: 'get',
          onComplete : function(responseTree, responseElements, responseHTML, responseJavaScript){
              $exec(responseHTML);
              self.onScriptLoad(script);
          },
          onFailure:function(){
            new Request.JSON({
              url: $$('head base[href]')[0].get('href') + 'touch/utility/load-js',
              data: {
                format: 'json',
                useragent: navigator.userAgent,
                src: script
              },
              onSuccess: function(resp) {
                $exec(resp.script);
                self.onScriptLoad(resp.script);
              },
              onFailure:function(){
                alert('Ooops! Dynamic Loading '+script+' Failed! :(');
              }
            }).send();
          }
        }).send();


//      var fileref=document.createElement('script');
//      fileref.setAttribute("type","text/javascript");
//      fileref.setAttribute("dynamic", 1);
//      fileref.setAttribute("defer", 'defer');
//      fileref.onload = function(e){
//        self.onScriptLoad(this, e);
//      };
//      fileref.setAttribute("src", script)
//      if (typeof fileref == "undefined"){
//        alert('Ooops! Dynamic Loading Failed! :(');
//      }
    });
    if(this.headScripts.length==0)
      setTimeout(function(){self.execInnerJS()}, 100);

    //this.clearUnused();

  },

  onScriptLoad: function(script){
    console.log('Loading Success: '+script);
    this.HSLoad++;
    if(this.HSLoad == this.HSCount)
      this.execInnerJS();
  },

  // Clears from head unused scripts
  clearUnused: function(){
    var self = this;
    this.oldHeadScriptsEl.each(function(script){
      if(!self.common_js_src.contains(script.get('src'))){
        console.log('Clear: '+script.get('src'));
        script.dispose();
      }
    });
  },

  // Executes inner Javascript code
  execInnerJS: function(){
    try{
      $exec(this.innerScript);
      this.tmpBodyEl.getElements('script').get('text').each(function(js){
        $exec(js);
      });
      this.completed = true;
    }catch(e){
      console.log(e);
      this.completed = false;
    }
    window.fireEvent('load');
},
  isComplete: function(){
    return this.completed;
  }
});
