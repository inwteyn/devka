<script type="text/javascript">
  document.addEvent('domready', function() {
       $$('#captcha-element img').addEvent('click', function() {
          var jsonRequest = new Request.JSON({
              url: "<?= $this->url(array(), 'refresh-captcha', false) ?>",
              onSuccess: function(captcha) {
                  $('captcha-id').set('value', captcha.id);
                  $$('#captcha-element img').set('src', captcha.src);
              }
          }).get();
      });
  });
</script>
