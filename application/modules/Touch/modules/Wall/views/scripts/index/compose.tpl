
<style type="text/css">



</style>

<script type="text/javascript">

  Wall.runonce.add(function (){
    new Wall.Composer({composer_uid: $$('.wallComposer.social')[0]});
    new Wall.Composer({composer_uid: $$('.wallComposer.facebook')[0]});
    new Wall.Composer({composer_uid: $$('.wallComposer.twitter')[0]});
  });

</script>


<div class="wallComposer">

  <div class="wallTextareaContainer">
    <div class="inputBox">
      <div class="labelBox is_active">
        <?php echo $this->translate('Post Something...');?>
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

  <div class="toolsBoxContainer"></div>

  <div class="submitMenu">
    <button type="submit"><?php echo $this->translate("Share") ?></button>
    <ul class="shareMenu"></ul>
  </div>

</div>


