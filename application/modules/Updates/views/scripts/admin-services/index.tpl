<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-14 15:48 ratbek $
 * @author     Ratbek
 */
?>
<link type="text/css" rel="stylesheet" href="application/modules/Updates/externals/styles/main.css"/>
<?php include 'application/modules/Updates/views/scripts/_submenus.tpl'; ?>

<script type="text/javascript">

en4.core.runonce.add(function()
{
  var mailService = '<?php echo $this->mailService; ?>';
  var $elemServices = $('services');

  $$('.mailchimp_container #periodTime-label').set('html', '<?php echo $this->translate('UPDATES_Period and Time:'); ?>');
  $$('.mailchimp_settings_form .form-description').setProperty('id', 'mailchimp_form_description');
  $('mailchimp_api_key_error').inject('mailchimp_form_description','after');

  for (var i=0; i < $elemServices.length; i++) {
    if ($elemServices.options[i].value == mailService) {
      $elemServices.options[i].selected = true;
    }
  }

  if (mailService == 'socialengine') {
    $$('.socialengine_container').setStyle('display', '');
    $('current_mail_service').set('html','SocialEngine');
  }
  else if (mailService == 'mailchimp') {
    $$('.mailchimp_container').setStyle('display', '');
    $('current_mail_service').set('html','MailChimp');
    var existList = '<?php echo $this->existList; ?>';


    if (existList == '1') {
      $('export_members').setStyle('background-color', '#619DBE');
      $('export_members').setStyle('border-color', '#619DBE');
    }
    else {
      $('export_members').setStyle('background-color', '#868686');
      $('export_members').setStyle('border-color', '#868686');
    }
  }
  else if (mailService == 'sendgrid') {
    $$('.sendgrid_container').setStyle('display', '');
    $('current_mail_service').set('html','SendGrid');
  }

  $('list_name_description').set('html', '<?php echo $this->translate("UPDATES_LIST_NAME_DESCRIPTION");?>');
});

function changeService()
{
  var $elemServices = $('services');
  var selectedMailService = $elemServices.options[$elemServices.selectedIndex].value;
  if (selectedMailService == 'socialengine') {
    $$('.socialengine_container').setStyle('display', '');
    $$('.mailchimp_container').setStyle('display', 'none');
    $$('.sendgrid_container').setStyle('display', 'none');
  }
  else if (selectedMailService == 'mailchimp') {
    $$('.mailchimp_container').setStyle('display', '');
    $$('.socialengine_container').setStyle('display', 'none');
    $$('.sendgrid_container').setStyle('display', 'none');
  }
  else if (selectedMailService == 'sendgrid') {
    $$('.sendgrid_container').setStyle('display', '');
    $$('.mailchimp_container').setStyle('display', 'none');
    $$('.socialengine_container').setStyle('display', 'none');
  }
}

function setMailService()
{
  // if demoadmin
  <?php if ($this->engine_admin_neuter): ?>
    alert("Disabled for DEMO users");
    return false;
  <?php endif; ?>
  var $elemServices = $('services');
  var selectedMailService = $elemServices.options[$elemServices.selectedIndex].value;

  var $request = new Request.JSON(
  {
    secure: false,
    url: '<?php echo $this->url(array("module" => "updates", "controller" => "services", "action" => "set-mail-service"), "admin_default", true)?>',
    method: 'post',
    data: {
      'format': 'json',
      'mailService': selectedMailService
    },
    'onRequest':function(){
			$('mailservice_loading').setStyle('display', '');
		},
    onSuccess: function(response) {
      if (response.set == 'Success') {
        if (response.mailService == 'socialengine') {
          $('current_mail_service').set('html','SocialEngine');
        }
        if (response.mailService == 'mailchimp') {
          $('current_mail_service').set('html','MailChimp');
          if (response.existList) {
            $('export_members').setStyle('background-color', '#619DBE');
            $('export_members').setStyle('border-color', '#619DBE');
            $('export_members').setProperty('onclick', 'exportMembers(1)');
          }
          else {
            $('export_members').setStyle('background-color', '#868686');
            $('export_members').setStyle('border-color', '#868686');
            $('export_members').setProperty('onclick', 'exportMembers(0)');
          }
        }
        if (response.mailService == 'sendgrid') {
          $('current_mail_service').set('html','SendGrid');
        }
      }
    },
    'onComplete':function(){
			$('mailservice_loading').setStyle('display', 'none');

      $('ok_icon').setStyle('opacity','1');
        setTimeout(function() {
          $('ok_icon').set('tween', {duration : 1000});
          $('ok_icon').tween('opacity', 0);
        }, 3000);
		}
  }).send();
}

