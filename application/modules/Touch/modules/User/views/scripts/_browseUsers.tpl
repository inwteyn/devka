<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _browseUsers.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<div class="layout_content">
	<?php echo $this->paginationControl(
			$this->paginator,
			null,
			array('pagination/filter.tpl', 'touch'),
			array(
				'search'=>$this->form_filter->getElement('search')->getValue(),
				'filter_default_value'=>$this->translate('TOUCH_Search Memebers'),
				'filterUrl'=>$this->url(array(), 'user_general', true),
			)
	); ?>

	<div id="filter_block">
		<ul class="items">
			<?php foreach( $this->users as $user ): ?>
				<li>
					<div class="item_photo">
						<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class'=>'touchajax')) ?>
					</div>

					<div class='item_body'>
						<div class="item_title">
							<?php echo $this->htmlLink($user->getHref(), $user->getTitle(),  array('class'=>'touchajax')) ?>
						</div>

						<?php if( $this->viewer()->getIdentity() ): ?>
							<div class='item_options'>
								<?php echo $this->userFriendship($user) ?>
							</div>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		</div>
</div>