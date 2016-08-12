
// Invoke when base core.js is ready
$(document).bind('appready', function(e, data /* server response */){
  window.Tablet.init();
});

window.Tablet = {
  init: function(data){
//  todo something to init
    this.configure(data);
  },
  configure: function(data){
    this.helper.boardInit()
  },
  helper:{
    boardInit: function(){
      var FnBoardOut = function(e){
        e.preventDefault();
              var $this = $(this);
              var $board = $('#core_board_index_rewrite');
              if($board.hasClass('ui-page-active')){
                var m = $.mobile;
                var $page = $(this).closest('.ui-page');
                m.changePage($page);
                $board.attr('board', 'mini');
                $this.removeAttr('board');
                return false;
              }
            };
//      var FnBoardOut2 = function(e){
//              var $this = $(document.querySelector('.ui-page[board="in"]'));
//              var $board = $('#core_board_index_rewrite');
//              if($board.hasClass('ui-page-active')){
//                window.history.back();
//                $board.attr('board', 'mini');
//                $this.addClass('ui-page-active');
//                $this.removeAttr('board');
//                return false;
//              }
//            };
      $('body').delegate('.board-in-cover', 'swipeleft', FnBoardOut);
      $('body').delegate('.board-in-cover', 'touchstart', FnBoardOut);
//      $('body').delegate('.menu_core_dashboard', 'vclick', FnBoardOut2);


      var FnLastOffset = function(e){
        $(this).data('lastOffset', window.pageYOffset);
      };
    }
  }
}
