
var AjaxWidget = {

  ajax_widgets: {},
  ajax_loading: {},
  url: 'optimizer/index/widget',

  setWidgetTitle: function (info)
  {
    if (!info){
      return ;
    }
    var tab = $$('.tab_'+info.content_id+' a')[0];
    if (tab){
      var title = '';
      title += info.title;
      if (info.childCount){
        title += '<span>(' + info.childCount + ')</span>';
      }
      tab.set('html', title);
    }

  },

  run: function (){

    var ajax_widgets = this.ajax_widgets;
    var ajax_loading = this.ajax_loading;

    ajax_widgets.each(function (content){

      var content_id = content.content_id;
      var params = content.params;

      // Hide tab link while loading
      en4.core.runonce.add(function (){
        var tab = $$('.tab_'+content_id)[0];
        if (tab){
          tab.setStyle('display', 'none')
            .addClass('optimizer_hide');
        }
      });

      // Profiling
      ajax_loading[content_id] = {
        'start_time': new Date().getTime()
      };

      var request_params = params;
      request_params.content_id = content_id;
      request_params.format = 'html';

      (new Request({
        'url': AjaxWidget.url,
        'data': request_params,
        'onSuccess': function (){

          var html = this.response.text;

          // has been setNoRender
          if (!html){
            var $loader = $('loader_content_id_' + content_id);
            if ($loader){
              var container = $loader.getParent('.generic_layout_container');
              container.destroy();
              delete container;
            }
            return ;
          }

          // Check tab link: default profile
          var tab = $$('.tab_'+content_id)[0];
          if (tab){
            tab.setStyle('display', 'inline-block')
              .removeClass('optimizer_hide');
          }
          // Check empty more link
          var $more = $$('.more_tab')[0];
          if ($more){
            if ($more.getElement('.tab_pulldown_contents ul').getChildren('li:not(.optimizer_hide)').length == 0){
              $more.setStyle('display', 'none')
                .addClass('optimizer_hide');
            }
          }

          // Profiling
          var prof = ajax_loading[content_id];
          prof.render_start_time = new Date().getTime();

          // set html
          var $loader = $('loader_content_id_' + content_id);
          if (!$loader){
            return ;
          }
          var $c = $loader.getParent();
          if (!$c){
            return ;
          }
          $c.innerHTML = html;

          // get all scripts and connect their
          try {

            var inline_script = [];
            var src_script = [];

            html.replace(/<script([^>]*)>([\s\S]*?)<\/script>/gi, function (all, attrs, code){

              if (code){
                inline_script[inline_script.length] = code;
              }
              if (attrs.indexOf('src') !== -1){
                var m = attrs.match(/.*src=(.*)/i);
                if (m[1]){
                  var src = m[1].replace(/"|'/g, '');
                  if (src){
                    src_script[src_script.length] = src;
                    head.js(src);
                  }
                }
              }
              return '';
            });

            head.ready(function (){
              inline_script.each(function (script){
                try {
                  // Bug fix: composeInstance
                  // I know it's terribly :)
                  if (script.indexOf('var composeInstance;') !== -1){
                    script = script.replace('var composeInstance;', 'window.composeInstance={};');
                  }

                  eval(script);
                } catch (e) {
                  if (console){
                    console.log(e);
                  }
                }
              });
              en4.core.runonce.trigger();

              // Bug fix: _activityUpdateHandler
              if (window._activityUpdateHandler){
                window.activityUpdateHandler = window._activityUpdateHandler;
              }

              // Bug Fix: Music player on the feed
              if (en4.music && en4.music.player){
                en4.music.player.getSoundManager().reboot();
              }

            });

            // Profiling
            prof.end_time = new Date().getTime();
            prof.total_time = prof.end_time - prof.start_time;
            prof.render_time = prof.end_time - prof.render_start_time;

            // Bug fix: replace return url from ajax widget to our page
            var optimizer_url = en4.core.baseUrl + 'optimizer/index/widget';
            var new_url = window.location.href;
            $$('input[value='+optimizer_url+']').set('value', new_url);

          } catch (e) {
            if (console){
              console.log(e);
            }
          }
        },
        'onError': function (){
          if (console){
            console.log(content_id + ' content_id not was loaded');
          }
        }
      })).send();
    });
  }

};