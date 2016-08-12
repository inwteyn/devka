var AdvNotificationUpdateHandler = new Class({

  Implements: [Events, Options],
  options: {
    debug: false,
    baseUrl: '/',
    identity: false,
    delay: 30000,
    minDelay: 5000,
    maxDelay: 600000,
    delayFactor: 1.5,
    admin: false,
    idleTimeout: 600000,
    last_id: 0,
    subject_guid: null
  },

  state: true,

  activestate: 1,

  fresh: true,

  lastEventTime: false,

  title: document.title,

  initialize: function (options) {
    this.setOptions(options);
    this.options.minDelay = this.options.delay;
  },

  start: function () {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout: this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive': function () {
        this.activestate = 1;
        this.state = true;
      }.bind(this),
      'onStateIdle': function () {
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });

    this.loop();
  },

  stop: function () {
    this.state = false;
  },

  updateNotifications: function () {
    if (en4.core.request.isRequestActive()) return;
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'advnotifications/index/update',
      data: {
        format: 'json'
      },
      onSuccess: this.showNotifications.bind(this)
    }));
  },

  advMessages:false,
  convId:0,
  showNotifications: function (response) {
    var self = this;
    if(response.status) {
      $$('.advnotification-wrapper').each(function(el, i){el.remove();});

      if(response.html) {
        var $html = new Element('div', {'class' : 'advnotification-wrapper'});
        $html.set('html', response.html);

        $html.getElements('a').each(function(e, i) {
          e.set('href', 'javascript://');
          e.set('class', 'advnotification-wrapper-fake');
        });

        $('global_content').grab($html, 'top');


  if($$(".nContentlink")){

      $html.addEvent('click', function(e) {
          if(e.target.get('id') == 'advnotification-close') {
              $html.remove();
          }
         if(e.target.get('id')=='link_ok'){
            self.accept('confirm',$$('#link_ok').get('data-id')[0],$$('#link_ok').get('data-notif')[0]);
         }
          if(e.target.get('id')=='link_not_ok'){
            self.accept('reject',$$('#link_ok').get('data-id')[0],$$('#link_ok').get('data-notif')[0]);
         }
      });
  }else{
    $html.addEvent('click', function(e) {
     if(e.target.get('id') == 'advnotification-close') {
        $html.remove();
      } else {
        if(response.advMessages && response.id) {
          self.advMessages = response.advMessages;
          self.convId = response.id;
        }
        self.markRead(response.action_id, response.href);
      }
    });
  }

      }
    }
  },

  accept : function(action, user_id, notification_id)
  {
      var url;
      if( action == 'confirm' )
      {
          url = en4.core.baseUrl +'members/friends/confirm';
      }
      else if( action == 'reject' )
      {
          url = en4.core.baseUrl +'members/friends/reject';
      }
      else
      {
          return false;
      }

      (new Request.JSON({
          'url' : url,
          'data' : {
              'user_id' : user_id,
              'format' : 'json'
          },
          'onSuccess' : function(responseJSON)
          {
              $$('.advnotification-wrapper > .content').set('html',responseJSON.message);
              $$('.nContentlink').hide();
          }
      })).send();
  },


  markRead : function (action_id, href){
    var self = this;
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'activity/notifications/markread',
      data : {
        format     : 'json',
        'actionid' : action_id
      },
      onSuccess: function (response) {
        if(self.advMessages && self.convId) {
          if($('headvmessages-wrapper')) {
            $('headvmessages-wrapper').setStyle('display', 'block');
            //headvmessagesCore.openConversation(self.convId);
            headvmessagesCore.openConversationFromNotification(self.convId);
          }
        } else {
          location.href = href;
        }
      },
      onError: function (response) {
      },
      onFailure: function (response) {
      }
    }));
  },

  loop: function () {
    if (!this.state) {
      this.loop.delay(this.options.delay, this);
      return;
    }

    try {
      this.updateNotifications().addEvent('complete', function () {
        this.loop.delay(this.options.delay, this);
      }.bind(this));
    } catch (e) {
      this.loop.delay(this.options.delay, this);
      this._log(e);
    }
  },

  // Utility

  _log: function (object) {
    if (!this.options.debug) {
      return;
    }

    // Firefox is dumb and causes problems sometimes with console
    try {
      if (typeof(console) && $type(console)) {
        console.log(object);
      }
    } catch (e) {
      // Silence
    }
  }
});