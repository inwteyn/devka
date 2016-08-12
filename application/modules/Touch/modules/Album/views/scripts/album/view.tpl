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
		Photobox.isOpen = true;
	})();
</script>

<?php
	// Render the menu & paginator
	echo $this->navigationPaginator($this->navigation, $this->paginator);
?>

<div id="navigation_content">
	<div class="layout_content">
		<ul class="items">
			<?php foreach( $this->paginator as $photo ): ?>
				<li class="thumbs">
						<div class="item_photo" >
							<a class="thumbs_photo touchajax" href="<?php echo $photo->getHref(); ?>">
								<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
							</a>
						</div>
				</li>
			<?php endforeach;?>
		</ul>

		<div class="clr"></div>
	</div>
</div>