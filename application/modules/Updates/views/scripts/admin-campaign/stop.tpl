<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>
<div class="global_form_popup">
	<div>
		<?php echo $this->translate("UPDATES_Delete campaign '%s'?", $this->template->subject); ?>
	</div>
	<?php echo $this->form->render($this) ?>
</div>