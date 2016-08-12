<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: reset.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<div class="layout_content">
	<?php if( empty($this->reset) ): ?>

		<?php echo $this->form->render($this) ?>

	<?php else: ?>

		<div class="tip">
			<span>
				<?php echo $this->translate("Your password has been reset. Click %s to sign-in.", $this->htmlLink(array('route' => 'user_login'), $this->translate('here'))) ?>
			</span>
		</div>

	<?php endif; ?>

</div>