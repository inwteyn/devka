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
	function recipients_operation($recipients, $operation){
    // if demoadmin
    <?php if ($this->engine_admin_neuter): ?>
      alert("Disabled for DEMO users");
    <?php else: ?>
		new Request.JSON({
			'url':'admin/updates/index/recipients',
			'data':{'format':'json', 'recipients':$recipients, 'operation':$operation},
			'onRequest':function() {
				$('loading_'+$recipients).setStyle('display', '');
			},
			'onSuccess':function($resp){
				if ($resp.result == 1){
					$('disable_'+$recipients).setStyle('display', 'none');
					$('enable_'+$recipients).setStyle('display', '');
				} else
				if ($resp.result == 0) {
					$('disable_'+$recipients).setStyle('display', '');
					$('enable_'+$recipients).setStyle('display', 'none');
				}
			},
			'onComplete':function(){
					$('loading_'+$recipients).setStyle('display', 'none');
			}
	}).send();
  <?php endif; ?>
	}
</script>
<?php include 'application/modules/Updates/views/scripts/_submenus.tpl'; ?>
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

<?php // NEWSLETTER CAMPAIGN DASHBOARD ?>
<div class="updates_admin_home_right" style="width: 48%; float: right;">
    <h3 class="sep">
        <span style='font-size: 14px; letter-spacing:0px;'>
        	<?php echo $this->translate('UPDATES_Newsletter Campaign'); ?>
        </span>
    </h3>
    <div class='admin_search' style="clear:none;">
    </div>
    <table cellpadding='0' cellspacing='0' style="width:100%">
    <tr>
     	<td><?php echo $this->translate('UPDATES_VIEWS_SCRIPTS_ADMIN_INDEX_NEWSLETTER_CAMPAIGN')?></td>
		</tr>
		<?php if (count($this->totalActiveCampaigns) > 0 && (int)($this->totalActiveCampaigns[0]['total'] + $this->totalActiveCampaigns[1]['total']) > 0): ?>
		<tr>
			<td width="100%;" style="padding-top:10px">
				<h3 class="sep"  style="height: 10px">
					<span style='font-size: 12px; letter-spacing:0px; padding-top: 3px'>
						<?php echo $this->translate('UPDATES_Active Campaigns'); ?>
					</span>
				</h3>
				<table>
				<tr>
					<td>
						<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'index'), 'admin_default', true); ?>">
							<?php echo $this->translate('UPDATES_Total Active Campaigns'); ?>
						</a>
					</td>
					<td style="text-align:right; padding-left: 10px">
						<?php echo (int)($this->totalActiveCampaigns[0]['total'] + $this->totalActiveCampaigns[1]['total']); ?>
					</td>
				</tr><tr>
					<td>
						<?php echo $this->translate('UPDATES_Instant Active Campaigns'); ?>
					</td>
					<td style="text-align:right; padding-left: 10px">
						<?php echo $this->totalActiveCampaigns[0]['total'] ?>
					</td>
				</tr><tr>
					<td>
						<?php echo $this->translate('UPDATES_Schedule Active Campaigns'); ?>
					</td>
					<td style="text-align:right; padding-left: 10px">
						<?php echo $this->totalActiveCampaigns[1]['total'] ?>
					</td>
				</tr></table>
			</td>
		</tr>
		<?php endif; ?>
		<?php  if (isset($this->totalFutureScheduledCampaigns) && ($this->totalFutureScheduledCampaigns['total']> 0 || $this->nextSendScheduleCampaign->campaign_id || $this->lastSentScheduleCampaign->campaign_id)): ?>
		<tr>
			<td width="100%;" style="padding-top:10px">
				<h3 class="sep"  style="height: 10px">
					<span style='font-size: 12px; letter-spacing:0px; padding-top: 3px'>
						<?php echo $this->translate('UPDATES_Schedule Campaigns'); ?>
					</span>
				</h3>
					<table>
					<?php if ($this->totalFutureScheduledCampaigns['total']>0): ?>
					<tr>
						<td>
							<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'index'), 'admin_default', true); ?>">
								<?php echo $this->translate('UPDATES_Total Future Scheduled Campaigns'); ?>
							</a>
						</td>
						<td style="text-align: right">
							<?php echo $this->totalFutureScheduledCampaigns['total']; ?>
						</td>
					</tr>
					<?php endif; ?>
					<?php if ($this->nextSendScheduleCampaign->campaign_id): ?>
					<tr>
						<td valign="top">
							<?php echo $this->translate('UPDATES_Next Scheduled Campaign'); ?>
						</td>
						<td style="text-align: right; padding-left: 10px">
							<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'edit'), 'admin_default', true) . '/' . $this->nextSendScheduleCampaign->campaign_id; ?>">
								<?php echo Engine_String::substr($this->nextSendScheduleCampaign->subject, 0, 30); if(Engine_String::strlen($this->nextSendScheduleCampaign->subject) > 30) echo '...'; ?>
							</a>
							<div style="clear:both"></div>
							<div style="font-size: 11px; margin-bottom: 3px; float:right"><?php echo $this->translate('UPDATES_Planned Time') . ':'?>&nbsp;<?php echo $this->nextSendScheduleCampaign->planned_date; ?></div>
						</td>
					</tr>
					<?php endif; ?>
					<?php if ($this->lastSentScheduleCampaign->campaign_id): ?>
					<tr>
						<td valign='top'>
							<?php echo $this->translate('UPDATES_Last Sent Scheduled Campaign'); ?>
						</td>
						<td style="text-align: right; padding-left: 10px" align="right">
							<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'edit'), 'admin_default', true) . '/' . $this->lastSentScheduleCampaign->campaign_id; ?>">
								<?php echo Engine_String::substr($this->lastSentScheduleCampaign->subject, 0, 30); if(Engine_String::strlen($this->lastSentScheduleCampaign->subject) > 30) echo '...'; ?>
							</a>
							<div style="clear:both"></div>
							<div style="font-size: 11px; margin-bottom: 3px; float:right"><?php echo $this->translate('UPDATES_Planned Time') . ':'?>&nbsp;<?php echo $this->lastSentScheduleCampaign->planned_date; ?></div>
						</td>
					</tr>
					<?php endif; ?>
				</table>
			</td>
		</tr>
		<?php endif; ?>
		<tr><td style="padding-top: 5px">
			<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'edit'), 'admin_default', true); ?>" style="text-decoration:none">
				<button onclick="location.href='<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'edit'), 'admin_default', true); ?>'"><?php echo $this->translate('UPDATES_Create New Campaign'); ?></button>
			</a>
		</td></tr>
    </table>

    <br />
