<h2 style="float: left;"><?php echo $this->video->getTitle(); ?></h2>
<div class="video_embed">
  <?php echo $this->video->getPlayer(); ?>
</div>
<div class="avp_clear"></div>
<?php echo $this->action("list", "comment", "core", array("type" => "avp_video", "id"=> $this->video->getIdentity())) ?>