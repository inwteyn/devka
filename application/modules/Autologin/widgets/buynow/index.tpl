<style>
  #frame{
    position: fixed;
    right: 0;
    bottom: 0;
    z-index: 101;
    border: 1px solid #CCCCCC;
    height: 102px;
  }

  #wrap {
    position: absolute;
    top: 48px;
  }

  .ribbon-wrapper-green {
    width: 85px;
    height: 88px;
    overflow: hidden;
    position: absolute;
    top: 0px;
    left: 0px;
  }

  .ribbon-green {
    font: bold 15px Sans-Serif;
    color: #333;
    text-align: center;
    text-shadow: rgba(255,255,255,0.5) 0px 1px 0px;
    -webkit-transform: rotate(-49deg);
    -moz-transform: rotate(-49deg);
    -ms-transform: rotate(-49deg);
    -o-transform: rotate(-49deg);
    position: relative;
    padding: 7px 0;
    left: -33px;
    top: 19px;
    width: 120px;
    background-color: #FF8300;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#FF8300), to(#FF9000));
    background-image: -webkit-linear-gradient(top, #FF8300, #FF9000);
    background-image: -moz-linear-gradient(top, #FF8300, #FF9000);
    background-image: -ms-linear-gradient(top, #FF8300, #FF9000);
    background-image: -o-linear-gradient(top, #FF8300, #FF9000);
    color: #FFFFFF;
    -webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.3);
    -moz-box-shadow: 0px 0px 3px rgba(0,0,0,0.3);
    box-shadow: 0px 0px 3px rgba(0,0,0,0.3);
  }

  .ribbon-green:before, .ribbon-green:after {
    content: "";
    border-top:   3px solid ##FF8300;
    border-left:  3px solid transparent;
    border-right: 3px solid transparent;
    position:absolute;
    bottom: -3px;
  }

  .ribbon-green:before {
    left: 0;
  }
  .ribbon-green:after {
    right: 0;
  }​
</style>
<script>
  window.addEvent('domready',function(){
    if($('frame'))$('frame').hide();
    if($('wrap'))$('wrap').hide();

    var scroll_ = function () {
         if (window.pageYOffset > 120) {
           if($('frame'))$('frame').show();
           if($('wrap'))$('wrap').show();
         }
       };
    window.addEventListener('scroll',scroll_ );

    var url_request_name_module = "<?php echo $this->module;?>";
    var src_url_for_frame = "http://dev.hire-experts.com/product-banner.php?product=";
    if(url_request_name_module){
      var url = src_url_for_frame+url_request_name_module;
      if($('frame_on_page'))$('frame_on_page').set('src',url);
      if(navigator.appName=="Microsoft Internet Explorer"){
        $('frame_on_page').set('width',664);
        if(Cookie.read(url_request_name_module)){
          if($('frame'))$('frame').hide();
          if($('wrap'))$('wrap').hide();
        }
      }
    }

    if($('x_button')){
      $('x_button').addEvent('click', function(){
        window.removeEventListener('scroll', scroll_);
        if(!Cookie.read(url_request_name_module)){
           Cookie.write(url_request_name_module, 1);
        }
            if($('frame'))$('frame').hide();
            if($('wrap'))$('wrap').hide();
      });
    }
  });
</script>
<?php
  if(!$_COOKIE[$this->module]){
?>
<div id="wrap">
  <div class="ribbon-wrapper-green"><div class="ribbon-green"><a style="color: #FFFFFF !important; text-decoration: none !important; " href="http://dev.hire-experts.com/" target="_blank">Buy Now</a></div></div>
</div>​
<div id="frame">
  <div id="x_button" style="position: absolute;top: 0;left: 632px;z-index: 101;cursor: pointer;">x</div>
<iframe id="frame_on_page" style="overflow: hidden " width="647" height="107" align="middle" src="http://dev.hire-experts.com/product-banner.php?product=gifts"></iframe>
</div>
<?php } ?>