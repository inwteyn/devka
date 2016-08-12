<script type="text/javascript">
    (function(){
    if(Touch.isIPhone()) {
        window.uploadedPhotos = [];
        window.Picup.responseCallback = function(response) {
          var t = "<?php echo $this->photo_result; ?>";
        }
    }
    })();
</script>
<!--<div id="navigation_content">-->
<!--  <div class="layout_content">-->
<!--    --><?php //echo $this->form->setAttrib('class', 'global_form touchupload touch-multi-upload')->render($this); ?>
<!--  </div>-->
<!--</div>-->