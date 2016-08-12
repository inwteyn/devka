<?php if ($this->type == 'tasks_paginator'): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style="width: 0.2%;">
          <?php echo $this->translate("UPDATES_ID") ?>
        </th>
        <th style="width: 1%;">
          <?php echo $this->translate("UPDATES_Type") ?>
        </th>
        <th style="width: 9%;">
          <?php echo $this->translate("UPDATES_Subject") ?>
        </th>
        <th style="width: 1%;">
          <?php echo $this->translate("UPDATES_Status") ?>
        </th>
        <th style="width: 1%;">
          <?php echo $this->translate("UPDATES_Progress") ?>
        </th>
        <th style="width: 9%;">
          <?php echo $this->translate("UPDATES_Recipients") ?>
        </th>
        <th style="width: 1%;">
          <?php echo $this->translate("UPDATES_Creation Date") ?>
        </th>
        <th style="width: 0.3%;">
          <?php echo $this->translate("Options") ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->items as $item ): ?>
      <tr>
        <td><input name='task_<?php echo $item->task_id; ?>' value='<?php echo $item->task_id; ?>' type='checkbox' class='checkbox'></td>
        <td><?php echo $item->task_id ?></td>
        <td><?php echo $item->type; ?></td>
        <td><?php echo $item->subject; ?></td>
        <td>
          <?php if ($item->finished == 0 && $item->cancelled == 0 && $item->scheduled == 0):
            echo $this->translate("UPDATES_Active");
          elseif($item->finished == 0 && $item->cancelled == 1 && $item->scheduled == 0):
            echo $this->translate("UPDATES_Cancelled");
          elseif($item->finished == 0 && $item->scheduled == 1):
            echo $this->translate("UPDATES_Scheduled");
          elseif ($item->finished == 1):
            echo $this->translate("UPDATES_Completed");
          endif;
          ?>
        </td>
        <td>
          <?php
            $progress = $item->sent / $item->total_recipients * 100;
            printf("%01.2f %s", $progress, '%');
          ?>
        </td>
        <td>
          <?php if ($item->type == 'updates') echo $this->translate("UPDATES_The updates has been sent to %s recipients in %s", $item->sent, $item->total_recipients) ?>
          <?php if ($item->type == 'campaign') echo $this->translate("UPDATES_The campaign has been sent to %s recipients in %s", $item->sent, $item->total_recipients) ?>
        </td>
        <td><?php echo $item->creation_date ?></td>
        <td>
          <?php if($item->finished == 0 && $item->cancelled == 0 && $item->scheduled == 0): ?>
            <a href="javascript:void(0)" onclick="cancelTask(<?php echo $item->task_id ?>)">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="javascript:void(0)" onclick="openConfirmDelete('<?php echo $item->task_id ?>')">Delete</a>
          <?php elseif ($item->finished == 0 && $item->cancelled == 1 && $item->scheduled == 0): ?>
            <a href="javascript:void(0)" onclick="restartTask(<?php echo $item->task_id ?>)">Restart</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="javascript:void(0)" onclick="openConfirmDelete('<?php echo $item->task_id ?>')">Delete</a>
          <?php else: ?>
          <span style="margin-right: 56px;">&nbsp;</span> <a href="javascript:void(0)" onclick="openConfirmDelete('<?php echo $item->task_id ?>')" >Delete</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else:?>
