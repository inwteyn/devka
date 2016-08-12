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
<?php include 'application/modules/Updates/views/scripts/_submenus.tpl'; ?>
<link type="text/css" rel="stylesheet" href="application/modules/Updates/externals/styles/campaign.css"/>
		
<script type="text/javascript">

var delete_template = function($id)
{
  // if demoadmin
  <?php if ($this->engine_admin_neuter): ?>
    alert("Disabled for DEMO users");
  <?php else: ?>
    if (confirm('<?php echo $this->translate("UPDATES_Delete this template?"); ?>'))
    {
      new Request.JSON({
        'url':"<?php echo $this->url(array('action' => 'template', 'controller' => 'campaign', 'module' => 'updates'), 'admin_default', true); ?>",
        'method':'post',
        'data':{'format':'json', 'task': 'delete', 'template_id':$id},
        'onRequest':function(){
          $('delete_'+$id).setStyle('display', 'none');
          $('loading_'+$id).setStyle('display', '');
        },
        'onSuccess':function($resp){
          if($resp.success == 1) {
            $('template_'+$id).fade('out');
            setTimeout(function(){$('template_'+$id).destroy()}, '500');
          }
        }
      }).send();
    }
  <?php endif; ?>
	return false;
}

var testemail = function()
{
	$subject = $('subject').get('value');
	$message = tinyMCE.get('message').getContent();


	if ($subject.trim() == '') {
		alert('<?php echo $this->translate("UPDATES_Subject field is required!!!"); ?>)');
		$('subject').focus();
		return false;
	}

	if ($message.trim() == '') {
		alert('<?php echo $this->translate("UPDATES_Message content is required!!!"); ?>)');
		$('message').focus();
		return false;
	}

  var $el = new Element('a', {'href': '<?php echo $this->url(array("module"=>"updates", "controller"=>"campaign", "action"=>"testemail"), "admin_default", true); ?>', 'class': 'smoothbox'});
  Smoothbox.open($el);
}

var refresh_recipients = function() {
	var recipients = {
    'subscribers': ($('subscribers').get('checked'))?1:0,
    'users':$('users').get('value'),
    'profile_photo':$('profile_photo').get('value'),
    'last_logged_count': $('last_logged_count').get('value'),
    'last_logged_type': $('last_logged_type').get('value')
  };

	switch(recipients.users)
	{
		case 'all_users':
			break;

		case 'member_levels':
      if ($('member_levels').selectedIndex > -1 ) {
        recipients.member_levels = $('member_levels').options[$('member_levels').selectedIndex].get('value');
        recipients.isFromEdit = 1;
      }
		break;

		case 'networks':
      if ($('networks').selectedIndex > -1 ) {
        recipients.networks = $('networks').options[$('networks').selectedIndex].get('value');
        recipients.isFromEdit = 1;
      }
		break;

		case 'profile_types':
      if ($('profile_types').selectedIndex > -1 ) {
        recipients.profile_types = $('profile_types').options[$('profile_types').selectedIndex].get('value');
        recipients.isFromEdit = 1;
      }
		break;

		case 'custom':
			//recipients.member_levels = $('member_levels').getChildren("option[selected]").get('value');
			recipients.member_levels = $('member_levels').getChildren("option:selected").get('value');
			recipients.networks = $('networks').getChildren("option:selected").get('value');
			recipients.profile_type = $('profile_type').get('value');

      var option_id = $('profile_type').get('value');
      if (option_id != '')
      {
        $$('.browsemembers_criteria > ul').setStyle('display','block');
        var $fieldsParent = $$('.option_' + option_id).getParent();
        $fieldsParent.setStyle('display','block');
        var $fieldsParentParent = $$('.option_'+option_id).getParent().getParent();
        $fieldsParentParent.setStyle('display','block');
        var $fieldsParentParentParent = $$('.option_'+option_id).getParent().getParent().getParent();
        $fieldsParentParentParent.setStyle('display','block');

        var $fields = $$('.option_'+recipients.profile_type).get('id');
        var $fields_id = new Array();
        var $fields_value = new Array();
        var fieldParentParent = '';
        var fieldParent = '';
        var j = -1;

        for (var i=0; i < $fields.length; i++) {
          if ($fields[i] !== null) {
            j++;
            if ($($fields[i]).get('type') == 'radio') {
              if ($($fields[i]).get('checked')) {
                $fields_id[j] = $fields[i];
                $fields_value[j] = $($fields[i]).get('value');
              }
            }
            else if($($fields[i]).get('type') == 'checkbox') {
              if ($($fields[i]).get('checked'))  {
                $fields_id[j] = $fields[i];
                $fields_value[j] = $($fields[i]).get('value');
              }
            }
            else {
              $fields_id[j] = $fields[i];
              $fields_value[j] = $($fields[i]).get('value');
            }

            if ($($fields[i]).get('onchange') === 'changeFields();') {
              $($fields[i]).removeProperty('onchange');
            }

            if ($($fields[i]).get('tag') === 'textarea') {
              $($fields[i]).getParent().destroy();
            }
          }
        }
      }

      recipients.fields_id = $fields_id;
      recipients.fields_value = $fields_value;

		default:
			break;
	}

  // if demoadmin
  <?php if ($this->engine_admin_neuter): ?>
    alert("Some functions are disabled for DEMO users");
  <?php else: ?>
    new Request.JSON({
      'url':"<?php echo $this->url(array('action' => 'refresh-recipients', 'controller' => 'campaign', 'module' => 'updates'), 'admin_default', true); ?>",
      'method':'post',
      'data':{'format':'json', 'recipients':recipients},
      'onRequest':function(){
        $('loading_refresh_recipients').setStyle('display', '');
      },
      'onSuccess':function($resp){
        if($resp.success == 1){
          if ($resp.mailService == 'mailchimp') {
            $$("#recipients-label > .optional").set('html', '<?php echo $this->translate('UPDATES_Total Recipients in Mailchimp:'); ?>');
          }
          $('total_recipients').set('text', $resp.total_recipients);
          $('recipients_qty').set('value', $resp.total_recipients);
        }
      },
      'onComplete':function(){
        $('loading_refresh_recipients').setStyle('display', 'none');
      }
    }).send();
  <?php endif; ?>
}

