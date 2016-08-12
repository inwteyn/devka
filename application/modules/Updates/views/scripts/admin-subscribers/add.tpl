<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>

<script type='text/javascript'>

en4.core.runonce.add(function()
{
	var $i = 2;
	
	$('add_more').addEvent('click', function(){
		$div_name = new Element('div',{
			'id':'div_name_'+$i
		});
		$name = new Element('input', {
			'type':'text',
			'name':'name'+$i,
			'id':'name'+$i,
			'style':'margin-top:5px'
		});
		$div_name.grab($name);
		
		$div_email = new Element('div',{
			'id':'div_email_'+$i
		});
		$email = new Element('input', {
			'type':'text',
			'name':'email_address'+$i,
			'id':'email_address'+$i,
			'style':'margin-top:5px'
		});
		$div_email.grab($email);
		
		$a = new Element('a', {
			'href':'javascript://',
			'id': $i
		});
		$a.set('text', ' - remove');
		$a.addEvent('click', function(){
				$id = $(this).getProperty('id');
				$('div_name_'+$id).destroy();
				$('div_email_'+$id).destroy();
				$(this).destroy();
			});
		$div_email.grab($a);
		
		$('name_div').grab($div_name);
		$('email_div').grab($div_email);
		$i++;
	});
});
</script>
<div style='height: 350px; width: 400px'>
	<div class="global_form_popup">
	  <?php echo $this->form->setAttrib('class', '')->render($this) ?>
	</div>
</div>