<table class='admin_table'>
	<thead>
		<tr>
			<th style='width: 1%;'><?php echo $this->translate("ID") ?></th>

			<th><?php echo $this->translate("UPDATES_Subject") ?></th>

			<?php if ($this->type == 'active_paginator'):?>
			<th style='width: 15%; text-align:center'><?php echo $this->translate("UPDATES_Type"); ?></th>
			<?php endif; ?>

			<th style="width: 25%;"><?php echo $this->translate("UPDATES_Recipients"); ?></th>

			<th style='width: 15%;'><?php echo $this->translate("UPDATES_Creation Date"); ?></th>

			<?php if ($this->type == 'schedule_paginator'):?>
			<th style='width: 15%;'><?php echo $this->translate("UPDATES_Planned Date"); ?></th>
			<?php endif; ?>

			<?php if ($this->type == 'active_paginator'):?>
			<th style='width: 15%;'><?php echo $this->translate("UPDATES_Activated Date"); ?></th>
			<?php endif; ?>

			<th style='width: 5%;'><?php echo $this->translate("UPDATES_Option"); ?></th>

		</tr>
	</thead>
	<tbody>
	<?php foreach( $this->items as $item ): ?>
			<tr <?php if(in_array($item->campaign_id, $this->soonCampaigns)): ?>class="soon_schedule"<?php endif; ?> id="campaign_<?php echo $item->campaign_id; ?>">
				<td><?php echo $item->campaign_id ?></td>
				<td class='admin_table_bold'><?php echo Engine_String::substr($item->subject, 0, 30); if (Engine_String::strlen($item->subject)>=30): echo '...'; endif;?></td>

				<?php if ($this->type == 'active_paginator'):?>
          <td style="text-align:center">
            <?php if ($item->type == 'schedule'): ?>
            <?php echo $this->translate('UPDATES_Schedule'); ?>
            <?php else: ?>
              <?php echo $this->translate('UPDATES_Instant'); ?>
            <?php endif; ?>
          </td>
				<?php endif; ?>

				<td>
					<?php if ($this->type == 'active_paginator'): ?>
						<?php echo $this->translate(array("UPDATES_The campaign sent to %s recipient in %s", "UPDATES_The campaign sent to %s recipients in %s", $item->sent), $item->sent, $item->getTotalRecipients()); ?>
					<?php elseif($this->type == 'schedule_paginator' && $item->finished == 0): ?>
						<?php echo $this->translate(array("UPDATES_The campaign will be sent to %s recipient", "UPDATES_The campaign will be sent to %s recipients", $item->getTotalRecipients()), $item->getTotalRecipients()); ?>
					<?php else: ?>
						<?php echo $this->translate(array("UPDATES_The campaign successfully has been sent to %s recipient", "UPDATES_The campaign successfully has been sent to %s recipients", $item->sent), $item->sent); ?>
					<?php endif; ?>
				</td>

				<td><?php echo $this->locale()->toDateTime($item->creation_date, array('size'=>'short', 'timezone'=>$this->timezone)); ?></td>

				<?php if ($this->type == 'schedule_paginator'):?>
				<td style="<?php if ($item->finished == 0 && strtotime($item->planned_date)>strtotime($this->locale()->toDateTime(time(), array('size'=>'short', 'timezone'=>$this->timezone)))):?> color: green; <?php endif; ?>">
					<?php $planned_date = date('m/d/Y h:i A', strtotime($item->planned_date)); ?>
					<?php echo $planned_date; ?>
				</td>
				<?php endif; ?>

				<?php if ($this->type == 'active_paginator'):?>
					<td><?php echo date('m/d/Y h:i A', strtotime($item->planned_date)); ?></td>
				<?php endif; ?>

				<td style="text-align:center;">
					<?php if ($this->type == 'active_paginator'):?>
						<a onclick="Smoothbox.open($('href_<?php echo $item->campaign_id; ?>'));" style="font-weight:bold" href="javascript://">
								<?php echo $this->translate('UPDATES_stop'); ?>
						</a>
					<a id="href_<?php echo $item->campaign_id; ?>" href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'index'), 'admin_default', true).'/stop/campaign_id/'.$item->campaign_id; ?>" style="display:none; text-decoration:none">&nbsp;</a>
					<?php elseif ($this->type == 'schedule_paginator' && $item->finished == 0): ?>
							<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'edit'), 'admin_default', true).'/'.$item->campaign_id; ?>" style="font-weight:bold">
								<?php echo $this->translate('UPDATES_edit'); ?>
							</a>&nbsp;-&nbsp;
							<a href="javascript:cancel_campaign('<?php echo $item->subject; ?>', '<?php echo $item->campaign_id; ?>')" style="font-weight:bold">
								<?php echo $this->translate('cancel'); ?>
							</a>
					<?php else: ?>
							<a href="<?php echo $this->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'edit'), 'admin_default', true).'/'.$item->campaign_id; ?>" style="font-weight:bold">
								<?php echo $this->translate('UPDATES_resend'); ?>
							</a>
					<?php endif; ?>
					</a>
				</td>

			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>