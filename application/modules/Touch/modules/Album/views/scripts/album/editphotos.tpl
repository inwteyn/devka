<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: editphotos.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<?php
	// Render the menu & paginator
	echo $this->navigationPaginator($this->navigation, $this->paginator);
?>

<div id="navigation_content" class="layout_content">
		<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="touchform">
			<?php echo $this->form->album_id; ?>
			<ul class='items'>
				<?php foreach( $this->paginator as $photo ): ?>
					<li>
						<div class="item_photo">
							<?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo), array('class'=>'touchajax'))  ?>
						</div>
						<div class="item_body">
							<?php
								$key = $photo->getGuid();
								echo $this->form->getSubForm($key)->render($this);
							?>
							<div class="albums_editphotos_cover">
								<input type="radio" name="cover" id = "cover_<?php echo $photo->getIdentity(); ?>" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->album->photo_id == $photo->getIdentity() ): ?> checked="checked"<?php endif; ?> />
								<label for="cover_<?php echo $photo->getIdentity(); ?>"><?php echo $this->translate('Album Cover');?></label>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php echo $this->form->submit->addDecorator('ViewHelper')->render(); ?>
		</form>
</div>