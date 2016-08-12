/* $Id: music.js 2010-10-21 16:02 idris $ */

var page_music2 = {
  url:{},

  page_id: 0,
  user_id: 0,
  p: 1,
  ipp: 10,
  hide_class: 'hidden',
  playlist_id: 0,
	allowed_post: 0,
  allowed_comment: 0,
	mine: false,

  container: 'page_music_container',
  $container: null,

  form: 'form-upload-music',
  $form: null,

  method: 'post',
  format: 'json',

  loader: 'page_music_loader',
  $loader: null,

  cancel: 'music_cancel',
  $cancel: null,

  just_created: false,
  block: false,

	count_span: '.tab_layout_pagemusic_profile_music a span',

  art: {
    list: 'music_art-demo-list',
    clear: 'music_art-demo-clear',
    up: {}
  },

  file: {
    list: 'music-demo-list',
    clear: 'music-demo-clear',
    up: {}
  },

	music_tab: '.tab_layout_pagemusic_profile_music a',
  music_tab_li: '.more_tab .tab_layout_pagemusic_profile_music',

  comment: {form:'music-comment-form', options: '.comments_options a', element: 'comments_playlist'},

  init: function(){
    this.init_elements();
    this.init_forms();
  },

	init_comments: function(){
    var self = this;
    Smoothbox.bind();
    if ($(self.comment.form)){
      var $comment_form = $(self.comment.form);
      $($comment_form.body).autogrow();
      en4.core.comments.attachCreateComment($comment_form);
      en4.core.comments.$element = $(self.comment.element);
      $comment_form.addEvent('submit', function(e){
        e.stop();
      });
      $comment_form.addEvent('focus', function(e){
        en4.core.comments.$element = $(self.comment.element);
      });
      if (!self.allowed_comment){
        $comment_form.setStyle('display', 'none');
      }
    }
    if (!self.allowed_comment){
      $$(self.comment.options).each(function($element){
        addClass($element, self.hide_class);
      });
    }
  },

	init_music: function(){
    if( $$(this.music_tab)[0] )
      tabContainerSwitch($$(this.music_tab)[0], 'generic_layout_container layout_pagemusic_profile_music');
    else if( $$(this.music_tab_li)[0] )
      tabContainerSwitch($$(this.music_tab_li)[0], 'generic_layout_container layout_pagemusic_profile_music');

  },

  init_elements: function(){
    var self = this;
    if ($(this.container)){
      this.$container = $(this.container);
    }

    if ($(this.form)){
      this.$form = $(this.form);
    }

    if ($(this.loader)){
      this.$loader = $(this.loader);
    }

    if ($(this.cancel)){
      this.$cancel = $(this.cancel);
      this.$cancel.set('onClick', '');
      this.$cancel.removeEvents('click')
        .addEvent('click', function(e){
          e.stop();
          self.hide_form();
          self.show_container();
        });
    }

		if (window.music_art_up) this.art.up = music_art_up;
    if (window.music_up) this.file.up = music_up;
  },

  init_forms: function(){
    var self = this;
    if (this.$form){
      this.$form.removeEvents('submit');
      this.$form.addEvent('submit', function(e){
        e.stop();
        self.just_created = true;
        self.post_form(this);
      });
    }
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

	set_page : function(page){
		this.p = page;
		if (this.mine){
			this.manage();
		}else{
			this.index();
		}
	},

  index: function(){
		if (this.mine){
			this.p = 1;
		}
		this.mine = false;
		
    var request = this.request(this.url.index);
    
    this.show_loader();
    request.send();
  },

  manage: function(){
		if (!this.mine){
			this.p = 1;
		}
		this.mine = true;
		
    var request = this.request(this.url.manage);
    
    this.show_loader();
    request.send();
  },

  post_form: function($form){
    $form = $($form);
    var url = $form.action + '?' + $form.toQueryString();

    var request = this.request(url);
    
    this.show_loader();
    request.send();
  },

  create: function(){
    this.hide_form();
    this.hide_container();
    
    this.$form.getElements('h3')[0].set('html', en4.core.language.translate('Add New Songs'));
    this.$form.getElements('p.form-description')[0].set('html', en4.core.language.translate('Choose music from your computer to add to this playlist.'));

    this.show_form();
    $('music_art-demo-status').setStyle('display', 'block');
  },

  show_loader: function(){
    if (this.$loader){
      this.$loader.removeClass(this.hide_class);
    }
  },

  hide_loader: function(){
    if (this.$loader){
      this.$loader.addClass(this.hide_class);
    }
  },

  request: function(url, data){
    var self = this;

    if (!url){
      return false;
    }

    if (!data){
      data = {};
    }

    data.format = self.format;
    data.page_id = self.page_id;
    data.p = self.p;
    data.ipp = self.ipp;

    return new Request.JSON({
      'url': url,
      'method': self.method,
      'data': data,
      onSuccess: function(response){
        self.handle_response(response);
      }
    });
  },

  confirm_delete: function(playlist_id){
    var self = this;
    var callback = function(){
      self.remove(playlist_id);
    }
    he_show_confirm('Delete Playlist', 'Are you sure you want to delete this playlist?', callback, {confirm_label:'Delete Playlist', cancel_label:'Cancel', or_label:'or'});
  },

  edit: function(playlist_id){
    var self = this;
    var data = {'playlist_id':playlist_id};
    var url = this.url.edit;
    var request = this.request(url, data);
    self.playlist_id = playlist_id;
    
    request.onSuccess = function(response){
      self.init_edit(response.playlist);
      
      self.init_edit_songs(response.songs_html);
      self.init_edit_photo(response.photo_html);

      self.just_created = true;
      self.hide_container();
      self.show_form();
      
      self.hide_loader();
    }

    this.show_loader();
    request.send();
  },

  init_edit: function(playlist){
    this.$form.getElements('h3')[0].set('html', en4.core.language.translate('Edit Playlist'));
    this.$form.getElements('p.form-description')[0].set('html', en4.core.language.translate('Edit your playlist title, description, change playlist artwork, remove and order songs.'));
    
    this.$form.playlist_id.value = playlist.playlist_id;
    this.$form.music_title.value = playlist.title;
    this.$form.music_art_fileid.value = playlist.photo_id;
    this.$form.music_description.value = playlist.description;
    this.$form.music_tags.value = playlist.tags;
  },

  init_edit_photo: function(html){
		var self = this;
    var $list = $(this.art.list);
		
    if (!html){
      $('music_art-demo-status').setStyle('display', 'block');
      $list.set('html', '');
			$list.setStyle('display', 'none');
			return ;
    }else{
			$('music_art-demo-status').setStyle('display', 'none');
			$list.set('html', html);
			$list.show();
		}
		
    $('art_action_remove').addEvent('click', function(){
      $(this).getParent('li').destroy();
      $('music_art-demo-status').setStyle('display', 'block');
      $list.setStyle('display', 'none');
      new Request.JSON({
        url: self.url.remove_art,
        method: self.method,
        data: {
          'format': 'json',
          'page_id': self.page_id,
          'playlist_id': self.playlist_id
        }
      }).send();

      return false;
    });
  },

  init_edit_songs: function(html){
    var self = this;
    var $list = $(this.file.list);
		
    if (!html){
			$list.set('html', '');
			$list.setStyle('display', 'none');
			return ;
    }else{
			$list.set('html', html);
			$list.show();
		}
		
		$list.getElements('li span.file-name').setStyle('cursor', 'move');

    new Sortables(this.file.list, {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: self.url.order,
          method: self.method,
          noCache: true,
          data: {
            'format': 'json',
            'page_id': self.page_id,
            'playlist_id': self.playlist_id,
            'order': this.serialize().toString()
          }
        }).send();
      }
    });

    $$('a.song_action_rename').addEvent('click', function(){
      var origTitle = $(this).getParent('li').getElement('.file-name').get('text')
          origTitle = origTitle.substring(0, origTitle.length);
      var newTitle  = prompt('Rename the song.', origTitle);
      var song_id   = $(this).getParent('li').id.split(/_/);
          song_id   = song_id[ song_id.length-1 ];

      if (newTitle && newTitle.length > 0){
        newTitle = newTitle.substring(0, 60);
        $(this).getParent('li').getElement('.file-name').set('text', newTitle);
        new Request({
          url: self.url.rename,
          method: self.method,
          data:{
            'format': 'json',
            'page_id': self.page_id,
            'song_id': song_id,
            'playlist_id': self.playlist_id,
            'title': newTitle
          }
        }).send();
      }
      return false;
    });

    $$('a.song_action_remove').addEvent('click', function(){
      var song_id  = $(this).getParent('li').id.split(/_/);
          song_id  = song_id[song_id.length-1];
      
      $(this).getParent('li').destroy();
      new Request.JSON({
        url: self.url.remove_song,
        method: self.method,
        data: {
          'format': 'json',
          'page_id': self.page_id,
          'song_id': song_id,
          'playlist_id': self.playlist_id 
        }
      }).send();

      return false;
    });

  },

  remove: function(playlist_id){
    var data = {'playlist_id':playlist_id};
    var request = this.request(this.url.delete_url, data);
    
    this.show_loader();
    request.send();
  },

  view: function(playlist_id){
	var self = this;
    var data = {'playlist_id':playlist_id};
    var url = this.url.view;

    var request = this.request(url, data);
		this.playlist_id = playlist_id;
    
		request.onSuccess = function(response){
			self.handle_response(response);
			self.init_comments();
			if (response.song_id){
				window.setTimeout(function(){self.play(response.song_id, $('pagemusic_play_btn_'+response.song_id));}, 200);
			}
			
			var options = {
				'container' : 'playlist_comments',
				'html' : response.likeHtml,
				'url' : {
					'like' : response.likeUrl,
					'unlike' : response.unlikeUrl,
					'hint' : response.hintUrl,
					'showLikes' : response.showLikesUrl,
					'postComment' : response.postCommentUrl
				}
			};

			var pageMusicLikeTips = new LikeTips('playlist', playlist_id, options);
		};

    this.show_loader();
      console.log(this.url);
      console.log(request);
    request.send();
  },

	view_song: function(song_id){
		var self = this;
    var data = {'song_id':song_id};
    var url = this.url.view;
    var request = this.request(url, data);
//		this.playlist_id = playlist_id;

		request.onSuccess = function(response){
			self.handle_response(response);
			self.init_comments();
			if (response.song_id){
				window.setTimeout(function(){self.play(response.song_id, $('pagemusic_play_btn_'+response.song_id));}, 200);
			}
		};

    this.show_loader();
    request.send();
  },

  handle_response: function(response){
    this.hide_form();
    this.$container.set('html', response.html);
    this.show_container();
    
    this.scripts();

    if (response.eval){
      eval(response.eval);
    }
    //response.html.stripScripts(true);
    en4.core.runonce.trigger();
    this.hide_loader();
  },

  hide_form: function(){
    if (this.$form){
      this.$form.addClass(this.hide_class);
      
      this.$form.reset();
      this.$form.playlist_id.value = '';
			this.$form.music_art_fileid.value = '';
			this.$form.music_fancyuploadfileids.value = '';
      
      $$(music_art_up.list.getChildren('li.file')).each(function($item){
        $item.dispose();
      });
      music_art_up.list.setStyle('display', 'none');
      
      $(this.art.clear).setStyle('display', 'none');
      if (!this.just_created && music_art_up.fileList.length){
        music_art_up.remove();
      }else{
        music_art_up.fileList = [];
        music_art_up.remove();
      }

      $$(music_up.list.getChildren('li.file')).each(function($item){
        $item.dispose();
      });
      music_up.list.setStyle('display', 'none');
      
      $(this.file.clear).setStyle('display', 'none');
      if (!this.just_created && music_up.fileList.length){
        music_up.remove();  
      }else{
        music_up.fileList = [];
        music_up.remove();
      }
    }
  },

  show_form: function(){
    if (this.$form){
      this.just_created = false;
      this.$form.removeClass(this.hide_class);
    }
  },

  hide_container: function(){
    if (this.$container){
      this.$container.addClass(this.hide_class);
    }
  },

  show_container: function(){
    if (this.$container){
      this.$container.removeClass(this.hide_class);
    }
  },

  scripts: function(){
    if (this.$container){
      $$(this.$container.getElements('script')).each(function($script){
        eval($script.innerHTML);
      });
    }
  },

	play: function(playerID){
		var self = this;
    var song_id = playerID.replace('song_', '').toInt();
		var data = {'song_id':song_id};
		var request = this.request(this.url.play, data);
		request.onSuccess = function(){};
		request.send();
	}
}