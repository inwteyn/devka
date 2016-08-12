<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: navigation.tpl 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */
?>

<div <?php if ($this->isAllowedPost): ?> class="tabs" <?php endif; ?> id="page_document_options"> <!--  page_document_options -->
	<?php if ($this->isAllowedPost): ?>
		<?php
			echo $this
			->navigation()
			->menu()
			->setContainer($this->navigation)
			->setPartial(array('_contentNavIcons.tpl', 'page'))
			->render();
		?>
	<div class="pagedocument_loader hidden" id="pagedocument_loader">
		<?php echo $this->htmlImage($this->baseUrl().'/application/modules/Pagedocument/externals/images/loader.gif'); ?>
	</div>
	<?php else: ?>
		<div class="pagedocument_loader_2 hidden" id="pagedocument_loader">
			<?php echo $this->htmlImage($this->baseUrl().'/application/modules/Pagedocument/externals/images/loader.gif'); ?>
		</div>
	<?php endif; ?>
	
	<div class="clr"></div>
</div>
<?php if ($this->isAllowedPost): ?>
	<br />
<?php endif; ?>