</div>
<?php // END OF NEWSLETTER CAMPAIGN DASHBOARD ?>


<?php // NEWSLETTER UPDATES DASHBOARD ?>
<div class="updates_admin_home_left" style="width: 48%;">
	<h3 class="sep">
	    <span style='font-size: 14px; letter-spacing:0px;'>
				<?php echo $this->translate('UPDATES_Newsletter Updates') ?> -
				<?php if ($this->mode == 'automatically'): ?>
	      	<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'settings'), 'admin_default') ?>">
						<?php echo $this->translate('UPDATES_Automatically send updates'); ?>
					</a>
				<?php elseif ($this->mode == 'manually'): ?>
					<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'settings'), 'admin_default') ?>">
							<?php echo $this->translate('UPDATES_Manually send updates'); ?>
					</a>
				<?php endif; ?>
	    </span>
		</h3>

	<div class='admin_search' style="clear:none;">
		<?php if ($this->mode == 'automatically'): ?>
			<?php echo $this->translate('UPDATES_VIEWS_SCRIPTS_ADMINGENERAL_AUTOMATICALLYMODE_DESCRIPTION')?>
		<?php elseif ($this->mode == 'manually'): ?>
			<?php echo $this->translate('UPDATES_VIEWS_SCRIPTS_ADMINGENERAL_MANUALLYMODE_DESCRIPTION')?>
		<?php endif; ?>
  </div>
  <br/>
  
	<table cellpadding='0' cellspacing='0' width="100%">
	<tr>
		<td width="100%;" colspan="2" style="padding-top:10px">
		<h3 class="sep" style="height: 10px">
	    	<span style='font-size: 12px; letter-spacing:0px;padding-top: 3px'>
					<?php echo $this->translate('UPDATES_Time Board'); ?>
				</span>
			</h3>
		</td>
	</tr>
	<tr>     	
	 	<td><?php echo $this->translate('UPDATES_Server time')?></td><td style='padding-left: 10px'><?php echo date('M d, Y g:i:s A', strtotime(Engine_Api::_()->updates()->getDatetime())) ?></td>
	</tr>

	<?php if ($this->mode == 'automatically'): ?>
	<tr>     	
	 	<td><?php echo $this->translate('UPDATES_Next send update time')?></td><td style='padding-left: 10px'><?php echo date('M d, Y g:i:s A', (int)$this->next_send_time);?></td>
	</tr>
	<?php endif; ?>

	<tr>
	 	<td><?php echo $this->translate('UPDATES_Last sent update time')?></td><td style='padding-left: 10px'><?php echo date('M d, Y g:i:s A', (int)$this->last_sent_time);?></td>
	</tr>
	<tr>
		<td width="100%;" colspan="2" style="padding-top:10px">
		<h3 class="sep"  style="height: 10px">
	    	<span style='font-size: 12px; letter-spacing:0px;padding-top: 3px'>
					<?php echo $this->translate('UPDATES_Recievers'); ?>
				</span>
			</h3>
		</td>
	</tr>
  <?php if ($this->mailService == 'mailchimp'): ?>
    <tr>
      <td>
        <?php echo $this->translate('UPDATES_Recipients in Mailchimp')?>
        <div style="float:right"><?php echo $this->usersCount; ?></div>
      </td>
    </tr>
  <?php else: ?>
    <tr>
      <td>
        <?php echo $this->translate('UPDATES_Registered recievers (site users) ')?>
        <div style="float:right"><?php echo $this->usersCount; ?></div>
      </td>
      <td style='padding-left: 10px'>
        <a href="javascript:recipients_operation('users', 'enable')" style="color:red;font-weight:bold;<?php if(!$this->users_disabled): ?>display: none;<?php endif; ?>" id="enable_users">
          <?php echo $this->translate("UPDATES_Include 'Registered Recipients'"); ?>
        </a>
        <a href="javascript:recipients_operation('users', 'disable')" style="color:green;<?php if($this->users_disabled): ?>display: none;<?php endif; ?>" id="disable_users">
          <?php echo $this->translate("UPDATES_Exclude 'Registered Recipients'"); ?>
        </a>
        <span id="loading_users" style="display:none"><img src="application/modules/Updates/externals/images/loading.gif" border="0" style="height:12px;vertical-align:bottom;"/></span>
      </td>
    </tr>
    <tr>
      <td>
        <?php echo $this->translate('UPDATES_Subscribed recievers ')?>
        <div style="float:right"><?php echo $this->subscribersCount; ?></div>
      </td>
      <td style='padding-left: 10px;'>
          <a href="javascript:recipients_operation('subscribers', 'enable')" style="color:red;font-weight:bold;<?php if(!$this->subscribers_disabled): ?>display: none;<?php endif; ?>" id="enable_subscribers">
            <?php echo $this->translate("UPDATES_Include 'Subscribers'"); ?>
          </a>
          <a href="javascript:recipients_operation('subscribers', 'disable')" style="color:green;<?php if($this->subscribers_disabled): ?>display: none;<?php endif; ?>" id="disable_subscribers">
            <?php echo $this->translate("UPDATES_Exclude 'Subscribers'"); ?>
          </a>
          <span id="loading_subscribers" style="display:none"><img src="application/modules/Updates/externals/images/loading.gif" border="0" style="height:12px;vertical-align:bottom;"/></span>
      </td>
    </tr>
  <?php endif; ?>

	</table>

	<?php if ($this->mode == 'manually'): ?>
  <div style='padding: 10px;'>
		<a href="<?php echo $this->url(array('action' => 'preview', 'controller' => 'layout', 'module' => 'updates'), 'admin_default', true)?>" target="blank" >
  	<button type="button" ><?php echo $this->translate('UPDATES_Preview'); ?></button>
 		</a>
		<a href="<?php echo $this->url(array('action' => 'send', 'controller' => 'index', 'module' => 'updates'), 'admin_default', true)?>" class="smoothbox">
  	<button type="button" ><?php echo $this->translate('UPDATES_Send Update'); ?></button>
 		</a>
  </div>
	<?php endif; ?>
</div>
<?php // END OF NEWSLETTER UPDATES DASHBOARD ?>

<div style="clear: both"></div>
<br/>

<div class="updates_admin_home_left" >
  <h3 class="sep">
   <span style='font-size: 14px; letter-spacing:0px;'>
     <?php echo $this->translate('UPDATES_Dashboard') ?>
   </span>
  </h3>
	<div class='admin_search' style="clear:none;">
		<?php echo $this->translate('UPDATES_REFERENCE_DESCRIPTION')?>
   </div>
</div>