<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: forgot.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<div class="layout_content">
	<?php if( empty($this->sent) ): ?>

		<?php echo $this->form->render($this) ?>

	<?php else: ?>

		<div class="tip">
			<span>
				<?php echo $this->translate("USER_VIEWS_SCRIPTS_AUTH_FORGOT_DESCRIPTION") ?>
			</span>
		</div>

	<?php endif; ?>

</div>