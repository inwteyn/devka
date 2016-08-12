function pinfeed (options){

   if($('activity-feed')) {
     column_count = Math.floor($('activity-feed').getComputedSize().width/270);
   }
    else{
     column_count = 3;
   }
    if(column_count <= 3 && column_count !=1){
       if( $('activity-feed')) $('activity-feed').setStyle('min-width', '700px');
        column_count = 3;

    }
  if($('global_page_user-profile-index') || $('global_page_timeline-profile-index')){
    column_count = 3;
  }
  if(column_count == 2 && options.w == 1){


    if($$('.generic_layout_container.layout_right')[0]){
      $$('.generic_layout_container.layout_right')[0].setStyle('margin-left','0px');
    }
  }

  if($('global_wrapper') && $('global_wrapper').getStyle('width').toInt()>1000){
   if( $$('.generic_layout_container.layout_right')[0]) $$('.generic_layout_container.layout_right')[0].setStyle('padding-right','66px');
  }

    if(options.bottom == 1){
        options.item[0].inject($('pinfeed1'), 'top');
    }else{
    var block_arr = [];

    for (var i = 0; i < column_count; block_arr[i ++] = $('pinfeed' + (i)));
       if(window.clear_pinfeed_counts == 1){
           start =1;
       }
    for(var k = start; k <options.item.length; k ++){
        var min = array[0], min_col = 0;
        for (var j = 1; j < column_count; j ++) {
            if (min > array[j]) {
                min = array[j];
                min_col = j;
            }
        }
        options.item[k].inject(block_arr[min_col], 'bottom');
        array[min_col] += parseInt(options.item[k].getComputedSize().height) + 30;
    }
    start = options.item.length;
    }

  $$('.wall-items-pinfeed').addEvents({
    'mouseout': function () {
      this.getElement('.wall-menu-expand-link').setStyle('display', 'none');
    },
    'mouseover': function () {
      this.getElement('.wall-menu-expand-link').setStyle('display', 'block');
    }

  });
}
window.addEvent('domready', function(){
    setTimeout(function(){
        if($('activity-feed')) { column_count = Math.floor($('activity-feed').getComputedSize().width/270);}
        else { column_count = 3;}
        if(column_count<3){
            column_count = Math.floor($('activity-feed').getComputedSize().width/270);
            if(column_count<3){
                column_count = 3;
            }
        }
      if($('global_wrapper') && $('global_wrapper').getStyle('width').toInt()>1000){
        if( $$('.generic_layout_container.layout_right')[0]) $$('.generic_layout_container.layout_right')[0].setStyle('padding-right','66px');
      }

        pinfeed_page = 1;
        start = 0;
        array = [];
        for (var i = 0; i < column_count; array[i++] = 0);
        //options.container.setStyle('width', column_count * 1);

        var options = {
            autoResize: true, // This will auto-update the layout when the browser window is resized.
            container: $('pinfeed'),
            item: $$('.wall-items-pinfeed'),
            offset: 2,
            itemWidth: 255,
            bottom: 0,
            w:1
        };
        var handler = $$('.wall-action-item');

        pinfeed(options);
    },3000);
});
    //window.fireEvent('resize');

window.addEvent('resize', function(){
    if(window.width_res == 1){
     new_size = window.getSize().y;

    column_count_new = Math.floor($('activity-feed').getComputedSize().width/275);
        if(column_count_new == column_count){
            return;
        }
    start = 0;
    array = [];
    for (var i = 0; i < column_count_new; array[i++] = 0);
    var options = {
        autoResize: true, // This will auto-update the layout when the browser window is resized.
        container: $('pinfeed'),
        item: $$('.wall-items-pinfeed'),
        offset: 2,
        itemWidth: 255,
        bottom: 0
    };
        pinfeed(options);

            old_size = new_size;


    }
});


function videoViewer( video_id){
    $('videoViewer').setStyle('display','block');
    $('videoViewer').setStyle('width','700px');
    $we = $('global_wrapper').getComputedSize().width;
    $he = window.getSize().y;
    var $width =  ($we-700)/2;
    $('videoViewer').setStyle('left',$width+'px');

        $('videoViewer').set('html','<div class="wpContainerVideo">' +
            '<a href="javascript: void(0)" onclick="clsoe_video()" class="close-video-contaner"></a>'+
        $('video_object_' + video_id).get('html')+
            '</div>' ) ;

    $he_contaner = $('videoViewer').getComputedSize().height;
    var $top =  (($he-$he_contaner)/2);

    $('videoViewer').setStyle('top',$top+'px');
    $('videoViewer').setStyle('z-index','502');
    $$('.video_background').setStyle('display', 'block');


}
function clsoe_video(){
    $('videoViewer').set('html','');
    $('videoViewer').setStyle('display', 'none');
    $$('.video_background').setStyle('display', 'none');
}

window.addEvent('domready', function(){
    if($$('.layout_active_theme_transformer')[0] && window.width_res == 1){
        $interval_check =  setInterval(function(){
            if(Math.floor($('activity-feed').getComputedSize().width/275)<3){

            }else{
                column_count = Math.floor($('activity-feed').getComputedSize().width/275);
                pinfeed_page = 1;
                start = 0;
                array = [];
                for (var i = 0; i < column_count; array[i++] = 0);
                var options = {
                    autoResize: true, // This will auto-update the layout when the browser window is resized.
                    container: $('pinfeed'),
                    item: $$('.wall-items-pinfeed'),
                    offset: 2,
                    itemWidth: 255,
                    bottom: 0
                };
                var handler = $$('.wall-action-item');

                pinfeed(options);
                clearInterval( $interval_check);
            }
        },2000)
    }

    if(window.width_res == 1){
    if ($$('.pinfeeds')) var wi =$$('.pinfeeds')[0].getComputedSize().width - column_count * (275);
    else  var wi =$$('.pinfeeds')[0].getComputedSize().width - column_count * (275);
    var ml = $$('.layout_left').getStyle("margin-left")[0].toInt();
    if(!$$('.layout_active_theme_transformer')[0] && !$$('.layout_active_theme_minimal')[0] && !$$('.layout_active_theme_minimal-blue')[0] ){
        $$('.layout_left').setStyle('margin-left', ml + ((wi/2)-3)+'px');
    }
    var wi =$$('.pinfeeds')[0].getComputedSize().width - column_count * (275);
    var ml = $$('.layout_left').getStyle("margin-left")[0].toInt();
//$$('.layout_left').setStyle('margin-left', ml + (wi/2)+'px');
        if(!$$('.layout_active_theme_transformer')[0]){
            $$('.fake-lists').setStyle('right',(ml-wi)-34+'px')
        }
    }
    if($$('.pinfeeds')[0].getComputedSize().width <=950)
    {
        var elements_div = $$('.generic_layout_container .layout_left').getChildren('div');
        elements_div.each(function(el, i)
        {
            el.setStyle('width','170px');
            if($$('.layout_active_theme_modern')){
                el.setStyle('margin-right','17px');
            }else if($$('.layout_active_theme_slipstream')){
                el.setStyle('margin-right','10px');
            }else{
                el.setStyle('margin-right','0px');
            }

        });
        $$('.generic_layout_container .layout_left').setStyle('margin-right','0px')
    }
});
