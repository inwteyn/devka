/* $Id: album.js 2010-09-06 16:02 idris $ */

var pageAlbumPhotoLikeTips = null;

var page_album = {

  page_id: 0,
  ipp: 10,
  page_num: 1,
  allowed_post: 0,
  allowed_comment: 0,
  block: false,
  url: {album:{}, photo:{}},
  album: 0,
  albums: {},
  mine: false,
  photo_id: 0,
  photos: [],
  photo_index: 0,
  comment_photo_index: 0,

  form_id: 'form-upload-page-album',
  form_edit_id: 'albums_edit',
  container_id: 'page_album_container',
  loader_id: 'pagealbum_loader',
  comment_options: '.comments_options a',
  comment_form: 'album-comment-form',
  count_span: '.tab_layout_pagealbum_profile_album a span',
  album_tab: '.tab_layout_pagealbum_profile_album a',
  album_tab_li: '.more_tab li.tab_layout_pagealbum_profile_album',
  album_select_id: 'album',
  photo_manage_form_id: 'page-album-photo-manage',
  load_photo_comment_id: 'load_photo_comments',
  photo_thumbs: '.ad-thumbs ul.ad-thumb-list li a.thumbs_photo',
  comments_container_id: 'photo_comments_container',
  photo_comment_prefix: 'photo_comments_',
  photo_comment_class: 'photo_comments',
  photo_comments: {},

  $form: {edit:{}, create:{}},
  $list: {},
  $comments_container: {},
  $container: {},
  $loader: '',
  $album_select: {},
  $photo_manage_form: {},
  comment_urls: {},

  tips_options: null,

  init: function() {
    $jq = jQuery.noConflict();
    var self = this;

    if ($(this.photo_manage_form_id)) {
      this.$photo_manage_form = $(this.photo_manage_form_id);

      this.$photo_manage_form.addEvent('submit', function(e) {
        e.stop();
        self.edit_photos(this);
      });
    }

    if ($(this.comments_container_id)) {
      this.$comments_container = $(this.comments_container_id);
    }

    if ($(this.form_id)) {
      this.$form.create = $(this.form_id);
    }

    if ($(this.album_select_id)) {
      this.$album_select = $(this.album_select_id);
    }

    if ($(this.loader_id)) {
      this.$loader = $(this.loader_id);
    }

    if ($(this.container_id)) {
      this.$container = $(this.container_id);
    }

    if ($(this.form_edit_id)) {
      this.$form.edit = $(this.form_edit_id);
    }
  },

  load_photo_comments: function(photo_id) {
    var self = this;
    var data = {};
    var index = this.photo_index;
    var html = this.photo_comments['comments_' + this.photo_index];

    $(this.load_photo_comment_id).addClass('hidden');

    if (html) {
      var options = {
        'container' : 'photo_comments_container',
        'html' : html,
        'url' : self.comment_urls
      };
      pageAlbumPhotoLikeTips = new LikeTips('pagealbumphoto', photo_id, options);
      pageAlbumPhotoLikeTips.container.removeClass('hidden');
      return ;
    }

    data.page_id = this.page_id;
    data.album = this.album;
    data.photo_id = photo_id;
    data.format = 'json';

    var request = this.request(data, this.url.photo.comments);

    request.onSuccess = function(response) {
      self.hide_album_form(self.$form.create);
      self.hide_album_form(self.$form.edit);
      self.photo_comments['comments_'+index] = response.likeHtml;
      self.hide_loader();
      self.load_photo_comments(photo_id);
    };

    this.show_loader();
    request.send();
  },

  init_forms: function() {
    var self = this;

    if (this.$form.create) {
      this.$form.create.addEvent('submit', function(e){
        e.stop();
        self.post(this);
      });
    }

    if (this.$form.edit) {
      this.$form.edit.addEvent('submit', function(e){
        e.stop();
        self.save(this);
      });
    }
  },

  init_album: function() {
    if($$(this.album_tab)[0])
      tabContainerSwitch($$(this.album_tab)[0], 'generic_layout_container layout_pagealbum_profile_album');
    else if($$(this.album_tab_li)[0])
      tabContainerSwitch($$(this.album_tab_li)[0], 'generic_layout_container layout_pagealbum_profile_album');
  },

  set_page: function(page) {
    this.page_num = page;
    if (this.mine){
      this.manage();
    }else{
      this.list();
    }
  },

  hide_album_form: function($form) {
    if ($form){
      this.add_class($form, 'hidden');
    }
    this.$container.removeClass('hidden');
  },

  display_album_form: function($form) {
    $form.removeClass('hidden');
    this.add_class(this.$container, 'hidden');
  },

  add_class: function($element, css_class) {
    if ($element.hasClass(css_class)){
      return ;
    }
    $element.addClass(css_class);
    return ;
  },

  inc_count: function(count) {
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

  list: function() {
    var self = this;

    if (this.mine){
      this.page_num = 1;
    }

    this.mine = false;
    var data = {'page_id':self.page_id, 'format':'json', 'p':self.page_num, 'ipp': self.ipp};
    var request = this.request(data, this.url.album.list);
    this.album = 0;
    this.photo_id = 0;
    this.show_loader();

    request.send();
  },

  manage: function() {
    var self = this;

    if (!this.mine){
      this.page_num = 1;
    }

    this.mine = true;

    var data = {'page_id':self.page_id, 'format':'json', 'p':self.page_num};
    var request = this.request(data, this.url.album.manage);
    this.album = 0;
    this.photo_id = 0;

    this.show_loader();
    request.send();
  },

  view: function(album_id,photo) {

    var self = this;
      if(photo==null){
          photo=0;
      }else{
          self.photo_id= photo;
      }
    var data = {'page_id':self.page_id, 'album': album_id, 'format':'json', 'photo_id': self.photo_id};
    var request = this.request(data, this.url.album.view);

    this.album = album_id;
    this.photo_comments = {};
    this.photos = [];

    request.onSuccess = function(response) {
      self.hide_album_form(self.$form.create);
      self.hide_album_form(self.$form.edit);
      self.$container.innerHTML = response.html;

      if (response.eval) {
        eval(response.eval);
      }

      self.init();

      $$(self.$container.getElements('script')).each(function($script){
        eval($script.innerHTML);
      });

      if (parseInt($$('.ad-slideshow-start'))) {
        $$('.ad-slideshow-start')[0].addEvent('click', function(){
          self.$comments_container.addClass('hidden');
          $(self.load_photo_comment_id).addClass('hidden');
        });
      }

     if ( parseInt( $$('.ad-slideshow-stop') ) ) {
        $$('.ad-slideshow-stop')[0].addEvent('click', function(){
          $(self.load_photo_comment_id).removeClass('hidden');
        });
      }

      if ($$(self.photo_thumbs)) {
        var $links = $$(self.photo_thumbs);
        $links.addEvent('click', function(){
          self.$comments_container.addClass('hidden');
          self.photo_index = $links.indexOf(this);
          $(self.load_photo_comment_id).removeClass('hidden');
        });
      }

      if (self.photo_id && response.startIndex) {
        galleries[0].showImage(response.startIndex, function(){});
      }

      if ($(self.load_photo_comment_id)) {
        $(self.load_photo_comment_id).addEvent('click', function(){
          self.photo_index = galleries[0].current_index;
          self.load_photo_comments(self.photos[galleries[0].current_index]);
        });
      }

      self.comment_urls = {
        'like' : response.likeUrl,
        'unlike' : response.unlikeUrl,
        'hint' : response.hintUrl,
        'showLikes' : response.showLikesUrl,
        'postComment' : response.postCommentUrl
      };

      var options = {
        'container' : 'photo_comments_container',
        'html' : response.likeHtml,
        'url' : self.comment_urls
      };

      pageAlbumPhotoLikeTips = new LikeTips('pagealbumphoto', response.photo_id, options);

      if(!$$('.with_photoviewer').length){
          pageAlbumPhotoLikeTips.container.removeClass('hidden');
      }

      self.hide_loader();
    };

    this.show_loader();
    request.send();
  },

  get_form_data: function($form){
    var data = {};

    if ($form.title) data.title = $form.title.value;
    if ($form.description) data.description = $form.description.value;
    if ($form.file) data.file = $form.file.value;
    if ($form.album) data.album = $form.album.value;
    if ($form.tags) data.tags = $form.tags.value;

    return data;
  },

  reset_create_from: function() {
    this.$form.create.reset();
    this.reset_files();
  },

  post: function($form) {
    var self = this;
    var data = this.get_form_data($form);

    if (this.album){
      data.album = this.album;
    }

    data.format = 'json';
    data.page_id = this.page_id;
    var request = this.request(data, $form.action);

    request.onSuccess = function(response){
      self.hide_album_form(self.$form.create);
      self.hide_album_form(self.$form.edit);
      self.$container.innerHTML = response.html;
      self.hide_loader();
      self.init();

      if (data.album == 0){
        var album = {'title':response.title, 'description':response.description};
        self.albums[response.album] = album;
        /*
         self.albums[response.album].title = response.title;
         self.albums[response.album].description = response.description;
         */
        self.add_option(self.$album_select, response.title, response.album);
      }

      if (response.eval){
        eval(response.eval);
      }
    };

    this.show_loader();
    request.send();

    return false;
  },

  manage_photos: function(album_id){
    this.album = album_id;
    var data = {};
    data.page_id = this.page_id;
    data.album = this.album;
    data.format = 'json';

    var request = this.request(data, this.url.photo.manage);

    this.show_loader();
    request.send();
  },

  edit_photos: function($form){
      var resultArray = {};
      var datasArray = $form.toQueryString().split('&');
      for (var i =0; i < datasArray.length; i++) {
          var check = datasArray[i].split('=');
          if (check[1] != '') {
              resultArray[check[0]] = check[1];
          }
      }
    var data = resultArray;

    data.page_id = this.page_id;
    data.album = this.album;
    data.format = 'json';

    var url = this.url.photo.edit;

    var request = this.request(data, url);

    this.show_loader();
    request.send();
  },

  reset_files: function(){
    var fileids = document.getElementById('fancyuploadfileids');
    fileids.value = "";
    $('demo-clear').setStyle('display', 'none');
    $('demo-list').setStyle('display', 'none');
    $$('#demo-list li').dispose();
  },

  create: function(album_id){
    this.display_album_form(this.$form.create);
    this.$form.create.reset();
    this.reset_files();
    this.$form.create.action = this.url.album.create;
    this.album = parseInt(album_id);

    if (this.album){
      this.$album_select.value = this.album;
      this.$form.create.description.value = this.albums[this.album].description;
      this.$form.create.title.value = this.albums[this.album].title;
    }

    this.$form.edit.reset();

    if (!this.$form.edit.hasClass('hidden')){
      this.$form.edit.addClass('hidden');
    }

    updateTextFields();
  },

  delete_album: function(album_id){
    var self = this;
    var he_title = en4.core.language.translate('Delete Album');
    var he_description = en4.core.language.translate('Are you sure you want to delete this album?');

    he_show_confirm(he_title, he_description, function(){
      var data = {'page_id':self.page_id, 'album':album_id, 'format':'json'};
      var request = self.request(data, self.url.album.delete_url);

      self.show_loader();
      request.send();

      self.remove_option(self.$album_select, album_id);
    });
  },

  add_option: function($select, label, value){
    $select = $($select);
    var $option = new Element('option', {'value':value, 'html':label});
    $select.appendChild($option);
  },

  remove_option: function($select, value){
    if ($select.getElement('option[value=' + value + ']')) $($select.getElement('option[value=' + value + ']')).dispose();
  },

  edit_option: function($select, value, label){
    if ($select.getElement('option[value=' + value + ']')) $($select.getElement('option[value=' + value + ']')).set('hmtl', label);
  },

  save: function($form){
    var data = this.get_form_data($form);

    data.page_id = this.page_id;
    data.album = this.album;
    data.format = 'json';

    var request = this.request(data, this.url.album.save);

    this.show_loader();
    request.send();

    this.edit_option(this.$album_select, data.album, data.title);
  },

  edit: function(album_id){
    var self = this;
    var data = {'page_id':self.page_id, 'album':album_id, 'format':'json'};
    var request = this.request(data, this.url.album.edit);

    this.$form.edit.action = this.url.album.save;
    this.album = album_id;

    request.onSuccess = function(response){
      self.hide_loader();
      if (response.error){
        self.$container.innerHTML = response.html;
        if (response.eval){
          eval(response.eval);
        }
      }else{
        self.fill_form(response.album);
      }
    };

    this.show_loader();
    request.send();
  },

  fill_form: function(album){
    if (!album){
      return false;
    }

    this.$form.edit.title.value = album.title;
    this.$form.edit.description.value = album.description;
    this.$form.edit.album.value = album.album;
    this.$form.edit.tags.value = album.tags;

    this.add_class(this.$container, 'hidden');
    this.$form.edit.removeClass('hidden');

    return true;
  },

  show_loader: function(){
    if (this.$loader) this.$loader.removeClass('hidden');
  },

  hide_loader: function(){
    if (this.$loader) this.$loader.addClass('hidden');
  },

  request: function(data, ajax_url){
    var self = this;
    data.no_cache = Math.random();
    return new Request.JSON({
      'url': ajax_url,
      'method': 'post',
      'data': data,
      onSuccess: function(response){
        self.hide_album_form(self.$form.create);
        self.hide_album_form(self.$form.edit);
        self.$container.innerHTML = response.html;
        if (response.eval){
          eval(response.eval);
        }
        self.hide_loader();
        self.init();
        $$(self.$container.getElements('script')).each(function($script){
          eval($script.innerHTML);
        });
      }
    });
  }
};