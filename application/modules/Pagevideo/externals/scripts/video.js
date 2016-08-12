/* $Id: video.js 2010-09-20 15:57 idris $ */

var page_video = {
  
  page_id: 0,
  video_id: 0,
  flowplayer_url: en4.core.baseUrl + "externals/flowplayer/flowplayer-3.2.18.swf",
  page_num: 1,
  files: null,
  
  allowed_post: 0,
  allowed_comment: 0,
  mine: false,
  
  url:{},
  
  form:{edit:'video_edit', create:'form-video-upload'}, 
  $form: {edit:{}, create:{}},
  
  hide_class: 'hidden',
  
  container: 'page_video_main_container',
  $container: {},
  
  loader: 'pagevideo_loader',
  $loader: {},
  
  tips: '.tip',
  $tips: {},
  
  count_span: '.tab_layout_pagevideo_profile_video a span',
  
  video_tab: '.tab_layout_pagevideo_profile_video a',
  video_tab_li: '.more_tab li.tab_layout_pagevideo_profile_video',

  comment: {form:'video-comment-form', options: '.comments_options a', element: 'comments_pagevideo'},
  
  init: function(){
    $('page_video_form_errors').addClass('hidden');

    var self = this;

    if ($(this.form.edit)){
      this.$form.edit = $(this.form.edit); 
    }
    
    if ($(this.form.create)){
      this.$form.create = $(this.form.create); 
    }
    
    if ($(this.container)){
      this.$container = $(this.container); 
    }
    
    if ($(this.loader)){
      this.$loader = $(this.loader); 
    }
    
    if ($$(this.tips)){
      this.$tips = $$(this.tips); 
    }
    
    if ($type(this.$form.create) == 'element'){
      this.$form.create.addEvent('submit', function(e){
        e.stop();
        self.post();
      });
    }
    
    if ($type(this.$form.edit) == 'element'){
      this.$form.edit.addEvent('submit', function(e){
        e.stop();
        self.save();
      });
    }
    
    animate_thumbs();
  },

	get_form_values: function($form){
		var data = {};

		$$($form.elements).each(function($item){
				data[$item.name] = $item.value;
		});
		return data;
	},
  
  init_video: function(){
    if( $$(this.video_tab)[0] )
      tabContainerSwitch($$(this.video_tab)[0], 'generic_layout_container layout_pagevideo_profile_video');
    if( $$(this.video_tab_li)[0] )
      tabContainerSwitch($$(this.video_tab_li)[0], 'generic_layout_container layout_pagevideo_profile_video');
  },
    
  set: {
    page: function(page){
      page_video.page_num = page;
      if (page_video.mine){
        page_video.manage();
      }else{
        page_video.all();
      }
    },
    container:{
      html: function(html){
        page_video.$container.innerHTML = html;
      }
    }
  },

  flashembed: function(container_id, url, duration){
    flashembed(container_id,
      {
        src: page_video.flowplayer_url,
        width: 480,
        height: 386,
        wmode: 'transparent'
      },
      {
        config:
        {
          clip:
          {
            url: url,
            autoPlay: false,
            duration: duration,
            autoBuffering: true
          },
          plugins:
          {
            controls:
            {
              background: '#ffffff',
              bufferColor: '#333333',
              progressColor: '#444444',
              buttonColor: '#444444',
              buttonOverColor: '#666666'
            }
          },
          canvas:
          {
            backgroundColor:'#000000'
          }
        }
      });
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
  
  hide:{
    form:{
      edit: function(){
        if ($type(page_video.$form.edit) != 'element'){
          return ;
        }
        page_video.$form.edit.reset();
        addClass(page_video.$form.edit, page_video.hide_class);
      },
      create: function(){
        if ($type(page_video.$form.create) != 'element'){
          return ;
        }
        page_video.$form.create.reset();
        addClass(page_video.$form.create, 'hidden');
        page_video.$form.create.setStyle('display', 'none');
        $('video_type-wrapper').setStyle('display', 'block'); // @todo some part of not so fancy FancyUpload
        $('video-demo-list').setStyle('display', 'none');
        $$('#video-demo-list li').each(function($item){
          $item.dispose();
        });
      }
    },
    container: function(){
      addClass(page_video.$container, page_video.hide_class);
    },
    loader: function(){
      addClass(page_video.$loader, page_video.hide_class);
    }
  },
  
  show:{
    form:{
      edit: function(){
        if ($type(page_video.$form.edit) != 'element'){
          return ;
        }
        page_video.$form.edit.reset();
        page_video.$form.edit.removeClass(page_video.hide_class);
      },
      create: function(){
        if ($type(page_video.$form.create) != 'element'){
          return ;
        }
        page_video.$form.create.reset();
        page_video.$form.create.setStyle('display', 'block');
        page_video.$form.create.removeClass(page_video.hide_class);
        updateVideoFields();
      }
    },
    container: function(){
      page_video.$container.removeClass(page_video.hide_class);
    },
    loader: function(){
      page_video.$loader.removeClass(page_video.hide_class);
    }
  },
      
  create: function(){
    $('page_video_form_errors').addClass('hidden');
    this.hide.container();
    this.hide.form.edit();
    this.show.form.create();
  },
  
  edit: function(video_id){
    var self = this;
    this.video_id = video_id;
    var url = this.url.edit;
    var data = {'video_id':video_id};
    var request = this.request(url, data);
    request.onSuccess = function(response){
      self.hide.container();
      self.show.form.edit();
      self.populate(response);
      self.hide.loader();
    };
    
    this.show.loader();
    request.send();
  },
  
  populate: function(data){
    this.$form.edit = $(this.form.edit);
    this.$form.edit.video_title.value = data.video.title;
    this.$form.edit.video_description.value = data.video.description;
    this.$form.edit.video_tags.value = data.tags;
  },
  
  confirm: function(video_id){
    var self = this;
    var callback = function(){
      self.remove(video_id);
    };
    he_show_confirm('Delete Video', 'Are you sure you want to delete this video?', callback, {confirm_label:'Delete Video', cancel_label:'Cancel', or_label:'or'});
  },
  
  remove: function(video_id){
    var url = this.url.delete_url;
    var data = {'video_id':video_id};
    var request = this.request(url, data);

    this.show.loader();
    request.send();
  },
  
  post: function() {
    if( !$(this.$form.create).video_title.value ){
      $('page_video_form_errors').removeClass('hidden');
      return;
    } else
      $('page_video_form_errors').addClass('hidden');

    var self = this;
    var url = this.url.create;
    var request = this.request(url + '?' + $(this.$form.create).toQueryString());

    request.onSuccess = function(response){
      self.files[response.video_id] = {};
      self.files[response.video_id] = response.video;
      self.handle.response(response);
    };
    
    this.show.loader();
    request.send();
  },
  
  save: function(){
    var self = this;
    var url = this.url.save + '?' + this.$form.edit.toQueryString();
    var data = {'video_id':this.video_id};
    var request = this.request(url, data);
    request.onSuccess = function(response){
      self.files[self.video_id].title = self.$form.edit.video_title.value;
      self.files[self.video_id].description = self.$form.edit.video_description.value;
      self.handle.response(response);
    };
    this.show.loader();
    request.send();
  },
  
  manage: function(){
    $('page_video_form_errors').addClass('hidden');

    if (!this.mine){
      this.page_num = 1;
      this.mine = true;
    }
    
    var request = this.request(this.url.manage);
    
    this.show.loader();
    request.send();
  },
  
  all: function(){

    $('page_video_form_errors').addClass('hidden');

    var self = this;
    
    if (this.mine){
      this.page_num = 1;
      this.mine = false;
    }
    
    var request = this.request(this.url.index);
    
    request.onSuccess = function(response){
      self.hide.form.create();
      self.hide.form.edit();
      self.set.container.html(response.html);
      self.$container = $(self.container);
      self.show.container();
      self.files = response.files;
      
      if ($$(self.count_span)[0]){
        $$(self.count_span)[0].innerHTML = "("+response.count+")";
      }
      
      animate_thumbs();
      self.hide.loader();
    };
    
    this.show.loader();
    request.send();
  },
  
  view: function(video_id){
    $('page_video_form_errors').addClass('hidden');
    var self = this;

    var video = this.files[video_id] ? this.files[video_id] : false;

    if (!video){
      alert('No video data.');
      return ;
    }

    var $container = new Element('div', {'class':'smoothbox_video', 'id':'smoothbox_pagevideo_container'});
    var comment_count =  video.comment_count.toInt();
    var $comment_link = new Element('a', {'href':'javascript:page_video.view_comments('+video_id+')', 'class':'pagevideo_comments_link', 'html':'View Comments('+comment_count+')'});
    
    var $loader = new Element('div', {'class':'video_loader'});
    $loader.innerHTML = '<img src="' + en4.core.baseUrl + 'application/modules/Pagevideo/externals/images/loader.gif" />';
    
    if (video.type == 3){
      $loader.addClass('hidden');
    }
    
    var $wrapper = new Element('div', {'class':'pagevideo_wrapper'});
    var $video = new Element('div', {'id':'pagevideo_'+video_id, 'class':'pagevideos'});
    
    var $details = new Element('div', {'class':'pagevideo_details'});
    
    var $description = new Element('div', {'class':'pagevideo_description'});
    $description.innerHTML = video.description;
        
    var $clr = new Element('div', {'class':'clr'});
    
    $details.appendChild($description);
    
    $wrapper.appendChild($video);
    $wrapper.setStyle('width', video.width);
    $wrapper.setStyle('height', video.height);
    
    $container.appendChild($loader);
    $container.appendChild($wrapper);
    $container.appendChild($details);
    $container.appendChild($comment_link);
    $container.appendChild($clr);
    
    Smoothbox.open($container, {mode: 'Inline', width: video.width + 25, height: video.height});
    $$('.pagevideos')[0].setProperty('id', 'pagevideo_' + video_id);
        
    if (video.type == 3){
      this.flashembed('pagevideo_' + video_id, video.url, video.duration);
    }else{
      this.embed(video_id, video.player, video.url, video.width, video.height);
    }
  },
  
  embed: function(video_id, player, url, width, height){
    var self = this;
    swfobject.embedSWF(
      player,
      "pagevideo_"+video_id,
      width,
      height,
      "9.0.0",
      "expressInstall.swf",
      {
        "data-file": url
      },
      null,
      null,
      function(){
        $$('.video_loader')[0].addClass(self.hide_class);
      }
    );
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
  
  view_comments: function(video_id) {
    Smoothbox.close();
    var self = this;
    var request = this.request(this.url.view, {'video_id':video_id});
    request.onSuccess = function(response) {
      self.handle.response(response);
      self.init_comments();
      
      if (self.files[video_id].type == 3) {
        self.flashembed('pagevideo_embed', self.files[video_id].url, 560);
      }

			var options = {
				'container' : 'pagevideo_comments',
				'html' : response.likeHtml,
				'url' : {
					'like' : response.likeUrl,
					'unlike' : response.unlikeUrl,
					'hint' : response.hintUrl,
					'showLikes' : response.showLikesUrl,
					'postComment' : response.postCommentUrl
				}
			};

			var pageVideoLikeTips = new LikeTips('pagevideo', video_id, options);
    };
    
    this.show.loader();
    request.send();
  },
    
  request: function(url, data){
    var self = this;
    
    if (!data){
      data = {};
    }
    
    data.format = 'json';
    data.page_id = this.page_id;
    data.p = this.page_num;

    return new Request.JSON({
      'url': url,
      'method': 'post',
      'data': data,
      onSuccess: function(response){
        self.handle.response(response);
      }
    });
  },
  
  handle:{
    response: function(data){
      page_video.hide.form.create();
      page_video.hide.form.edit();
      page_video.set.container.html(data.html);
      
      page_video.$container = $(page_video.container);
      
      page_video.show.container();
      page_video.scripts();
      
      if (data.eval){
        eval(data.eval);
      }
      
      data.html.stripScripts(true);
      en4.core.runonce.trigger();
      
      page_video.hide.loader();
    }
  },
  
  scripts: function(){
    $$(this.$container.getElements('script')).each(function($script){
      eval($script.innerHTML);
    });
  }
}

function addClass($element, classname){
  if (!$element || !classname){
    return false;
  }
  
  if ($element.length){
    $$($element).each(function($item){
      if ($item.hasClass(classname)){
        return false;
      }
      $item.addClass(classname);
    });
  };
  
  if ($element.hasClass(classname)){
    return false;
  }
  
  $element.addClass(classname);
	
  return true;
}

function print_arr(object, flag) {
  var type = typeof(object);
  var output = '';
  var property = null;

  switch (type){
    case 'object':{
      for (property in object){
        output += property + ': ' + print_arr(object[property], true)+'; ';
      }
    }
    break;
    case 'array':{
      for (var i = 0; i < object.length; i++){
        output += i + ': ' + print_arr(object[i], true)+'; ';
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

	alert(output);

  if (window.console !== undefined){
    window.console.log(output);
  }else{
    alert(output);
  }
}