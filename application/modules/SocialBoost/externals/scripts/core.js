var SocialBoost = {
  content:'',
  modalView:'',
  subscribeUrl:'',
  likeUrl:'',
  popupType:'',
  subscribeBtn:'',
  maxDays:'',

  showPopup:function()
  {
    console.log('showPopup');

    this.content.setStyle('display', 'block');
    this.modalView.setStyle('display', 'block');

    this.modalView.addClass('socialboost_in');
    this.content.addClass('socialboost_in');
  },

  hidePopup:function()
  {
    var self = this;
    this.content.removeClass('socialboost_in');
    this.modalView.removeClass('socialboost_in');

    (function(){
      self.modalView.setStyle('display', 'none');
      self.content.setStyle('display', 'none');
    }).delay(200);

  },

  init:function()
  {
    var lastdate = Cookie.read('en4_socialboost_lastdate', {path:en4.core.basePath});
    var currentdate = new Date();

    if( lastdate ) {
      lastdate = new Date(lastdate);
      var dateDiff = parseInt((currentdate.getTime()-lastdate.getTime())/(24*3600*1000*7));
      console.log(dateDiff);

      if( dateDiff < this.maxDays ) {
        return
      }
    }

    this.content.inject($$('body')[0]);
    this.modalView.inject($$('body')[0]);

    var self = this;
    $('socialboost_close_button').addEvent('click', function(){
      self.hidePopup();
    });

    self.subscribeBtn = $('socialboost_subscribe_btn');
console.log('wtf 1');
console.log(self.subscribeBtn);
    if( self.subscribeBtn ) {
      self.subscribeBtn.addEvent('click', function() {
        self.subscribe();
      });
    }

    Cookie.write('en4_socialboost_lastdate', currentdate, {path:en4.core.basePath});

    var tw = $('sb_twitter');
    if(tw) {
      tw.addEvent('click', function() {
        var url = 'https://twitter.com/intent/tweet?text=Some Text&url=http://wasm.ru';
        window.open(url, '', 'HEIGHT=500,WIDTH=800');
      });
    }

    var fb = $('sb_facebook');
    if(fb) {
      fb.addEvent('click', function() {
        var url = 'http://www.facebook.com/plugins/like.php?href=http://wasm.ru';
        //var url = 'https://www.facebook.com/sharer/sharer.php?u=http://wasm.ru';
        window.open(url, '', 'HEIGHT=500,WIDTH=800');
      });
    }

    var gp = $('sb_google');
    if(gp) {
      var url = 'https://twitter.com/intent/tweet?text=Some Text&url=http://wasm.ru';
      //window.open(url, '', 'HEIGHT=500,WIDTH=800');
    }

  },

  subscribe:function() {
    console.log('sub 1');
    var email = $('socialboost_email').get('value');
    $('socialboost_email').set('value', '');
    if( email.length < 1 ) {
      console.log('sub -1');
      return;
    }

    if( email.indexOf("@") == -1 || email.indexOf("@") == email.length ) {
      console.log('sub -2');
      return;
    }
    console.log('sub 3');
    var self = this;
    var request = new Request.JSON({
      url : self.subscribeUrl,
      data : {
        format : 'json',
        email : email
      },
      onComplete: function(response) {
        console.log(response);

        if( response.status ) {
          if(response.goto_url)
            window.location.href = response.goto_url;

            self.hidePopup();
        } else {
          if( response.message ) {
            alert(response.message);
          } else {
            self.hidePopup();
          }
        }
      }
    });
    request.send();
  },

  like:function(type)
  {
    var self = this;
    var request = new Request.JSON({
      url : self.likeUrl,
      data : {
        format : 'json',
        type : type
      },
      onComplete: function(response) {
        console.log(response);

        if( response.status ) {
          if(response.goto_url)
            window.location.href = response.goto_url;
          self.hidePopup();
        } else {
          if( response.message ) {
            alert(response.message);
          } else {
            self.hidePopup();
          }
        }

      }
    });
    request.send();
  }
}