<script type="text/javascript">
  function avpAcceptVideoOnlyFromYTnVM(el){
    var url = el.get('value');
    if(url.length!=0){
      //-------------------
      var valid = false;
      var urlmatch=[
        'youtube.com',
        'www.youtube.com',
        'http://youtube.com',
        'http://www.youtube.com',
        'vimeo.com',
        'www.vimeo.com',
        'http://vimeo.com',
        'http://www.vimeo.com'
      ];
      for(var i=1; i<urlmatch.length; i++){
        if(url.substr(0, urlmatch[i].length) == urlmatch[i]){
          valid = true;
          i = urlmatch.length;
        }
      }
      if(!valid){
      alert('<?php echo $this->translate('TOUCH_You can import videos from www.youtube.com or www.vimeo.com only') ?>');
        el.set('value', '');
      }
    }
  }
</script>
<div class="avp_form">
<?php echo $this->form->render($this);?>
</div>
