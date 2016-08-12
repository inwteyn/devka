/* $Id: blog.js 2010-08-31 16:02 idris $ */

var page_blog = {
	
	page_id: 0,
    ipp: 10,
	page_num: 1,
	form_id: '',
	list_id: '',
	container_id: '',
	options_id: '',
	$form: {},
	$list: {},
	$container: {},
	allowed_post: 0,
	allowed_comment: 0,
	block: false,
	url: {},
	blog_id: 0,
	mine: false,
	comment_options: '.comments_options a',
	comment_form: 'blog-comment-form',
	count_span: '.tab_layout_pageblog_profile_blog a span',
	blog_tab: '.tab_layout_pageblog_profile_blog a',
  blog_tab_li: '.more_tab li.tab_layout_pageblog_profile_blog',
	tabs_container_id: 'main_tabs',
	navigation_buttons_class: '#page_blog_options .page_content_navigation li a',
	loader_id: 'pageblog_loader',
	$loader: '',

	init: function(){
		var self = this;
		
		if (this.allowed_post){
			this.$form = $(this.form_id);
		}
		
		this.init_comments();
		this.$loader = $(this.loader_id);
		this.$container = $(this.container_id);
	},
	
	init_comments: function(){
    var self = this;
    if ($(this.comment_form)){
      var $form = $(this.comment_form);
      $($form.body).autogrow();
      en4.core.comments.attachCreateComment($form);
      en4.core.comments.$element = $('comments_pageblog');
      $form.addEvent('submit', function(e){
        e.stop();
      });
      $form.addEvent('focus', function(e){
        en4.core.comments.$element = $('comments_pageblog');
      });
      if (!this.allowed_comment){
        this.add_class($form, 'hidden');
      }
    }
    if (!this.allowed_comment){
      $$(this.comment_options).each(function($element){
        self.add_class($element, 'hidden');
      });
    }
  },
	
	init_blog: function(){
    if($$(this.blog_tab)[0])
  		tabContainerSwitch($$(this.blog_tab)[0], 'generic_layout_container layout_pageblog_profile_blog');
    else if($$(this.blog_tab_li)[0])
      tabContainerSwitch($$(this.blog_tab_li)[0], 'generic_layout_container layout_pageblog_profile_blog');
	},
	
	set_page : function(page){
		this.page_num = page;
		if (this.mine){
			this.my_blogs();
		}else{
			this.list();
		}

    var scrollExample = new Fx.Scroll(window);
    scrollExample.start(0, 0);
	},
	
	hide_blog_form: function(){
		if (!this.allowed_post){
			return ;
		}
		if (this.$form){
			this.add_class(this.$form, 'hidden');
		}
		this.$container.removeClass('hidden');
	},
	
	display_blog_form: function(){
		if (!this.allowed_post){
			return ;
		}
		this.$form.removeClass('hidden');
		this.add_class(this.$container, 'hidden');
	},
	
	add_class: function($element, css_class){
		if ($element.hasClass(css_class)){
			return ;
		}
		
		$element.addClass(css_class);
		return ;
	},
	
	inc_count: function(count){
		var $span = $$(this.count_span)[0];
		if (!$span){
			return false;
		}
		if (!count) count = 1;
		
		var str = $span.get('html');
		var new_count = str.substr(1, (str.length - 2)).toInt() + count;
		$span.innerHTML = "(" + new_count + ")";
		
		return true;
	},
	
	list: function(){
		if (this.mine){
			this.page_num = 1;
		}
		this.mine = false;
		var self = this;
		var data = {'page_id':self.page_id, 'format':'json', 'p':self.page_num, 'ipp': self.ipp};
		var request = this.request(this.url.list, data);
		
		self.show_loader();
		request.send();
	},
	
	my_blogs: function(){
		if (!this.mine){
			this.page_num = 1;
		}
		this.mine = true;
		var self = this;
		var data = {'page_id':self.page_id, 'format':'json', 'p':self.page_num};
		var request = this.request(this.url.my_blogs, data);
		
		self.show_loader();
		request.send();
	},
	
	view: function(blog_id){
		var self = this;
		var data = {'page_id':self.page_id, 'blog_id': blog_id, 'format':'json'};
		var request = this.request(this.url.view, data);

		request.onSuccess = function(response) {
			self.hide_blog_form();
			self.$container.innerHTML = response.html;
      response.html.stripScripts(true);
      en4.core.runonce.trigger();
			self.hide_loader();
			self.init();

			var options = {
				'container' : 'pageblog_comments',
				'html' : response.likeHtml,
				'url' : {
					'like' : response.likeUrl,
					'unlike' : response.unlikeUrl,
					'hint' : response.hintUrl,
					'showLikes' : response.showLikesUrl,
					'postComment' : response.postCommentUrl
				}
			};
			
			var pageBlogLikeTips = new LikeTips('pageblog', blog_id, options);
		}
		
		self.show_loader();
		request.send();
	},
	
	get_form_data: function($form){
		var data = {};
		
		data.blog_title = $('blog_title').value.trim();
		data.blog_tags = $('blog_tags').value;
		data.blog_body = window.tinyMCE.editors.blog_body.getContent().trim();
    data.photo_id = $('fancyblogphotoid').value;
		
		return data;
	},
	
	post: function($form){
		var self = this;
		var data = this.get_form_data($form);

    if( !data.blog_title ) {
      alert(en4.core.language.translate('Pageblog_Title_Empty'));
      return false;
    }

    if( !data.blog_body ) {
      alert(en4.core.language.translate('Pageblog_Body_Empty'));
      return false;
    }

		if (this.blog_id){
			data.blog_id = this.blog_id;
		}
		
		data.format = 'json';
		data.page_id = self.page_id;
		
		var request = this.request($form.action, data);
		
		self.show_loader();
		request.send();
		
		return false;
	},
	
	reset_form: function(){
		if(!this.$form){
			this.$form = $(this.form_id);
		}
		this.$form.reset();
		if (window.tinyMCE.editors.blog_body){
			window.tinyMCE.editors.blog_body.setContent("");
		}

    this.$form.file.value = '';
    $$(fancy.list.getChildren('li.file')).each(function($item){
      $item.dispose();
    });

    fancy.list.setStyle('display', 'none');

    fancy.fileList = [];
    fancy.remove();
	},
	
	create: function(){
		this.display_blog_form();
		this.reset_form();
    $('blog-demo-status').setStyle('display', 'block');
		this.$form.action = this.url.create;
		this.blog_id = 0;
	},
	
	delete_blog: function(blog_id){
    var self = this;
    var he_title = en4.core.language.translate('Delete Blog');
    var he_description = en4.core.language.translate('Are you sure you want to delete this blog?');

    he_show_confirm(he_title, he_description, function(){
      var data = {'page_id':self.page_id, 'blog_id':blog_id, 'format':'json'};
      var request = self.request(self.url.delete_url, data);

      self.show_loader();
      request.send();
    });
	},
	
	edit: function(blog_id){
		var self = this;
		var data = {'page_id':self.page_id, 'blog_id':blog_id, 'format':'json'};
		var request = this.request(this.url.edit, data);
		this.$form.action = this.url.save;
		this.blog_id = blog_id;
		
		request.onSuccess = function(response){
			if (response.error){
				self.$container.innerHTML = response.html;
				if (response.eval){
					eval(response.eval);
				}
			}else{
				self.fill_form(response.blog);
        self.edit_photo(response);
			}
			self.hide_loader();
			self.init();
		};
		
		self.show_loader();
		request.send();
	},
	
	fill_form: function(blog){
		if (!blog){
			return false;
		}
		
		this.$form.blog_title.value = blog.title;
		this.$form.blog_tags.value = blog.tags;
		this.$form.file.value = blog.photo_id;

		if (window.tinyMCE.editors.blog_body){
			window.tinyMCE.editors.blog_body.setContent(blog.body);
		}
		this.$form.blog_body.value = blog.body;
		
		this.add_class(this.$container, 'hidden');
		this.$form.removeClass('hidden');
		
		return true;
	},

  edit_photo: function(response) {
    var self = this;
    var $list = $('blog-demo-list');

    if (!response.photo){
      $('blog-demo-status').setStyle('display', 'block');
      $list.set('html', '');
      $list.setStyle('display', 'none');
      return ;
    }else{
      $('blog-demo-status').setStyle('display', 'none');
      $list.set('html', response.photo_html);
      $list.show();
    }

    $('blog_photo_action_remove').addEvent('click', function(){
      $(this).getParent('li').destroy();
      $('blog-demo-status').setStyle('display', 'block');
      $('blog-demo-browse').setStyle('display', 'block');
      $('blog-demo-list').setStyle('display', 'none');
      $list.setStyle('display', 'none');
      new Request.JSON({
        url: self.url.remove_photo,
        method: 'post',
        data: {
          'format': 'json',
          'photo_id': response.blog.photo_id
        }
      }).send();

      self.$form.file.value = '';
      return false;
    });

  },

	show_loader: function(){
		this.$loader.removeClass('hidden');
	},
	
	hide_loader: function(){
		this.$loader.addClass('hidden');
	},
	
	request: function(ajax_url, data){
		var self = this;
		data.no_cache = Math.random();		
		return new Request.JSON({
			'url': ajax_url,
			'method': 'post',
			'data': data,
			onSuccess: function(response){
				self.hide_blog_form();
				self.$container.innerHTML = response.html;
				if (response.eval){
					eval(response.eval);
				}
				self.hide_loader();
				self.init();
			}
		});
	}
};