var changeRecievers = function ($el){
	$$('.recievers_select_item').setStyle('display', 'none');
	$('profile_type_select').setStyle('display', 'none');
	$$('.profile_type_options').setStyle('display', 'none');

	if($el.value == 'custom'){
		$('recievers_select').setStyle('display', 'none');
		$$('.recievers_select_item').setStyle('display', 'block');
		$('profile_type_select').setStyle('display', 'block')
		$('profile_types_select').setStyle('display', 'none');

		$$('.'+$('profile_type').value+'_options').setStyle('display', 'block');

		$('recievers_select').setStyle('display', 'block');
	} else	if ($el.value == 'all_users'){
		$('recievers_select').setStyle('display', 'none');
	}else if ($el.value == 'profile_types'){
		$('recievers_select').setStyle('display', 'none');
		$('profile_types_select').setStyle('display', 'block');
	}else {
		$('recievers_select').setStyle('display', 'block');
		$($el.value+'_select').setStyle('display', 'block');
	}
}

en4.core.runonce.add(function() {
  // fixing bug in IE
  if ($$('.settings > div').length != 0) {
    $$('.settings > div').setProperty('id', 'phantom_div');
    $$('.browsemembers_criteria').setProperty('id', 'browsemembers_criteria');
    $('phantom_div').inject('browsemembers_criteria', 'after');
    $('phantom_div').setStyle('float', 'left');
  }

	$$('.count_recipients, .field_toggle').addEvent('change', function(){
    // hide all fields
    var $fieldsParent = $$('.field_toggle').getParent();
    $fieldsParent.setStyle('display','none');

    var $fieldsParent_x3 = $$('.field_toggle').getParent().getParent().getParent();
    $fieldsParent_x3.setStyle('display','none');

		refresh_recipients();
	});

	$('users-element').grab($('include_subscribers'), 'bottom');
	$('include_subscribers').grab($('include_subscribers').getElement('label'), 'bottom');
	$('recievers_select-element').adopt($$('.profile_ages'));
	var $els = $$('.profile_ages').getElements('.form-label');
	for(var i in $els){
		if ($els[i].set != undefined){
			$els[i].set('html', '<label>' + "<?php echo $this->translate('UPDATES_Age'); ?>" + '</label>');
		}
	}

  $('last_logged_count-element').grab($('last_logged_type-element'), 'bottom');
  $('last_logged_type-element').grab($('last_logged_type-element').getElement('label'), 'bottom');

	if ($('campaign_type').value == 'schedule'){
		$('planned_date_conteiner').setStyle('display', '');
	}

	changeRecievers($('users'));
	refresh_recipients();

	var $labels = $$('.custom_selectors').getElement('label');
	for(var i in $labels) {
		if ($labels[i] !=undefined){
			var $div = new Element('div', {'id':'div_'+$labels[i].getProperty('for'), 'class': 'div_custom_labels'});
			$($labels[i].getProperty('for')+'_select').grab($div.grab($labels[i]), 'top');
		}
	}
});
  var cleanHiddenFields = function() {
    if(confirm("<?php echo $this->translate("UPDATES_Are you sure you want to send this campaign?"); ?>")) {
      var $hide_fields = $$('.field_toggle');
      var $els = new Array();
      for(var i=0; i < $hide_fields.length; i++) {
        if($hide_fields[i].getParent().getStyle('display') == 'none'){
          $hide_fields[i].destroy();
        }
      }
      return true;
    }

    return false;
  }
  var changeCampaignType = function() {
    if ($('campaign_type').value == 'schedule') {
      $('planned_date_conteiner').setStyle('display', '');
      $('submit').set('html', '<?php echo $this->translate('UPDATES_Save Changes'); ?>');
      $('campaign_form').removeProperty('onsubmit');
    }
    else {
      $('planned_date_conteiner').setStyle('display', 'none');
      $('submit').set('html', '<?php echo $this->translate('UPDATES_Send Campaign'); ?>');
      $('campaign_form').setProperty('onsubmit','return cleanHiddenFields()');
    }
  }
