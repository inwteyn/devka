<div class="item_options">
<?php if ($this->member && $this->member->rsvp == 2):?>
<a class="active"><?php echo $this->
    translate('PAGEEVENT_ATTENDING').' ('.$this->attending->getTotalItemCount().')'?>
</a>
<?php else: ?>
<a href="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 2), 'page_event', true)?>"
   onclick = 'Touch.navigation.subRequest($(this), "_rsvp_loading", "_rsvp_"); return false;'><?php echo $this->translate('PAGEEVENT_ATTENDING').' ('.$this->attending->getTotalItemCount().')'?></a>
<?php endif;?>
<?php if ($this->member && $this->member->rsvp == 1):?>
<a class="active"><?php echo $this->
  translate('PAGEEVENT_MAYBEATTENDING').' ('.$this->maybe_attending->getTotalItemCount().')'?>
</a>
<?php else: ?>
<a href="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 1), 'page_event', true)?>"
 onclick = 'Touch.navigation.subRequest($(this), "_rsvp_loading", "_rsvp_"); return false;'><?php echo $this->translate('PAGEEVENT_MAYBEATTENDING').' ('.$this->maybe_attending->getTotalItemCount().')'?></a>
<?php endif;?>
<?php if ($this->member && $this->member->rsvp == 0):?>
<a class="active"><?php echo $this->
  translate('PAGEEVENT_NOTATTENDING').' ('.$this->not_attending->getTotalItemCount().')'?>
</a>
<?php else: ?>
<a href="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 0), 'page_event', true)?>"
onclick = 'Touch.navigation.subRequest($(this), "_rsvp_loading", "_rsvp_"); return false;'><?php echo $this->translate('PAGEEVENT_NOTATTENDING').' ('.$this->not_attending->getTotalItemCount().')'?></a>
<?php endif;?>
</div>
<div class="item_options">
<?php if ($this->attending->getTotalItemCount()):?>
  <div class="members">
    <div class="header">
      <a class="title"><?php echo $this->translate('PAGEEVENT_ATTENDING'); ?></a>
      <?php if ($this->attending->getTotalItemCount() > $this->attending->getItemCountPerPage()):?>
        <div class="viewall"><a href='javascript:Pageevent.members(<?php echo $this->event_id?>, 2, <?php echo $this->jsonInline($title)?>);'><?php echo $this->translate('PAGEEVENT_VIEWALL')?></a></div>
      <?php endif;?>
      <div class="clr"></div>
    </div>
    <div class="list">
      <?php foreach ($this->attending as $member):?>
        <div class="item">
          <div class="userinfo">
            <?php echo $this->htmlLink($member->getHref(), $member->getTitle())?>
          </div>
        </div>
      <?php endforeach;?>
      <div class="clr"></div>
    </div>

  </div>
<?php endif;?>

<?php if ($this->maybe_attending->getTotalItemCount()):?>
  <div class="members">
    <div class="header">
      <a class="title"><?php echo $this->translate('PAGEEVENT_MAYBEATTENDING'); ?></a>
      <?php if ($this->maybe_attending->getTotalItemCount() > $this->maybe_attending->getItemCountPerPage()):?>
        <div class="viewall"><a href='javascript:Pageevent.members(<?php echo $this->event_id?>, 1, <?php echo $this->jsonInline($title)?>);'><?php echo $this->translate('PAGEEVENT_VIEWALL')?></a></div>
      <?php endif;?>
      <div class="clr"></div>
    </div>
    <div class="list">
      <?php foreach ($this->maybe_attending as $member):?>
        <div class="item">
          <div class="userinfo">
            <?php echo $this->htmlLink($member->getHref(), $member->getTitle())?>
          </div>
        </div>
      <?php endforeach;?>
      <div class="clr"></div>
    </div>

  </div>
<?php endif;?>


<?php if ($this->not_attending->getTotalItemCount()):?>
  <div class="members">
    <div class="header">
      <a class="title"><?php echo $this->translate('PAGEEVENT_NOTATTENDING'); ?></a>
      <?php if ($this->not_attending->getTotalItemCount() > $this->not_attending->getItemCountPerPage()):?>
        <div class="viewall"><a href='javascript:Pageevent.members(<?php echo $this->event_id?>, 0, <?php echo $this->jsonInline($title)?>);'><?php echo $this->translate('PAGEEVENT_VIEWALL')?></a></div>
      <?php endif;?>
      <div class="clr"></div>
    </div>
    <div class="list">
      <?php foreach ($this->not_attending as $member):?>
        <div class="item">
          <div class="userinfo">
            <?php echo $this->htmlLink($member->getHref(), $member->getTitle())?>
          </div>
        </div>
      <?php endforeach;?>
      <div class="clr"></div>
    </div>

  </div>
<?php endif;?>
</div>
