
/* $Id: Pagediscussion.js 2010-05-25 01:44 michael $ */

var Pagediscussion =
{
  page_id: 0,
  ipp: 10,
  url: {},
  topic_list: {},
  post_list: {},
  timeOut: 300,

  init: function ()
  {
    this.$loader = $('pagediscussion_loader');
    this.$elm = $('pagediscussion');

    // Custom Reset
    this.$elm.getElements('form').addEvent('reset', function ()
    {
      var $input = $(this).getElements('input[type=text]');
      if ($input){ $input.set('value', ''); }
      var $textarea = $(this).getElements('textarea');
      if ($textarea){ $textarea.set('value', ''); }
      var $checkbox = $(this).getElements('input[type=checkbox]');
      if ($checkbox){ $checkbox.set('checked', true); }
      return false;
    });

  },

  setCount: function (count)
  {
    var $counter = $$('.tab_layout_pagediscussion_profile_discussion a span')[0];
    if ($counter) {
      var countvar = (count) ? '('+count+')' : '';
      $counter.set('html', countvar);
    }
  },

  loadTab: function (element)
  {
    var $tab = this.$elm.getElements(element);
    if ($tab) {
      this.$elm.getElements('.tab').addClass('hidden');
      $tab.removeClass('hidden');
    }
    return $tab;
  },

  goTopic: function ()
  {
    this.loadTab('.tab_topic');
  },

  init_discussions : function(){
    if($$('.tab_layout_pagediscussion_profile_discussion a')[0])
      tabContainerSwitch($$('.tab_layout_pagediscussion_profile_discussion a')[0], $$('layout_pagediscussion_profile_discussion'));
    else if($$('.more_tab li.tab_layout_pagediscussion_profile_discussion')[0])
      tabContainerSwitch($$('.more_tab .tab_layout_pagediscussion_profile_discussion')[0], $$('layout_pagediscussion_profile_discussion'));

  },

  goDiscussionTab: function (topic_id, post_id)
  {
    if($$('.tab_layout_pagediscussion_profile_discussion a')[0])
      tabContainerSwitch($$('.tab_layout_pagediscussion_profile_discussion a')[0], $$('layout_pagediscussion_profile_discussion'));
    else if($$('.more_tab li.tab_layout_pagediscussion_profile_discussion')[0])
      tabContainerSwitch($$('.more_tab .tab_layout_pagediscussion_profile_discussion')[0], $$('layout_pagediscussion_profile_discussion'));

    this.topic(topic_id, null, post_id);
  },

  showMessage: function (html)
  {
    var $tab = this.loadTab('.tab_message');
    if ($tab) {
      $tab.set('html', html);
    }
  },

  doCreate: function (form)
  {
    var self = this;
    self.request(self.url.create, $(form).toQueryString(), function (obj) {
      if (obj.html)
      {
        self.showMessage(obj.html);
        if (obj.result) {
          self.$elm.getElement('.tab_create form').reset();
          setTimeout(function () { self.topic(obj.topic_id, obj.post_id); }, self.timeOut);
        }
      }
    });
    return false;
  },

  doPost: function (form)
  {
    var self = this;
    this.request(self.url.post, $(form).toQueryString(), function (obj) {
      if (obj.html)
      {
        self.showMessage(obj.html);
        if (obj.result) {
          self.$elm.getElement('.tab_post form').reset();
          setTimeout(function () { self.topic(obj.topic_id, obj.post_id); }, self.timeOut);
        }
      }
    });
    return false;
  },

  doRename: function (form)
  {
    var self = this;
    self.request(self.url.rename, $(form).toQueryString(), function (obj) {
      if (obj.html)
      {
        self.showMessage(obj.html);
        if (obj.result) {
          self.$elm.getElement('.tab_rename form').reset();
          setTimeout(function () { self.topic(obj.topic_id); }, self.timeOut);
        }
      }
    });
    return false;
  },

  doEdit: function (form)
  {
    var self = this;
    self.request(self.url.edit, $(form).toQueryString(), function (obj) {
      if (obj.html)
      {
        self.showMessage(obj.html);
        if (obj.result) {
          self.$elm.getElement('.tab_edit form').reset();
          setTimeout(function () { self.topic(obj.topic_id, obj.post_id); }, self.timeOut);
        }
      }
    });
    return false;
  },

  create: function ()
  {
    this.loadTab('.tab_create');
  },

  post: function (topic_id, body)
  {
    var $tab = this.loadTab('.tab_post');
    if ($tab) {
      var $topic_id = $tab.getElement('input[name=topic_id]');
      if ($topic_id) { $topic_id.set('value', topic_id); }
      if (body) {
        var $body = $tab.getElement('textarea[name=body]');
        if ($body) {
          $body.set('value', body);
        }
      }
    }
  },

  rename: function (topic_id)
  {
    var $tab = this.loadTab('.tab_rename');
    if ($tab) {
      var topic = this.topic_list[topic_id];
      if (topic) {
        var $title = $tab.getElement('input[name=title]');
        if ($title) { $title.set('value', topic) }
        var $topic_id = $tab.getElement('input[name=topic_id]');
        if ($topic_id) { $topic_id.set('value', topic_id); }
      }
    }
  },

  edit: function (post_id)
  {
    var $tab = this.loadTab('.tab_edit');
    if ($tab) {
      var post = this.post_list[post_id];
      if (post) {
        var $body = $tab.getElement('textarea[name=body]');
        if ($body) { $body.set('value', post) }
        var $post_id = $tab.getElement('input[name=post_id]');
        if ($post_id) { $post_id.set('value', post_id); }
      }
    }
  },

  discussion: function (task, id, checked)
  {
    var self = this;

    if (task != 'watch' && task != 'close' && task != 'sticky' && task != 'deletetopic' && task != 'deletepost') {
      return ;
    }
    var title = en4.core.language.translate('PAGEDISCUSSION_' + task.toUpperCase() + '_TITLE');
    var description = en4.core.language.translate('PAGEDISCUSSION_' + task.toUpperCase() + '_DESCRIPTION');

    var param = {};
    param.task = task;
    param.set = (checked) ? 1 : 0;
    if (task == 'deletepost') {
      param.post_id = id;
    } else {
      param.topic_id = id;
    }

    var callback = function ()
    {
       self.request(self.url.discussion, param, function (obj) {

        if (obj.html) {
          self.showMessage(obj.html);
        }
        if (obj.result) { setTimeout(function ()
        {
          if (task == 'deletetopic' || (task == 'deletepost' && !obj.topic_id)) {
            self.list();
          } else {
            self.topic(obj.topic_id);
          }
        }, self.timeOut); }
      });
    };

    if (task == 'deletetopic' || task == 'deletepost') {
      he_show_confirm(title, description, callback);
    } else {
      callback();
    }

  },

  list: function (page)
  {
    if (!page) { page = 1; }
    var self = this;
    var $tab = self.loadTab('.tab_list');
    if ($tab) {
      self.request(self.url.list, {page: page, ipp: self.ipp}, function (obj) {
        if (obj.html) {
          $tab.set('html', obj.html);
          self.setCount(obj.count);
          self.topic_list = obj.topic_list;
        }
      });
    }
  },

  topic: function (topic_id, page, post_id)
  {
    if (!page) { page = 1; }
    if (!post_id) { post_id = 0; }

    var self = this;
    var $tab = self.loadTab('.tab_topic');
    if ($tab) {
      self.request(self.url.topic, {'topic_id': topic_id, 'page': page, 'post_id': post_id}, function (obj) {
        if (obj.html) {
          $tab.set('html', obj.html);
          self.post_list = obj.post_list;

          // Scroll to Post
          if ($('post_'+post_id)) {
            new Fx.Scroll(window).toElement('post_'+post_id);
          }
        }
      });
    }
  },

  quote: function (topic_id, user, href, element) {

    var $element = $(element);
    if ($element) {
      var $body = $element.getParent('li').getElement('.body_raw');
      if ($body) {
        var quote = "[blockquote][b][url="+ href +"]"+ user +"[/url][/b]\n"
            + htmlspecialchars_decode($body.get('html').trim())
            + "[/blockquote]\n\n";
        this.post(topic_id, quote);
      }
    }
  },

  request: function (url, data, callback)
  {
    var self = this;

    if (typeof(data) == 'string') {
      data += '&format=json&page_id=' + self.page_id;
    } else if (typeof(data) == 'object') {
      data.format = 'json';
      data.page_id = self.page_id;
    }

    self.$loader.removeClass('hidden');
    self.$elm.setStyle('display', 'none');
    var request = new Request.JSON({
      secure: false,
      url: url,
      method: 'post',
      data: data,
      onSuccess: function(obj) {
        self.$loader.addClass('hidden');
        self.$elm.setStyle('display', 'block');
        if (callback) { callback(obj); }
      }
    }).send();
  }

};