<?php
// $this->headScript()->appendFile($this->baseUrl() . 'application/modules/Touch/modules/Chat/externals/scripts/core.js');
?>
<?php if($this->canIM){ ?>
<script type="text/javascript">
  document.body.setStyle('padding-bottom', '40px');
  var chatHandler;
  en4.core.runonce.add(function() {
    try {
      chatHandler = new ChatHandler({
        'baseUrl' : en4.core.baseUrl,
        'basePath' : en4.core.basePath,
        'identity' : <?php echo $this->identity; ?>,
        'enableIM' : <?php echo $this->canIM; ?>,
        'enableChat' : false,
        'imOptions' : { 'memberIm' : <?php echo $this->memberIm; ?> },
        'delay' : <?php echo $this->delay; ?>
      });

      chatHandler.start();
      window._chatHandler = chatHandler;
    } catch( e ) {
    }
  });
  var started = false;
  var to = null;
  var im_container = $('im_container');
  var im_main_menu_active;
  var im_menu_head;
  var im_menu_footer;
  window.addEvent('scroll', function() {
    $('im_container').style.top = (window.pageYOffset + window.innerHeight) + 'px';
    if(!started){
        im_main_menu_active = document.body.getElement('.im_menu_wrapper_container').getElement('.im_main_menu_active');
        if(im_main_menu_active){
            im_menu_head = im_main_menu_active.getElement('.im_menu_head');
            im_menu_footer = im_main_menu_active.getElement('.im_menu_footer');
        }
        started =true;
      if(im_menu_head && im_menu_footer){
          //im_menu_head.setStyle('display', 'none');
          //im_menu_footer.setStyle('display', 'none');
      }
    }
    clearTimeout(to);
    to = setTimeout(function(){
        if(im_menu_head && im_menu_footer){
            im_menu_head.style.top = (window.pageYOffset) + 'px';
            im_menu_footer.style.top = (window.pageYOffset + window.innerHeight-71) + 'px';
            im_menu_head.setStyle('display', 'block');
            im_menu_footer.setStyle('display', 'block');
            //im_menu_footer.getElement('textarea').focus();
        }
        started =false;
    }, 100);
  });

</script>
  <?php } ?>