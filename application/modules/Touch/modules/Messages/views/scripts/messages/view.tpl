<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<?php if( count($this->navigation) > 0 ): ?>
<?php
	// Render the menu
	echo $this->navigation()
		->menu()
		->setContainer($this->navigation)
		->setPartial(array('navigation/index.tpl', 'touch'))
		->render();
?>
<?php endif; ?>

<div id="navigation_content">
	<div class="layout_content">

		<div>
			<?php
			$you  = array_shift($this->recipients);
			$you  = $this->htmlLink($you->getHref(), ($you==$this->viewer()?$this->translate('You'):$you->getTitle()), array('class'=>'touchajax'));
			$them = array();
			foreach ($this->recipients as $r) {
				if ($r != $this->viewer()) {
						$them[] = ($r==$this->blocker?"<s>":"").$this->htmlLink($r->getHref(), $r->getTitle(), array('class'=>'touchajax')).($r==$this->blocker?"</s>":"");
				} else {
						$them[] = $this->htmlLink($r->getHref(), $this->translate('You'), array('class'=>'touchajax'));
				}
			}

			if (count($them)) echo $this->translate('Between %1$s and %2$s', $you, $this->fluentList($them));
			else echo $this->translate('Conversation with a deleted member.');
			?>
		</div>

		<ul class="items">
			<?php foreach( $this->messages as $message ):
				$user = $this->user($message->user_id); ?>
				<li>
						<div class='item_photo'>
							<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class'=>'touchajax')) ?>
						</div>
						<div class='item_body'>
							<p>
								<?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('class'=>'touchajax')) ?>
							</p>
							<p class="item_date">
								<?php echo $this->timestamp($message->date) ?>
							</p>
						<?php echo nl2br(html_entity_decode($message->body)) ?>
						<?php if( !empty($message->attachment_type) && null !== ($attachment = $this->item($message->attachment_type, $message->attachment_id))): ?>
							<div class="message_attachment">
								<?php if(null != ( $richContent = $attachment->getRichContent(false, array('message'=>$message->conversation_id)))): ?>
									<?php echo $richContent; ?>
								<?php else: ?>
									<div class="message_attachment_photo">
										<?php if( null !== $attachment->getPhotoUrl() ): ?>
											<?php echo $this->itemPhoto($attachment, 'thumb.normal') ?>
										<?php endif; ?>
									</div>
									<div class="message_attachment_info">
										<div class="message_attachment_title">
											<?php echo $this->htmlLink($attachment->getHref(array('message'=>$message->conversation_id)), $attachment->getTitle()) ?>
										</div>
										<div class="message_attachment_desc">
											<?php echo $attachment->getDescription() ?>
										</div>
									</div>
							 <?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>

			<li>
				<div>
				<?php if( !$this->blocked || (count($this->recipients)>1)): ?>
					<?php echo $this->form->setAttrib('class', 'global_form touchform')->render($this) ?>
				<?php else:?>
					<?php echo $this->translate('You can no longer respond to this message because %1$s has blocked you.', $this->blocker->getTitle())?>
				<?php endif; ?>
				</div>

			</li>
		</ul>
	</div>
</div>