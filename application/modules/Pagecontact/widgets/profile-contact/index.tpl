<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-07-13 16:01 ratbek $
 * @author     Ratbek
 */
?>

<script type="text/javascript">

	en4.core.runonce.add(function()
  {
    $$('.btn_send_class').removeEvents('click').addEvent('click', function()
		{
			var $contactForm = $('page_edit_form_contact');
			var page_id = $contactForm.getElement('input[name="page_id"]').getProperty('value');
			var topic_id = $contactForm.getElement('select[name="topic"]').getProperty('value');
			var topicValue = $contactForm.getElement('select[name="topic"]').getProperty('value');
			var subjectValue = $contactForm.getElement('input[name="subject"]').getProperty('value');
			var messageValue = $contactForm.getElement('textarea[name="message"]').getProperty('value');

			var senderNameValue = '';
			var senderEmailValue = '';
			var visitorValue = $contactForm.getElement('input[name="visitor"]').getProperty('value');
			if (visitorValue == 1)
			{
				senderNameValue = $contactForm.getElement('input[name="sender_name"]').getProperty('value');
				senderEmailValue = $contactForm.getElement('input[name="sender_email"]').getProperty('value');
			}

			if (topicValue == 0) {
				he_show_message("<?php echo $this->translate('PAGECONTACT_Topic field is empty. Please full fill topic and try again.');?>",'error', 5000);
				return;
			}

			if (visitorValue == 1)
			{
				if	(senderNameValue === '') {
					he_show_message("<?php echo $this->translate('PAGECONTACT_User name field is empty. Please full fill user name and try again.');?>",'error', 5000);
					return;
				}

				if	(senderEmailValue === '') {
					he_show_message("<?php echo $this->translate('PAGECONTACT_Email field is empty. Please full fill email and try again.');?>",'error', 5000);
					return;
				}
			}

			if (subjectValue === '') {
				he_show_message("<?php echo $this->translate('PAGECONTACT_Subject field is empty. Please full fill subject and try again.');?>",'error', 5000);
				return;
			}
			if	(messageValue === '') {
				he_show_message("<?php echo $this->translate('PAGECONTACT_Message field is empty. Please fill message and try again.');?>",'error', 5000);
				return;
			}



			$$('.btn_send_class')[0].disabled= true;

			var request = new Request.JSON(
			{
				secure: false,
				url: '<?php echo $this->url(array("module" => "pagecontact", "controller" => "index", "action" => "send"), "default", true)?>',
				method: 'post',
				data: {'format': 'json', 'page_id' : page_id, 'topic_id' : topic_id, 'subject' : subjectValue, 'message' : messageValue,
								'visitor' : visitorValue, 'sender_name' : senderNameValue, 'sender_email' : senderEmailValue },
				onSuccess: function()
				{
					he_show_message("<?php echo $this->translate('PAGECONTACT_Message has been sent successfully.'); ?>",'',5000);
					$contactForm.reset();
					$$('.btn_send_class')[0].disabled= false;
				}
			}).send();

    });
  });

</script>

<?php echo $this->contactForm->render($this); ?>
