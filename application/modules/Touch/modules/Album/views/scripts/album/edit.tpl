<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-04-26 11:18:13 mirlan $
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
	<div class="layout_content">
		<?php
			echo $this->form->setAttrib('class', 'global_form touchform')->render();
		?>
	</div>
</div>
