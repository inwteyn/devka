<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2011-04-26 11:18:13 mirlan $
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
					'filter_default_value'=>$this->translate('TOUCH_Search My Albums'),
					'filterUrl'=>$this->url(array('action'=>'manage'), 'album_general', true),
				)
		); ?>
	</div>

	<div id="filter_block">
	<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
		<ul class='items'>
			<?php foreach( $this->paginator as $album ): ?>
					<li id="album-<?php echo $album->getIdentity(); ?>">

					<div class="item_photo">
						<a class="thumbs_photo touchajax" href="<?php echo $album->getHref(); ?>">
							<span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
						</a>
					</div>

					<div class="item_body">

						<div class="item_title">
							<?php echo $this->htmlLink($album->getHref(), Engine_String::substr($album->getTitle(), 0, 15) . ((Engine_String::strlen($album->getTitle()) > 15)? '...':''), array('class'=>'touchajax')) ?>
						</div>

						<div class="item_options item_date">
							<?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
							-
							<a href="<?php echo $this->url(array('action' => 'delete', 'album_id' => $album->album_id), 'album_specific', true); ?>" class="touchconfirm">
								<?php echo $this->translate("Delete"); ?>
							</a>
						</div>

					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php else: ?>
			<div class="tip">
				<span>
					<?php echo $this->translate('You do not have any albums yet.');?>
				</span>
			</div>
		<?php endif; ?>
	</div>
</div>