</script>

<div>
<h2><?php echo $this->translate("Newsletter Updates Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
</div>

<div class='clear'>
	<div style="float:left">
	<div style='padding: 10px'>
		<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'index'), 'admin_default', true);?>" style='text-decoration: none'>
		<button onclick="location.href = '<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'index'), 'admin_default', true);?>' "><?php echo $this->translate('UPDATES_Back to Campaign Manager'); ?></button>
		</a>
	</div>

	<?php if($this->templates->count() > 0): ?>
	<h3 style="padding:10px; padding-bottom: 2px"><?php echo $this->translate('UPDATES_Available Templates'); ?></h3>
		<div class="templates_cont">
			<?php foreach ($this->templates as $template): ?>
				<div class="template_item" id="template_<?php echo $template->template_id; ?>">
					<?php echo Engine_String::substr($template->subject, 0, 20); ?>
					<?php if (Engine_String::strlen($template->subject)>=20): echo '...'; endif; ?>

					<div class="options">
						<?php if(!$template->hasCampaign()): ?>
							<a href="javascript://" onclick="delete_template(<?php echo $template->template_id; ?>, 'delete')">
								<img id="delete_<?php echo $template->template_id; ?>" src="application/modules/Updates/externals/images/delete.gif" border="0px" title="<?php echo $this->translate('UPDATES_Delete Template'); ?>"/>
								<img id="loading_<?php echo $template->template_id; ?>" src="application/modules/Updates/externals/images/loading.gif" border="0px" title="<?php echo $this->translate('Loading...'); ?>" style="display:none"/>
							</a>
						<?php endif; ?>
						<a class='smoothbox' href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'template', 'task'=>'preview', 'template_id'=>$template->template_id), 'admin_default', true); ?>">
							<img id="preview_<?php echo $template->template_id; ?>" src="application/modules/Updates/externals/images/preview.jpg" border="0px" title="<?php echo $this->translate('UPDATES_Preview Template'); ?>"/>
						</a>
					</div>

					<div class="description">
						<?php if (Engine_String::strlen($template->description) >0):?>
							<?php echo $this->translate('Description') . ': ' . $template->description . '<br/>'; ?>
						<?php endif;?>
						<?php echo $this->translate('Created') . ': ' . $this->locale()->toDateTime($template->creation_date, array('size'=>'short')); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	</div>
	
  <div class='settings' style="position: absolute; overflow: visible; width: 700px; margin-left: 240px;">
		<?php echo $this->form->render($this); ?>
	</div>
</div>