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
	var active_paginator = new paginator(<?php echo Zend_Json::encode($this->active_paginator_pages)?>);
	var schedule_paginator = new paginator(<?php echo Zend_Json::encode($this->schedule_paginator_pages)?>);
	var sent_paginator = new paginator(<?php echo Zend_Json::encode($this->sent_paginator_pages)?>);
	
	var cancel_campaign = function(subject, id){
		if (confirm('<?php echo $this->translate("UPDATES_The campaign will be deleted after cancelling.")?>\r\n <?php echo $this->translate("Cancel")?>' + " '" + subject + "' ?"))
		{
			new Request.JSON({
				'method':'post',
				'url': "<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'delete'), 'admin_default', true); ?>",
				'data': {'format': 'json', 'task':'delete', 'campaign_id': id},
				'onRequest': function(){
					$('campaign_'+id).fade('out');
				},
				'onSuccess': function($resp){
					if ($resp.success == 1){
						$('campaign_'+id).destroy();
					}
				}
			}).send();
		}
	}
</script>
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

<div class="updates_admin_home_right" style="width: 170px; float: right;">
	<h3 class="sep" style="letter-spacing:0px"><span>
		<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'edit'), 'admin_default', true); ?>" style="text-decoration:none">
			<button onclick="location.href ='<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'edit'), 'admin_default', true); ?>' "><?php echo $this->translate('UPDATES_Create New Campaign'); ?></button>
		</a>
	</span></h3>
</div>

<div class="updates_admin_home_left" style="margin-top: 20px;">
	<h3 class="sep" style="width: 760px;"><span><?php echo $this->translate('UPDATES_Campaign Manager')?></span></h3>
	<div><?php echo $this->translate('UPDATES_VIEWS_SCRIPTS_ADMIN_CAMPAIGN_CAMPAIGN_MANAGER_DESCRIPTIOP')?></div>
</div>
<div style="clear:both;"></div>
<br/><br/>

<?php if (isset($this->instant_campaign) && $this->instant_campaign->campaign_id): ?>
	<?php if ($this->instant_campaign->getTotalRecipients() > $this->instant_campaign->sent): ?>
		<?php echo $this->translate('UPDATES_INSTANT_CAMPAIGN_SUCCESSFULLY_SENT_CONTINUE_MESSAGE', array($this->instant_campaign->sent, $this->baseUrl().'admin/tasks')); ?>
	<?php else: ?>
		<?php echo $this->translate(array('UPDATES_Instant Campaign successfully has been sent to %s recipient', 'UPDATES_Instant Campaign successfully has been sent to %s recipients', $this->instant_campaign->sent),$this->instant_campaign->sent); ?>
	<?php endif; ?>
	<br/><br/>
<?php endif; ?>

<?php if ($this->active_paginator->count() == 0 && $this->schedule_paginator->count() == 0 && $this->sent_paginator->count() == 0): ?>
	<?php echo $this->translate('UPDATES_No Campaign has been created yet.', $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'edit'),'admin_default', true)); ?>
<?php endif;?>

<?php if ($this->active_paginator->count() > 0): ?>
	<div class="updates_admin_home_left active_paginator_conteiner ajax_paginator_conteiner">
		<h3 class="sep">
				<span style='font-size: 14px; letter-spacing:0px;'>
					<?php echo $this->translate('UPDATES_Active Campaigns'); ?>
				</span>
		</h3>

		<div>
			 <?php echo $this->translate('UPDATES_VIEWS_SCRIPTS_ADMIN_CAMPAIGN_ACTIVE_CAMPAIGNS_DESCRIPTION'); ?>
		</div>
		<br />

		<div>
			<?php echo $this->paginationControl($this->active_paginator, null, array('_pagination.tpl', 'updates'), array('paginator_name'=>'active_paginator')); ?>
		</div>
		<br/>
		<div id="active_paginator_items">
			<?php echo $this->ajaxPaginator($this->active_paginator, 'active_paginator'); ?>
		</div>
	</div>
	<br/><br/>
<?php endif; ?>

<?php if ($this->schedule_paginator->count() > 0): ?>
	<div class="updates_admin_home_left schedule_paginator_conteiner ajax_paginator_conteiner">
		<h3 class="sep">
				<span style='font-size: 14px; letter-spacing:0px;'>
					<?php echo $this->translate('UPDATES_Schedule Campaigns'); ?>
				</span>
		</h3>
		<div>
			 <?php echo $this->translate('UPDATES_VIEWS_SCRIPTS_ADMIN_CAMPAIGN_SCHEDULE_CAMPAIGNS_DESCRIPTION'); ?>
		</div>
		<br />

		<div>
			<?php echo $this->paginationControl($this->schedule_paginator, null, array('_pagination.tpl', 'updates'), array('paginator_name'=>'schedule_paginator')); ?>
		</div>
		<br/>
		<div id="schedule_paginator_items">
			<?php echo $this->ajaxPaginator($this->schedule_paginator, 'schedule_paginator'); ?>
		</div>
	</div>
	<br/><br/>
<?php endif; ?>

<?php if ($this->sent_paginator->count() > 0): ?>
	<div class="updates_admin_home_left sent_paginator_conteiner ajax_paginator_conteiner">
		<h3 class="sep">
				<span style='font-size: 14px; letter-spacing:0px;'>
					<?php echo $this->translate('UPDATES_Sent Campaigns'); ?>
				</span>
		</h3>
		<div>
			 <?php echo $this->translate('UPDATES_VIEWS_SCRIPTS_ADMIN_CAMPAIGN_SENT_CAMPAIGNS_DESCRIPTION'); ?>
		</div>
		<br />
		<div>
			<?php echo $this->paginationControl($this->sent_paginator, null, array('_pagination.tpl', 'updates'), array('paginator_name'=>'sent_paginator')); ?>
		</div>
		<br/>
		<div id="sent_paginator_items">
			<?php echo $this->ajaxPaginator($this->sent_paginator, 'sent_paginator'); ?>
		</div>
	</div>
<?php endif; ?>