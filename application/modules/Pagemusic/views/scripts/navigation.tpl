<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: navigation.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */
?>

<div <?php if ($this->isAllowedPost): ?> class="tabs" <?php endif; ?> id="page_music_options">
	<?php if ($this->isAllowedPost): ?>
	<?php 
    echo $this
		->navigation()
		->menu()
		->setContainer($this->navigation)
		->setPartial(array('_contentNavIcons.tpl', 'page'))
		->render(); 
	?>
		<div class="pagemusic_loader hidden" id="page_music_loader">
			<?php echo $this->htmlImage($this->baseUrl() . '/application/modules/Pagemusic/externals/images/loader.gif'); ?>
		</div>
	<?php else: ?>
		<div class="pagemusic_loader_2 hidden" id="page_music_loader">
			<?php echo $this->htmlImage($this->baseUrl() . '/application/modules/Pagemusic/externals/images/loader.gif'); ?>
		</div>
	<?php endif; ?>
	<div class="clr"></div>
</div>
<?php if ($this->isAllowedPost): ?>
<br />
<?php endif; ?>