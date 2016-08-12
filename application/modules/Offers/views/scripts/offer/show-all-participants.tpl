<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Offers/externals/scripts/core.js');
?>
<div id="he_contacts_loading" style="display:none;">&nbsp;</div>
<div id="he_contacts_message" style="display:none;"><div class="msg"></div></div>

<div class="he_contacts">
  <?php if ($this->title): ?>
  <h4 class="contacts_header"><?php echo $this->title; ?></h4>
  <?php endif; ?>
  <div class="clr"></div>

  <div class="contacts">
    <div id="he_contacts_list">
      <?php foreach($this->users as $user): ?>
        <a href="<?php echo $user->getHref(); ?>" class="item" target="_blank">
          <span class="photo"><?php echo $this->itemPhoto($user, 'thumb.icon', $user->getTitle()); ?></span>
          <span class="name"><?php echo $user->getTitle(); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
</div>