function getListID()
{
  var api_key = $('api_key').get('value');
  var list_name = $('list_name').get('value');

  if (api_key == '') {
    $('mailchimp_api_key_error').setStyle('display', '');
    $('mailchimp_error_message').set('html','<?php echo $this->translate('UPDATES_Api key is empty! Please fill in it and try again.')?>');
    return;
  }
  else {
    $('mailchimp_api_key_error').setStyle('display', 'none');
  }

  if (list_name == '') {
    $('mailchimp_api_key_error').setStyle('display', '');
    $('mailchimp_error_message').set('html','<?php echo $this->translate('UPDATES_List name is empty! Please fill in it and try again.')?>');
    return;
  }
  else {
    $('mailchimp_api_key_error').setStyle('display', 'none');
  }

  var request = new Request.JSON (
  {
    secure: false,
    url: '<?php  echo $this->url(array("module" => "updates", "controller" => "services", "action" => "get-list-id"), "admin_default", true)?>',
    method: 'post',
    data: {
      'format': 'json',
      'api_key' : api_key,
      'list_name' : list_name
    },
    'onRequest':function() {
			$('getListID_loading').setStyle('visibility', 'visible');
		},
    onSuccess: function(response) {
      if (response.api_key_error == 'error') {
        $('mailchimp_api_key_error').setStyle('display', '');
        $('mailchimp_error_message').set('html','<?php echo $this->translate('UPDATES_Invalid api key! Please check it and try again.')?>');
      }
      else if (response.getList_error == 'error') {
        $('mailchimp_api_key_error').setStyle('display', '');
        $('mailchimp_error_message').set('html','<?php echo $this->translate('UPDATES_Error: Unable to load required list!')?>'+response.getList_error_details);
      }
      else if (response.total <= 0) {
        $('mailchimp_api_key_error').setStyle('display', '');
        $('mailchimp_error_message').set('html','<?php echo $this->translate('UPDATES_Not found required list. Please check list name.')?>');
      }
      else {
        var lists_id = response.list_id;
        $('list_id').setProperty('value', lists_id);
        $('mailchimp_api_key_error').setStyle('display', 'none');
      }
    },
    'onComplete':function() {
			$('getListID_loading').setStyle('visibility', 'hidden');
		}
  }).send();
}

function saveSync()
{
  var mode = $('mode').getProperty('value');
  var period = $('period').getProperty('value');
  var hour = $('hour').getProperty('value');
  var minute = $('minute').getProperty('value');
  var am_pm = $('am_pm').getProperty('value');

  var request = new Request.JSON (
  {
    secure: false,
    url: '<?php  echo $this->url(array("module" => "updates", "controller" => "services", "action" => "save-sync"), "admin_default", true)?>',
    method: 'post',
    data: {
      'format': 'json',
      'mode' : mode,
      'period' : period,
      'hour' : hour,
      'minute' : minute,
      'am_pm' : am_pm
    },
    'onRequest':function() {
			$('save_sync_loading').setStyle('display', '');
		},
    onSuccess: function() {
      $('sync_form_notices').setStyle('display', '');
    },
    'onComplete':function() {
			$('save_sync_loading').setStyle('display', 'none');
		}
  }).send();
}

