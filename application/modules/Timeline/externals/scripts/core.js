/*function likeSubject(id, type) {
 console.log(type);
 }*/
document.addEvent('domready', function () {
    var scroller = $('tl-dates');
    if (scroller) {
        var left = $('global_content').getSize().x +(window.getSize().x - $('global_content').getSize().x) / 2 + 10,
            top_ab = $('global_header').getSize().y + 15;
        scroller.setStyles({
            'position': 'fixed',
            'top': top_ab + 'px',
            'left': left + 'px'
        });
    }
    setTimeout(function () {
        var scroller = $('tl-dates');
        if (scroller) {
            var left = $('global_content').getSize().x +(window.getSize().x - $('global_content').getSize().x) /2 + 10,
                top_ab = $('global_header').getSize().y + 15;
            scroller.setStyles({
                'position': 'fixed',
                'top': top_ab + 'px',
                'left': left + 'px'
            });
        }
    }, 2000);

    var new_html = $('newfeedtimeline');
    if (new_html) {
        var sethtml = new_html.get('html');
        var tab = $('#tab-timeline');
        if (tab) {
            if(tab.hasClass('page-single-content')){
                var parent_none = new_html.getParent();
                parent_none.setStyle('display','none');
              var tt = parent_none.getParent();
              if(tt){
                if(tt.get('id') && $(tt.get('id').replace('#',''))) $(tt.get('id').replace('#','')).setStyle('display', 'none');
              }
                tab.setStyle('display', 'block');
                tab.setStyle('position', 'relative');
                tab.set('html', sethtml);
                if(tab.hasClass('hide-feed-for-widget')) {
                    tab.hide();
                }
                new_html.set('html', '');
            } else {
                var parent_none = new_html.getParent();
                parent_none.setStyle('display','none');
                var tt = parent_none.getParent();
                if(tt){
                  if(tt.get('id') && $(tt.get('id').replace('#',''))) $(tt.get('id').replace('#','')).setStyle('display', 'none');
                }
                $$('.active_tabInTimeLine').setStyle('display', 'none');
                tab.setStyle('display', 'block');
                tab.setStyle('position', 'relative');
                $('time_line_widget').getParent('ul').getChildren('li').set('class','');
                $('time_line_widget').setStyle('display', 'block');
                $('time_line_widget').set('class', 'he-active');
                var ch = $('timeLine-active');
                if (ch) {
                    ch.erase('class');
                    ch.getParent().erase('class');
                }
                tab.set('html', sethtml);
                new_html.set('html', '');
            }
        }
    }

    /*  var layoutLeft = document.querySelector('.layout_left_timeline');
     if (layoutLeft) {
     var lLeftWidth = layoutLeft.offsetWidth;
     var lLeftLeft = layoutLeft.getBoundingClientRect().left;
     if (lLeftLeft <= 10) {
     setTimeout(function () {
     lLeftLeft = layoutLeft.getBoundingClientRect().left;
     }, 1000);
     setTimeout(function () {
     lLeftLeft = layoutLeft.getBoundingClientRect().left;
     }, 3000);
     }


     var layoutMiddle = document.querySelector('.layout_middle_timeline');
     var left = (layoutMiddle.offsetLeft +15 ) + 'px';
     if($('global_page_group-profile-index')){
     var left = (layoutMiddle.offsetLeft ) + 'px';
     }

     var sub = layoutLeft.offsetTop + layoutLeft.offsetHeight - layoutLeft.offsetTop - window.innerHeight;
     var fixed = false;

     var offtop = layoutLeft.offsetTop;
     var offheith = layoutLeft.offsetHeight;


     window.addEventListener('resize', function (e) {
     sub = layoutLeft.offsetTop + layoutLeft.offsetHeight - layoutLeft.offsetTop - window.innerHeight;
     });
     if(layoutLeft.offsetHeight>layoutMiddle.offsetHeight){
     return;
     }
     var t = document.getElementById("timelinefeedtab");
     var _body = document.body;
     if(t) var bottom_height = _body.offsetHeight - t.offsetHeight - t.offsetTop + window.innerHeight;
     var dates_scroll = $('tl-dates');
     window.addEventListener('scroll', function (e) {

     if(left<=0) return;
     //            console.log(e.pageY);
     if (e.pageY < offtop - 20) {
     layoutLeft.style.position = 'static';
     layoutMiddle.style.left = 0;
     layoutLeft.style.width = '';
     fixed = false;
     return;
     }

     var documentHeight = _body.offsetHeight;
     if(t){ var tmp = documentHeight - bottom_height;
     if (e.pageY >= tmp) {
     t.setStyle('position', 'relative');
     layoutLeft.style.position = 'absolute';
     //layoutLeft.style.left = '0';
     layoutLeft.style.bottom = '0';
     layoutLeft.style.top = '';
     fixed = false;
     layoutMiddle.style.left = left;
     return;
     } else{
     t.setStyle('position', 'relative');
     }
     }
     //      if(absolute) return;
     //            var differ = e.pageY < layoutLeft.offsetHeight;
     if (!fixed && e.pageY > sub + offtop) {

     sub = layoutLeft.offsetTop + layoutLeft.offsetHeight - layoutLeft.offsetTop - window.innerHeight;
     layoutLeft.style.position = 'fixed';
     layoutLeft.style.width = lLeftWidth + 'px';
     // layoutLeft.style.left = lLeftLeft + 'px';
     fixed = true;
     if (offheith < window.getSize().y) {
     layoutLeft.morph({
     'top': '50px'
     });
     } else {
     layoutLeft.style.top = -sub + 'px';
     }
     layoutMiddle.style.left = left;

     } else if (fixed && e.pageY < sub + offtop) {
     layoutLeft.style.position = 'static';
     fixed = false;
     layoutMiddle.style.left = 0;
     layoutLeft.style.width = '';
     }

     });
     }*/

});

