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
<script type="text/javascript">
(function(){
	var options={
		prev_button: 'paginator-navigation-prev',
		next_button: 'paginator-navigation-next',
    media_photo: 'media_photo_next',
    sub_nav: true
	}

	en4.core.runonce.add(function(){
		Photobox.setOptions(options);

		if (Photobox.isOpen){
			Photobox.show('media_photo');
		}
	});
})();
</script>

<?php
$next_photo = $this->photo->getNextCollectible();
$prev_photo = $this->photo->getPrevCollectible();
?>

	<div class="navigation-header navigation-paginator">
		<?php if ($this->album->count() > 1): ?>
			<span class="touch-navigation-paginator">
				<a  class="paginator-navigation" href="<?php echo $this->url(array('action' => 'view-photo', 'photo_id' => $prev_photo->getIdentity()), 'page_album'); ?>" id="paginator-navigation-prev"
						onclick="Touch.navigation.subNavRequest($(this)); return false;">
					<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev.png" alt="<?php echo $this->translate('Prev') ?>" />
				</a>

				<a class="paginator-navigation" href="<?php echo $this->url(array('action' => 'view-photo', 'photo_id' => $next_photo->getIdentity()), 'page_album'); ?>/sm/0"  id="paginator-navigation-next"
					 onclick="Touch.navigation.subNavRequest($(this)); return false;">
					<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next.png" alt="<?php echo $this->translate('Next') ?>" />
				</a>
			</span>
		<?php endif; ?>
  </div>

	<div style="height:10px"></div>

	<div id="navigation_loading" style="display:none; text-align: center; vertical-align: middle;">
		<a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
	</div>

	<div id="navigation_content">
		<div class="layout_content">
			<div class="album_photo_left">
						<?php echo $this->translate('Added %1$s', $this->timestamp($this->photo->modified_date)) ?>
			</div>

			<div class="album_photo_right">
				<?php echo $this->translate('TOUCH_Photo %1$s of %2$s',
						$this->locale()->toNumber($this->photo->getCollectionIndex() + 1),
						$this->locale()->toNumber($this->album->count()))?>
				<div id="loading_photo"></div>
			</div>

			<div class="clr"></div>

			<div class='photo_view_container'>
				<div class='album_viewmedia_container photo' id='media_photo_div'>
					<a id='media_photo_next'  href='<?php echo $this->escape($next_photo->getHref()) ?>' onclick="return false;">
						<?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
							'id' => 'media_photo',
						)); ?>
					</a>
				</div>
				<?php if( $this->canDelete ): ?>
					&nbsp;<?php echo $this->htmlLink(array('reset' => false, 'action' => 'delete'), $this->translate('Delete') . '<div style="display:none"> ' . $this->translate('TOUCH_this photo') . ' ?</div>', array('class' => 'touchconfirm redirect')) ?>
				<?php endif; ?>
			</div>

			<?php echo $this->touchAction("list", "comment", "core", array("type"=>"album_photo", "id"=>$this->photo->getIdentity(), 'viewAllLikes'=>true)); ?>

		</div>
	</div>
