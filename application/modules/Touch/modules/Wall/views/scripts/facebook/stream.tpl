
<?php 
  $this->headTranslate(array('WALL_STREAM_COMPOSE_TEXT'));
?>


<script type="text/javascript">

Wall.runonce.add(function() {

  var stream = new Wall.Stream.Facebook({
    'feed_uid': '<?php echo $this->feed_uid?>'
  });
  Wall.feeds.get('<?php echo $this->feed_uid?>').addEvent('complete', function (){
    stream.init();
  });

});

</script>


<div class="stream-container" style="display: none;">

  <div class="wallComposer">

    <form method="post" action="<?php echo $this->url()?>">

      <div class="wallTextareaContainer">
        <div class="inputBox">
          <div class="labelBox is_active">
            <span><?php echo $this->translate('WALL_Post Something...');?></span>
          </div>
          <div class="textareaBox">
            <div class="close"></div>
            <textarea rows="1" cols="1" name="body"></textarea>
            <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
            <?php if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
              <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
            <?php endif; ?>
          </div>
        </div>
        <div class="toolsBox"></div>

      </div>

      <div class="wall-compose-tray"></div>

      <div class="submitMenu">
        <button type="submit"><?php echo $this->translate("WALL_Share") ?></button>
        <ul class="shareMenu"></ul>
      </div>

    </form>

  </div>

  <ul class="feed service-feed" id="activity-feed">
    <?php echo $this->render('facebook/items.tpl')?>
  </ul>

</div>