function generateListName()
{
  var request = new Request.JSON (
  {
    secure: false,
    url: '<?php  echo $this->url(array("module" => "updates", "controller" => "services", "action" => "generate-list-name"), "admin_default", true)?>',
    method: 'post',
    data: {
      'format': 'json'
    },
    'onRequest':function() {
			$('generateListName_loading').setStyle('display', '');
		},
    onSuccess: function(response) {
      $('list_name').setProperty('value', response.listName);
    },
    'onComplete':function() {
			$('generateListName_loading').setStyle('display', 'none');
		}
  }).send();
}

function exportMembers(existList)
{
  if (existList) {
    var $el = new Element('a', {'href': '<?php echo $this->url(array("module"=>"updates", "controller"=>"services", "action"=>"export-members"), "admin_default", true); ?>', 'class': 'smoothbox'});
    Smoothbox.open($el);
  }
}

</script>

<div>
<h2><?php echo $this->translate("UPDATES_Newsletter Updates Plugin") ?></h2>

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

<div class='services_container'>
  <h3><?php echo $this->translate("UPDATES_Mail Services");?></h3>
  <p><?php echo $this->translate("UPDATES_FORM_ADMIN_SERVICES_DESCRIPTION");?></p>
  <div class="select_container">
    <p class="current_mail_service_text"><?php echo $this->translate("UPDATES_Current mail service:");?></p>
    <div class="current_mail_service">
      <span id="current_mail_service" >Mail Service</span>
      <img id="mailservice_loading" src="application/modules/Updates/externals/images/loading.gif" border="0px" title="<?php echo $this->translate('Loading...'); ?>" style="display: none; margin-left: 12px"/>
      <img id="ok_icon" class="ok_icon" src="application/modules/Updates/externals/images/ok.png" alt="Successfully saved" title="<?php echo $this->translate('UPDATES_Successfully saved'); ?>">
    </div>
    <select id="services" onchange="changeService()" name="services">
      <option label="SocialEngine" value="socialengine">SocialEngine</option>
      <option label="MailChimp" value="mailchimp">MailChimp</option>
      <option label="SendGrid" value="sendgrid">SendGrid</option>
    </select>
    <button id="set_mailservice" type="button" onclick="setMailService()" name="set_mailservice" ><?php echo $this->translate("UPDATES_Set this mail service"); ?></button>
  </div>
  <br><br>

  <div class="socialengine_container" style="display: none">
    <a target="_blank" href="<?php $_SERVER['HTTP_HOST']?>/admin/mail/settings"><?php echo $this->translate("UPDATES_SocialEngine Mail Settings");?></a>
    <ul id="socialengine_api_key_error" class="form-errors" style="display: none;">
      <li>
        <ul class="errors">
          <li id="socialengine_error_message">
            Displays error messages
          </li>
        </ul>
      </li>
    </ul>
  </div>

  <div class="mailchimp_container" style="display: none">
    <button id="export_members" onclick="exportMembers(<?php echo $this->existList; ?>)" type="button" name="export_members"><?php echo $this->translate("UPDATES_Export members to Mailchimp");?></button>
    <div class="mailchimp_settings_form">
      <div class="mailchimp_inner_settings_form">
        <ul id="mailchimp_api_key_error" class="form-errors" style="display: none;">
          <li>
            <ul class="errors">
              <li id="mailchimp_error_message">
                Displays error messages
              </li>
            </ul>
          </li>
        </ul>
        <?php echo $this->mailChimpForm->render($this); ?>
      </div>
    </div>
  </div>

  <div class="sendgrid_container" style="display: none">
    <div class="settings_form">
      <div class="inner_settings_form">
        <ul id="sendgrid_api_key_error" class="form-errors" style="display: none;">
          <li>
            <ul class="errors">
              <li id="sendgrid_error_message">
                Displays error messages
              </li>
            </ul>
          </li>
        </ul>
        <?php echo $this->sendGridForm->render($this); ?>
      </div>
    </div>
  </div>
</div>