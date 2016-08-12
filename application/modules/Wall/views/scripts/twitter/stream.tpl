<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: stream.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>


<script type="text/javascript">

Wall.runonce.add(function() {

  var stream = new Wall.Stream.Twitter({
    'feed_uid': '<?php echo $this->feed_uid?>'
  });
  Wall.feeds.get('<?php echo $this->feed_uid?>').streams.add('twitter', stream);

});

</script>


<div class="stream-container">

  <div class="wallComposer">

    <form method="post" action="<?php echo $this->url()?>">

      <div class="wallComposerContainer">

        <div class="wallTextareaContainer">
          <div class="inputBox">
            <div class="labelBox is_active">
              <span><?php echo $this->translate("WALL_What's on your mind?");?></span>
            </div>
            <div class="textareaBox">
              <div class="close"></div>
              <textarea rows="1" cols="1" name="body"></textarea>
              <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
            </div>
          </div>
          <div class="toolsBox"></div>

        </div>

      </div>

      <div class="wall-compose-tray"></div>

      <div class="submitMenu">
        <button type="submit"><?php echo $this->translate("WALL_Share") ?></button>
        <div class="wall_counter">140</div>
        <ul class="shareMenu"></ul>
      </div>

    </form>

  </div>


  <ul class="feed service-feed" id="activity-feed">
    <?php echo $this->render('twitter/items.tpl') ?>
  </ul>

</div>


