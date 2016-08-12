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
<h4>
    &raquo; <?php echo $this->subject->__toString()?>
</h4>

<div id="navigation_content">
	<div class="layout_content">
		<?php
			echo $this->form->setAttrib('class', 'global_form touchform')->render();
		?>
	</div>
</div>
