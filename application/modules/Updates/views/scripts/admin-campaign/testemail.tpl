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
<script type="text/javascript">
	en4.core.runonce.add(function(){
		$('subject').set('value', parent.$('subject').get('value'));
		$('message').set('text', parent.tinyMCE.get('message').getContent());
});
</script>

<div class="global_form_popup">
	<?php echo $this->form->render($this) ?>
</div>