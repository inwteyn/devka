<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: compose.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<?php if($this->form->isErrors()): ?>

	<?php echo $this->form->setAttrib('class', 'global_form touchform')->render($this) ?>

<?php else: ?>

<script type="text/javascript">
(function(){
	var options = {
		c: 'messageSendTo',
		listType: "all",
		m: 'touch',
		l: 'getFriends',
		p: 1,
		t: 'TOUCH_Search contacts',
		ipp: 3,
		contacts: [],
		params: {'button_label':'<?php echo $this->translate("Add"); ?>'}
	};


	var touchContacts = new HEContacts(options);

	window.messageSendTo = function(toValues){
		touchContacts.options.contacts = toValues;
		$('to').set('text', toValues.length + ' ' + '<?php echo $this->translate('TOUCH_contacts'); ?>');
		$('toValues').value = touchContacts.options.contacts.join(',');
	}

	window.selectedContacts = function(){
		if (touchContacts.options.contacts.length > 0){
			touchContacts.options.params['sort_list'] = touchContacts.options.contacts.join(',');
		}
		touchContacts.box();
	}
})();
</script>

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
		<?php echo $this->form->setAttrib('class', 'global_form touchform')->render($this) ?>
	</div>
</div>
<?php endif;?>