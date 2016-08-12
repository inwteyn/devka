<div class="he-hint">
	<div class="he-hint-body">
		<div class="he-hint-right">
			<?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.profile', '', array('width' => '100px', 'height' => '100px', 'target' => '_blank'))); ?>
		</div>
		<div class="he-hint-left">
			<div class="title">
				<?php
					$display_name = $this->user->getTitle();
					echo $this->htmlLink($this->user->getHref(), $display_name, array('target' => '_blank'));
				?>
			</div>
			<div class="clr"></div>
			<?php if ($this->paginator->getTotalItemCount() > 0): ?>
			<div class="horizontal-list">
				<?php if (!$this->isSelf): ?>
					<a class="item_count" href="javascript:void(0)" onclick="like.get_mutual_friends(<?php echo (int)$this->user->getIdentity(); ?>);">
						<?php echo $this->translate(array('like_%s mutual friend', 'like_%s mutual friends', $this->paginator->getTotalItemCount()), ($this->paginator->getTotalItemCount())); ?>
					</a>
				<?php else: ?>
					<a class="item_count" href="javascript:void(0)" onclick="like.get_friends(<?php echo (int)$this->user->getIdentity(); ?>);">
						<?php echo $this->translate(array('like_%s friend', 'like_%s friends', $this->paginator->getTotalItemCount()), ($this->paginator->getTotalItemCount())); ?>
					</a>
				<?php endif; ?>
				<?php foreach($this->paginator as $item): ?>
					<div class="item">
						<?php
              $img = $item->getPhotoUrl('thumb.icon');
              if (!$img) {
                $img = $this->baseUrl() . '/application/modules/Like/externals/images/nophoto/' . $item->getType() . '.png';
              }
              $photo = "<img width='32px' height='32px' class='thumb_icon item_photo_".$item->getType()."' src='".$img."' />";
							echo $this->htmlLink($item->getHref(), $photo, array('class' => 'he-hint-tip-links'));
						?>
						<div class="he-hint-title hidden"></div>
						<div class="he-hint-text hidden"><?php echo $item->getTitle(); ?></div>
					</div>
				<?php endforeach; ?>
				<div class="clr"></div>
			</div>
			<div class="clr"></div>
			<?php endif; ?>
		</div>
		<div class="clr"></div>
	</div>
	<div class="he-hint-options">
		<?php if (!$this->isSelf && $this->viewer->getIdentity()): ?>
			<?php echo $this->htmlLink($this->url(array('action' => 'compose', 'to' => $this->user->getIdentity()), 'messages_general'), $this->translate("Send Message"), array('class' => 'he-hint-send-message', 'target' => '_blank')); ?>
			<?php echo $this->userFriendship($this->user); ?>
			<div class="clr"></div>
		<?php endif; ?>
	</div>
</div>