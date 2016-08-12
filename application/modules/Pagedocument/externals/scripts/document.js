/* $Id: document.js 2010-08-31 16:02 idris $ */

var page_document = {
  file_info:{},
  page_id: 0,
  ipp: 10,
  page_num: 1,
  form_id: '',
  edit_form_id: '',
  list_id: '',
  container_id: '',
  options_id: '',
  $form: {},
  $editform: {},
  $list: {},
  $container: {},
  allowed_post: 0,
  allowed_comment: 0,
  block: false,
  url: {},
  document_id: 0,
  mine: false,
  comment_options: '.comments_options a',
  comment_form: 'document-comment-form',
  count_span: '.tab_layout_pagedocument_profile_document a span',
  document_tab: '.tab_layout_pagedocument_profile_document a',
  document_tab_li: '.more_tab .tab_layout_pagedocument_profile_document',
  tabs_container_id: 'main_tabs',
  navigation_buttons_class: '#page_document_options .page_content_navigation li a',
  loader_id: 'pagedocument_loader',
  $loader: '',
  $values: {},

  init: function(){
    var self = this;
    if (this.allowed_post){
      this.$form = $(this.form_id);
      this.$editform = $(this.edit_form_id);

      if(this.$form){
      en4.core.runonce.trigger();
      this.$form.addEvent('submit', function(e){
          e.stop();
          self.create_document(this);
        });
      }
      if(this.$editform){
        en4.core.runonce.trigger();
        this.$editform.addEvent('submit', function(e){
            e.stop();
            self.edit_document();
          });
      }
    }

    this.$loader = $(this.loader_id);
    this.$container = $(this.container_id);
  },

  init_document: function(){
    if($$(this.document_tab)[0])
      tabContainerSwitch($$(this.document_tab)[0], 'generic_layout_container layout_pagedocument_profile_document');
    else if($$(this.document_tab_li)[0])
      tabContainerSwitch($$(this.document_tab_li)[0], 'generic_layout_container layout_pagedocument_profile_document');

  },

  set_page : function(page){
    this.page_num = page;
    if (this.mine){
      this.my_documents();
    }else{
      this.list();
    }
  },
  
  hide_document_form: function(){
    if (!this.allowed_post){
      return ;
    }
    if (this.$form){
      this.add_class(this.$form, 'hidden');
    }
    this.$container.removeClass('hidden');
  },
  
  display_document_form: function(){
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

    set_category : function(category_id, view_type) {
        if(!view_type) return;

        if (this.mine) this.page_num = 1;
    this.mine = false;
    var self = this;
    var data = {'page_id':self.page_id, 'format':'json', 'p':self.page_num, 'category_id':category_id};

        var url = '';

        view_type == 'all' ? url = this.url.list : url = this.url.my_documents;

    var request = this.request(data, url);

    self.show_loader();
    request.send();

    },






    download: function(id) {
        var self = this;
        var data = {'id':id};

        var request = this.request(data, '/page-document/download');



        self.show_loader();
        request.send();
    },







  list: function() {
    if (this.mine){
      this.page_num = 1;
    }
    this.mine = false;
    var self = this;
    var data = {'page_id':self.page_id, 'format':'json', 'p':self.page_num, 'ipp': self.ipp};

    var request = this.request(data, this.url.list);



    self.show_loader();
    request.send();
  },
  
  my_documents: function(){
    if (!this.mine){
      this.page_num = 1;
    }
    this.mine = true;
    var self = this;
    var data = {'page_id':self.page_id, 'format':'json', 'p':self.page_num};
    var request = this.request(data, this.url.my_documents);
    
    self.show_loader();
    request.send();
  },
  
  view: function(document_id) {
    var self = this;
    var data = {'page_id':self.page_id, 'document_id': document_id, 'format':'json'};
    var request = this.request(data, this.url.view);



    request.onSuccess = function(response)
    {

      self.hide_document_form();
      response.html.stripScripts(true);
      self.$container.innerHTML = response.html;
      self.hide_loader();
      self.init();
      en4.core.runonce.trigger();
      var options = {
        'container' : 'pagedocument_comments',



        'html' : response.likeHtml,
        'url' : {
          'like' : response.likeUrl,
          'unlike' : response.unlikeUrl,
          'hint' : response.hintUrl,
          'showLikes' : response.showLikesUrl,
          'postComment' : response.postCommentUrl
        }
      };
      var pageDocumentLikeTips = new LikeTips('pagedocument', document_id, options);
    }
    
    self.show_loader();
    request.send();
  },
  
  get_form_data: function($form) {
    var data = {};

    data.document_title = $('document_title').value;

    data.document_tags = $('document_tags').value;

    data.document_description = window.tinyMCE.editors.document_description.getContent();

    data.category_id = $$('#page_document_create_form #category_id')[0].value;

    /*data.download_allow = $('download_allow').value;*/
    /*if($('secure_allow-1').checked)
      data.secure_allow = 1;
    else*/
      data.secure_allow = 0;
    data.file_id = $('file_id').value;
    data.file_path = $('file_path').value;
    data.file_size = $('file_size').value;

    return data;
  },
  
  post: function($form) {
    var self = this;
    var data = this.get_form_data($form);
    if (this.document_id)
    {
      data.document_id = this.document_id;
    }

    data.format = 'json';
    data.page_id = self.page_id;
    var request = this.request(data, $form.action);

    
    self.show_loader();
    request.send();
    
    return false;
  },
  
  reset_form: function() {
    window.document_photo_up.fileList.each(function (file){
        file.remove();
    });
    this.$form.reset();
    if (window.tinyMCE.editors.document_body){
      window.tinyMCE.editors.document_body.setContent("");
    }
  },
  
  create: function() {
    var self = this;

    var data = {'page_id':self.page_id, 'format':'html', 'values':self.$values};
    self.show_loader();
    var r = new Request.HTML({
      url: this.url.get_create_form,
      method: 'post',
      data: data,
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        self.$container.innerHTML = responseHTML;
        en4.core.runonce.trigger();
        self.init();
        self.hide_loader();
        self.$form.action = self.url.create;
      }
    })
    r.post();
  },

    create_document: function($form) {
    var data = this.get_form_data($form);
    data.page_id = this.page_id;
    data.format = 'json';
    var url = this.url.create;
    var request = this.request(data, url);
    this.show_loader();
    request.send();
  },
  
  delete_document: function(document_id){
    var self = this;
    var he_title = en4.core.language.translate('pagedocument_Delete Document');
    var he_description = en4.core.language.translate('pagedocument_Delete_confirmation');

    he_show_confirm(he_title, he_description, function(){
      var data = {'page_id':self.page_id, 'document_id':document_id, 'format':'json'};
      var request = self.request(data, self.url.delete_url);

      self.show_loader();
      request.send();
    });
  },

    get_edit_data: function($form) {
    var data = {};
    data.document_id = $('document_id').value;
    data.document_title = $('document_title').value;
    data.document_tags = $('document_tags').value;
    data.document_description = window.tinyMCE.editors.document_description.getContent();
    data.category_id = $$('#page_document_create_form #category_id')[0].value;
    return data;
  },

  edit: function(document_id) {
    var self = this;
    var data = {'page_id':self.page_id, 'document_id':document_id, 'format':'html', 'values':self.$values};
    self.show_loader();
    var r = new Request.HTML({
      url: this.url.get_edit_form,
      method: 'post',
      data: data,
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        self.$container.innerHTML = responseHTML;
        en4.core.runonce.trigger();
        self.init();
        self.hide_loader();
        self.$editform.action = self.url.save;
      }
    })
    r.post();
  },

  edit_document: function(){
    var data = this.get_edit_data(this.$editform);
    data.page_id = this.page_id;
    data.format = 'json';
    var url = this.url.save;
    var request = this.request(data, url);
    this.show_loader();
    request.send();
    en4.core.runonce.trigger();
  },
  
  fill_form: function(document){
    if (!document){
      return false;
    }
    
    this.$form.document_title.value = document.title;
    this.$form.document_tags.value = document.tags;
    
    if (window.tinyMCE.editors.document_body){
      window.tinyMCE.editors.document_body.setContent(document.body);
    }
    this.$form.document_body.value = document.body;
    
    this.add_class(this.$container, 'hidden');
    this.$form.removeClass('hidden');
    
    return true;
  },
  
  show_loader: function(){
    this.$loader.removeClass('hidden');
  },
  
  hide_loader: function(){
    this.$loader.addClass('hidden');
  },
  
  request: function(data, ajax_url){
    var self = this;
    data.no_cache = Math.random();    
    return new Request.JSON({
      'url': ajax_url,
      'method': 'post',
      'data': data,
      onSuccess: function(response) {
        self.hide_document_form();
        self.$values = response.values;
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