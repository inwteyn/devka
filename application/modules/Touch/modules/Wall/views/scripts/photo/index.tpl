
<div class="set_prev_page" style="display: none;"><?php echo $this->prev?></div>
    <div class="set_next_page" style="display: none;"><?php echo $this->next?></div>

<div class="photo">

<div class="item item_<?php echo $this->photo->getIdentity()?> is_active">
  <?php if ("" != $this->photo->getTitle() || "" != $this->photo->getDescription()):?>
    <div class="header">
      <?php if ("" != $this->photo->getTitle()):?>
        <div class="title"><?php echo $this->viewMore($this->photo->getTitle())?></div>
      <?php endif;?>
      <?php if ("" != $this->photo->getDescription()):?>
        <div class="description"><?php echo $this->viewMore($this->photo->getDescription())?></div>
      <?php endif;?>
    </div>
  <?php endif;?>

  <table cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto;">
    <tr><td valign="middle" align="center" >
      <a href="javascript:void(0);" class="wall_blurlink">
        <img class="_img" src="<?php echo $this->photo->getPhotoUrl()?>" alt="<?php echo $this->photo->getTitle()?>"/>
      </a>
    </td></tr>
  </table>
</div>

</div>

<div class="body">

  <div class="photos_info">
      <div class="item item_<?php echo $this->photo->getIdentity();?>">
      <?php echo $this->wallComments($this->photo, $this->viewer())?>
      </div>
  </div>

</div>

