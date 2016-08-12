<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: navigation.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<div <?php if ($this->isAllowedPost): ?> class="tabs" <?php endif; ?> id="page_blog_options"> <!--  page_blog_options -->
	<?php if ($this->isAllowedPost): ?>
		<?php
			echo $this
			->navigation()
			->menu()
			->setContainer($this->navigation)
			->setPartial(array('_contentNavIcons.tpl', 'page'))
			->render();
		?>
	<div class="pageblog_loader hidden" id="pageblog_loader">
		<?php echo $this->htmlImage($this->baseUrl().'/application/modules/Pageblog/externals/images/loader.gif'); ?>
	</div>
	<?php else: ?>
		<div class="pageblog_loader_2 hidden" id="pageblog_loader">
			<?php echo $this->htmlImage($this->baseUrl().'/application/modules/Pageblog/externals/images/loader.gif'); ?>
		</div>
	<?php endif; ?>
	
	<div class="clr"></div>
</div>
<?php if ($this->isAllowedPost): ?>
	<br />
<?php endif; ?>