Initializer.core.init =  function(response){
    if($('body.tablet').length){
      var init = function(){
        var $page = $('#core_board_index_rewrite');
        if($page.length){
          if($page.attr('board') == 'mini' || $page.hasClass('ui-page-active')){
            clearInterval(intervlisten);
            intervlisten = setInterval(listen, 2000);
            return clearInterval(intervInit);
          }
          $page.page();
          $page.attr('board', 'mini');
          if(!core.device.platform.isAndroid() || core.device.platform.isAndroid() > 4.2){
                var iscroll = new iScroll($page.find('.component-dashboard')[0], { hScroll: false, vScroll: true, hideScrollbar:true });
                iscroll._resize();
                window._dashboardScroll = iscroll;
                $page.data('iscroll', iscroll);
          }
        }
      };
      var listen = function(){
          if(!$('#core_board_index_rewrite').length){
            clearInterval(intervInit);
            intervInit = setInterval(init, 500);
            return clearInterval(listen);
          }
      };
      var intervlisten = -1;
      var intervInit = setInterval(init, 500);
    }
  };

