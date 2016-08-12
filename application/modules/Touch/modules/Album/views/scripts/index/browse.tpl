<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: browse.tpl 2011-04-26 11:18:13 mirlan $
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
	<div	class="search">
		<?php echo $this->paginationControl(
				$this->paginator,
				null,
				array('pagination/filter.tpl', 'touch'),
				array(
					'search'=>$this->form_filter->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Albums'),
					'filterUrl'=>$this->url(array(), 'album_general', true),
				)
		); ?>
	</div>

	<div id="filter_block">
	<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

		<ul class="items">
			<?php foreach( $this->paginator as $album ): ?>
				<li>
					<div class="item_photo">
						<a class="thumbs_photo touchajax" href="<?php echo $album->getHref(); ?>">
							<span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
						</a>
					</div>

					<div class="item_body">
						<div class="item_title">
							<?php echo $this->htmlLink($album, $this->string()->chunk(Engine_String::substr($album->getTitle(), 0, 45), 10), array('class'=>'touchajax')) ?>
						</div>
						<div class="item_options item_date">
							<?php echo $this->translate('By');?>
							<?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
							-
							<?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
						</div>
					</div>
			</li>

			<?php endforeach;?>
		</ul>

	<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('Nobody has created an album yet.');?>
			</span>
		</div>
	<?php endif; ?>
	</div>
</div>