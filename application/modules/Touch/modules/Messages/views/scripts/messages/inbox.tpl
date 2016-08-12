<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: inbox.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<?php
	// Render the menu & paginator
	//echo $this->navigationPaginator($this->navigation, $this->paginator);
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
  <div class="search">
    <?php echo $this->paginationControl( 
    $this->paginator,
    null,
    array('pagination/filter.tpl', 'touch'),
    array(
    'search'=>$this->form_filter->getElement('search')->getValue(),
    'filter_default_value'=>$this->translate('TOUCH_Search Messages'),
    'filterUrl'=>$this->url(array('action'=> 'inbox'), 'messages_general', true)
    )
    ); ?>
  </div>
	<div id="filter_block">
		<?php if( $this->paginator->getTotalItemCount() >0 ): ?>

			<ul class="items">
				<?php foreach( $this->paginator as $conversation ):?>
				<?php
					$message = $conversation->getInboxMessage($this->viewer());
					$recipient = $conversation->getRecipientInfo($this->viewer());
					if( $conversation->recipients > 1 ) {
						$user = $this->viewer();
					} else {
						foreach( $conversation->getRecipients() as $tmpUser ) {
							if( $tmpUser->getIdentity() != $this->viewer()->getIdentity() ) {
								$user = $tmpUser;
							}
						}
					}
					if( !isset($user) || !$user ) {
						$user = $this->viewer();
					}
					?>

					<li <?php if( !$recipient->inbox_read ): ?> class="unread" <?php endif; ?>>
						<div class="item_photo">
							<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class'=>'touchajax')) ?>
						</div>

						<div class="item_body">
							<div class="item_title">
									<?php
										( '' != ($title = trim($message->getTitle())) ||
											'' != ($title = trim($conversation->getTitle())) ||
											$title = $this->translate('(No Subject)') );
									?>
									<?php echo $this->htmlLink($conversation->getHref(), $this->touchSubstr($this->stripHtmlTag($title)), array('class'=>'touchajax')) ?>
							</div>

							<div class="item_options item_date">
								<p>
									<?php if( $conversation->recipients == 1 ): ?>
										<?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('class'=>'touchajax')) ?>
									<?php else: ?>
										<?php echo $conversation->recipients ?> people
									<?php endif; ?>
								</p>

								<?php echo $this->timestamp($message->date) ?>
								-
								<?php echo $this->htmlLink($this->url(array('message_id'=>$conversation->getIdentity()), 'touch_messages_delete', true), $this->translate("Delete"), array('class'=>'touchconfirm')); ?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php else: ?>

			<div class="tip">
				<span>
					<?php echo $this->translate('Tip: %1$sClick here%2$s to send your first message!', "<a href='".$this->url(array('action' => 'compose'), 'messages_general')."' onclick='Touch.navigation.request($(this)); return false;'>", '</a>'); ?>
				</span>
			</div>

		<?php endif; ?>
</div>
</div>