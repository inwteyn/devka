<?php echo $this->htmlLink(array('route' => 'offers_specific','action' => 'show-all-participants', 'offer_id' => $this->offer_id),
  $this->translate('OFFERS_offer_show_all'), array('class' => 'smoothbox', 'style' => 'float: right; padding-top: 3px;')); ?>
<?php if (count($this->users) > 0): ?>
  <ul class="offer_list_user_sub">
    <?php foreach($this->users as $user): ?>
      <li>
        <span class="offer_user_photo"><?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle())); ?></span>
        <span class="offer_user_title"><?php echo $this->htmlLink($user->getHref(), $this->string()->truncate($user->getTitle(), 10)); ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
  <span class="clr"></span>
  <br/>
<?php endif; ?>