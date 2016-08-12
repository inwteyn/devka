<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-07-13 16:01 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>


<script type="text/javascript">

en4.core.runonce.add(function()
{
	$$('#button_addmore_id').removeEvents('click').addEvent('click', function()
	{
		var $topic = $('subForm_0').clone();
		$topic.getElement("input[name='extra_0[topic_name]']").setProperty('value', '');
		$topic.getElement("textarea[name='extra_0[emails]']").setProperty('value', '');

		var $forms = $$('.subForm_class');
		$topic.set('id', 'subForm_' + $forms.length).inject($forms[$forms.length-1],'after');

		var $newSubForm = $$('#subForm_' + $forms.length);
		$newSubForm.getElement("input[name='extra_0[topic_name]']").setProperty('name', 'extra_' + $forms.length + '[topic_name]');
		$newSubForm.getElement("textarea[name='extra_0[emails]']").setProperty('name', 'extra_' + $forms.length + '[emails]');
		$newSubForm.getElement("button[name='extra_0[add_more]']").set('text', 'Delete');
		$newSubForm.getElement("button[name='extra_0[add_more]']").setProperty('onClick', 'deleteTopic(' + $forms.length + ')');
		$newSubForm.getElement("button[name='extra_0[add_more]']").setProperty('name', 'extra_' + $forms.length + '[delete]');
		$newSubForm.getElement("input[name='extra_0[topic_id]']").setProperty('value', 0);
		$newSubForm.getElement("input[name='extra_0[topic_id]']").setProperty('name','extra_' + $forms.length + '[topic_id]');

		var $form_label = $$('#subForm_' + $forms.length + ' .form-label');
		for (var i = 0; i < $form_label.length; i++)
		{
			$form_label[i].setProperty('id', 'form-label_id_' + i);
		}
	});
});


function deleteTopic(i)
{
	var topicNameValue = $('subForm_'+ i).getElement("input[name='extra_"+ i +"[topic_name]']").getProperty('value');
	var emailsValue = $('subForm_'+ i).getElement("textarea[name='extra_"+ i +"[emails]']").getProperty('value');
	var confirmDelete;

	try	{
		if (topicNameValue != '' || (emailsValue != ''))	{
			confirmDelete = confirm("<?php echo $this->translate('PAGECONTACT_Are you sure you want to delete the topic?');?>");
		}
		else	{
			confirmDelete = true;
		}
	}
	catch(err)	{
		confirmDelete = true;
	}

	if (!confirmDelete)	{
		return;
	}

	var topic_id = $$('#subForm_' + i).getElement("input[name='extra_" + i +"[topic_id]']").getProperty('value');

	if(topic_id == 0)
	{
		$$('#subForm_' + i).destroy();

		var m = 1;
		var $subForms = $$('.subForm_class');
		for(var k = 1; k <= $subForms.length; k++)
		{
			var $subForm = $('subForm_'+ k);

			if($subForm != undefined)
			{
				$subForm.set('id', 'subForm_' + m);
				$subForm.getElement("input[name='extra_"+ k +"[topic_name]']").setProperty('name', 'extra_' + m + '[topic_name]');
				$subForm.getElement("textarea[name='extra_"+ k +"[emails]']").setProperty('name', 'extra_' + m + '[emails]');
				$subForm.getElement("button[name='extra_"+ k +"[delete]']").setProperty('name','extra_'+ m + '[delete]');
				$subForm.getElement("button[name='extra_"+ m +"[delete]']").setProperty('onclick', 'deleteTopic('+ m +')');
				$subForm.getElement("input[name='extra_"+ k +"[topic_id]']").setProperty('name', 'extra_' + m + '[topic_id]');

				m++;
			}
		}
	}
	else
	{
		var request = new Request.JSON(
		{
			secure: false,
			url: '<?php  echo $this->url(array("module" => "pagecontact", "controller" => "index", "action" => "delete"), "default", true)?>',
			method: 'post',
			data: {'format': 'json', 'topic_id' : topic_id, 'page_id' : <?php echo $this->page_id; ?> },
			onSuccess: function()
			{
				$$('#subForm_' + i).destroy();

				var m = 1;
				var $subForms = $$('.subForm_class');
				for(var k = 1; k <= $subForms.length; k++)
				{
					var $subForm = $('subForm_'+ k);

					if($subForm != undefined)
					{
						$subForm.set('id', 'subForm_' + m);
						$subForm.getElement("input[name='extra_"+ k +"[topic_name]']").setProperty('name', 'extra_' + m + '[topic_name]');
						$subForm.getElement("textarea[name='extra_"+ k +"[emails]']").setProperty('name', 'extra_' + m + '[emails]');
						$subForm.getElement("button[name='extra_"+ k +"[delete]']").setProperty('name','extra_'+ m + '[delete]');
						$subForm.getElement("button[name='extra_"+ m +"[delete]']").setProperty('onclick', 'deleteTopic('+ m +')');
						$subForm.getElement("input[name='extra_"+ k +"[topic_id]']").setProperty('name', 'extra_' + m + '[topic_id]');

						m++;
					}
				}
			}
		}).send();
	}
}
</script>

<div class="page_edit_contact">
  <?php echo $this->adminContactForm->render($this); ?>
</div>