var TimeLineCore = new Class({

    likeUrl: en4.core.baseUrl + 'like/like',
    unlikeUrl: en4.core.baseUrl + 'like/unlike',

    likeBtn: null,

    likeOnAir: false,

    toggleLike: function () {
        var self = this;
        var span = null;
        if (self.likeBtn.hasClass('he-btn-like')) {
            self.likeBtn.removeClass('he-btn-like');
            self.likeBtn.addClass('he-btn-unlike');
            self.likeBtn.setAttribute('data-like', 0);

            span = self.likeBtn.getElement('span');
            if (span.hasClass('he-glyphicon-thumbs-up')) {
                span.removeClass('he-glyphicon-thumbs-up');
                span.addClass('he-glyphicon-thumbs-down');
            }
            span = self.likeBtn.getElement('span#tl-like-btn-text');
            span.set('text', en4.core.language.translate('like_Unlike'));
            return;
        }
        if (self.likeBtn.hasClass('he-btn-unlike')) {
            self.likeBtn.removeClass('he-btn-unlike');
            self.likeBtn.addClass('he-btn-like');
            self.likeBtn.setAttribute('data-like', 1);

            span = self.likeBtn.getElement('span');
            if (span.hasClass('he-glyphicon-thumbs-down')) {
                span.removeClass('he-glyphicon-thumbs-down');
                span.addClass('he-glyphicon-thumbs-up');
            }
            span = self.likeBtn.getElement('span#tl-like-btn-text');
            span.set('text', en4.core.language.translate('like_Like'));
        }
    },

    likeSubject: function (btn, id, type) {
        var self = this;
        if (self.likeOnAir) {
            return;
        }
        self.likeOnAir = true;

        self.likeBtn = btn;

        var direction = btn.getAttribute('data-like');
        console.log(direction);
        var url = self.likeUrl;
        if (direction == 0) {
            url = self.unlikeUrl;
        }

        //this.showLoader();
        new Request.JSON({
            method: 'post',
            url: url + '/object/' + type + '/object_id/' + id,
            data: {
                format: 'json'
            },
            onSuccess: function (response) {
                self.block = false;
                if (response.error) {
                    he_show_message(response.html, 'error', 3000);
                    return;
                }
                self.likeOnAir = false;
                //self.hideLoader();
                self.toggleLike();
                return true;
            }
        }).send();

    },

    isTimeline: function () {
        return 1;
    }

});

document.tl_core = new TimeLineCore();

var TlManager = new Class({
    fireTab: function (id) {
        var tab = $('tab-'+id);
        if(tab) {
            tab.click();
        }
    }
});

document.tl_manager = new TlManager();
var tl_manager = document.tl_manager;