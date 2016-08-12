<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: contact.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<div class="layout_content">
	<?php if( $this->status ): ?>
		<?php echo $this->message; ?>
	<?php else: ?>
		<?php echo $this->form->setAttrib('class','global_form touchform')->render($this) ?>
	<?php endif; ?>
</div>