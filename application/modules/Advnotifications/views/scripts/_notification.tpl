<div class="image">
  <img src="<?php echo $this->user->getPhotoUrl('thumb.icon') ?>">
</div>
<div class="content">
  <?php echo $this->nContent ?>
</div>
<?php
    if(isset($this->nContentlink)){
?>
<div class="nContentlink" id="nContentlink">
    <?php echo $this->nContentlink;?>
</div>
<?php } ?>
<div class="close">
  <i id="advnotification-close" class="he-glyphicon he-glyphicon-remove"></i>
</div>
<div style="clear: both;"></div>
<div class="time-strip">
  <?php if($this->icon): ?>
    <i class="hei hei-<?php echo $this->icon; ?>"></i>
  <?php endif; ?>
  <span class="time"><?php echo $this->time; ?></span>
</div>