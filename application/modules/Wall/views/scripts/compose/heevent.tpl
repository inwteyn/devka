<?php if (!Engine_Api::_()->core()->hasSubject()):?>
<?php
  $this->headScript()
      ->appendFile($this->baseUrl() . '/application/modules/Wall/externals/scripts/composer_event.js')
  ;

  ?>


<script type="text/javascript">
  Wall.runonce.add(function (){
    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
    var composeInstance = feed.compose;

    if (composeInstance.options.type != 'message') {
      var type = 'wall';
      if (composeInstance.options.type) type = composeInstance.options.type;
      composeInstance.addPlugin(new Wall.Composer.Plugin.Heevent({
        title : '<?php echo $this->translate('Ask') ?>',
        lang : {

        }
      }));
    }
  });
</script>
<?php endif;?>