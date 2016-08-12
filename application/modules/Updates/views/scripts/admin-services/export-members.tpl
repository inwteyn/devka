<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: export-members.tpl 2012-04-17 14:58 ratbek $
 * @author     Ratbek
 */
?>

<link type="text/css" rel="stylesheet" href="application/modules/Updates/externals/styles/main.css"/>

<script type="text/javascript">
en4.core.runonce.add(function()
{
  $('export_question').inject('export_members','before');
});
</script>

<div class="export_members_container">
  <?php if ($this->api_key_error == 'error'): ?>
    <ul id="mailchimp_api_key_error" class="form-errors">
      <li>
        <ul class="errors">
          <li id="mailchimp_error_message">
            <?php echo $this->translate("UPDATES_Invalid api key! Please check it and try again."); ?>
          </li>
        </ul>
      </li>
    </ul>
    <div id="close_link">
      <a href="javascript:void(0);" onclick="parent.Smoothbox.close()">Close</a>
    </div>
  <?php else: ?>
    <div id="export_question"><?php echo $this->translate("UPDATES_Would you like to export members to list '%s' in Mailchimp?", $this->listName); ?></div>
    <?php echo $this->exportMembersForm->render($this); ?>
  <?php endif; ?>

</div>