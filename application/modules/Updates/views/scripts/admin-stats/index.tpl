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

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">

  google.load("visualization", "1", {packages:["corechart"]});

function globalChart($title, $rows) {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Sent Date');
	data.addColumn('number', 'Sent');
	data.addColumn('number', 'Viewed');
	data.addColumn('number', 'Referred');
	data.addRows($rows);
	var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
	chart.draw(data, {width: 1000, height: 400, title: $title,
										hAxis: {title: '<?php echo $this->translate("UPDATES_Updates")?>', titleTextStyle: {color: '#FF0000'}},
									 });
}

function referredChart($title, $rows, $values, $id) {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Module name');
  data.addColumn('number', 'Referrals');
  data.addRows($rows);

  for (var $i=0; $i<$values.length; $i++)
  {
	  data.setValue($i, 0, $values[$i]['module_title']);
		data.setValue($i, 1, parseInt($values[$i]['module_referreds']));
  }

  var chart = new google.visualization.PieChart(document.getElementById($id));
  chart.draw(data, {
	  width: 600,
	  height: 400,
	  is3D: true,
	  title: $title
	  });
}

function load_referredChart($id){
	switch($id)
	{
		case 'current':
			$('all_ref_links').setStyle('display', 'none');
			$('all_ref_chart').setStyle('display', 'none');
			$('current_ref_links').setStyle('display', '');
			$('current_ref_chart').setStyle('display', '');
			break;

		case 'all':
			$('current_ref_links').setStyle('display', 'none');
			$('current_ref_chart').setStyle('display', 'none');
			$('all_ref_links').setStyle('display', '');
			$('all_ref_chart').setStyle('display', '');
			break;

			default:
			break;
	}

	$('content').setStyle('display', '');
}

//GLOBAL STATS
<?php	$updatesCount = $this->paginator->getTotalItemCount(); ?>

<?php if ($updatesCount > 0) : ?>

	var $globalTitle = '<?php echo $this->translate(array("UPDATES_Total: %s update sent"," %s updates sent", $updatesCount), $updatesCount)?>';
	var $globalRows = <?php echo json_encode($this->rows) ?>;

  en4.core.runonce.add(function()
	{
		globalChart($globalTitle, $globalRows);
	});
<?php	endif;?>
//END OF GLOBAL STATS


// REFERRED STATS
<?php	$referralCount = $this->total_referreds['all']; ?>
<?php if ($referralCount > 0 ) : ?>

	<?php if( count($this->module_referreds['current']) ): ?>
		var $current_rows = <?php echo count($this->module_referreds['current']); ?>;
		var $current_values =	<?php echo json_encode($this->module_referreds['current']); ?>;
		var $current_title = '<?php echo $this->translate(array("UPDATES_Total: %s referral","Total: %s referrals", $this->total_referreds["current"]), ($this->total_referreds["current"]))?>'
	<?php endif; ?>

	<?php if( count($this->module_referreds['all']) ): ?>
		var $all_rows = <?php echo count($this->module_referreds['all']); ?>;
		var $all_values =	<?php echo json_encode($this->module_referreds['all']); ?>;
		var $all_title = '<?php echo $this->translate(array("UPDATES_Total: %s referral","Total: %s referrals", $this->total_referreds["all"]), ($this->total_referreds["all"]))?>'
	<?php endif; ?>

	en4.core.runonce.add(function()
	{
		referredChart($current_title, $current_rows, $current_values, 'current_ref_chart');
		referredChart($all_title, $all_rows, $all_values, 'all_ref_chart');

		$$('.ref_tab').addEvents({
				'mouseenter':function(){
					$(this).setStyle('background-color', '#ECECED');
				},
				'mouseleave':function(){
					$(this).setStyle('background-color', '#ffffff');
				},
				'click':function(){
					$$('.ref_tab').removeClass('active_ref');
					$(this).addClass('active_ref');

					$('content').setStyle('display', 'none');
					load_referredChart($(this).getProperty('id'));
				}
			});
	});
<?php endif; ?>
//END OF REFERRED STATS

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

<div class="admin_home_left">
	<h3 class="sep">
			<span>
				<?php echo $this->translate("UPDATES_Global Updates Statistics"); ?>
			</span>
		</h3>
</div>
<div style="clear: both"></div>

<?php if ($updatesCount > 0): ?>
	<div class='admin_results'>
  	<div>
    <?php if($this->paginator->count()>1):?>
    <div class="pages">
      <ul class="paginationControl">
       <?php if ($this->paginator->getCurrentPageNumber()<$this->paginator->count()): ?>
          <li>
           <a href="<?php echo $this->url(array('action' => 'index', 'controller' => 'stats', 'module' => 'updates'), 'updates_stats', true)?>/<?php echo (int)($this->paginator->getCurrentPageNumber()+1); ?>">
            <?php echo $this->translate('UPDATES_ &#171; Previous') ?>
           </a>
          </li>
       <?php endif; ?>
       <?php $j = 0; for($i = $this->paginator->count(); $i>=1; $i--) : $j++;?>
          <?php if ($this->paginator->getCurrentPageNumber() == $i): ?>
            <li class="selected">
              <a href="javascript://"><?php echo $j; ?></a>
            </li>
          <?php else: ?>
            <li>
              <a href="<?php echo $this->url(array('action' => 'index', 'controller' => 'stats', 'module' => 'updates'), 'updates_stats', true)?>/<?php echo $i ?>"><?php echo $j?></a>
            </li>
          <?php endif; ?>
       <?php endfor; ?>
       <?php if ($this->paginator->getCurrentPageNumber()>1): ?>
          <li>
           <a href="<?php echo $this->url(array('action' => 'index', 'controller' => 'stats', 'module' => 'updates'), 'updates_stats', true)?>/<?php echo (int)($this->paginator->getCurrentPageNumber()-1); ?>">
            <?php echo $this->translate('UPDATES_ Next &#187;') ?>
           </a>
          </li>
       <?php endif; ?>
      </ul>
    </div>
    <?php endif; ?>
  	</div>
   </div>
   
   <br />
   
	<div id='chart_div'></div>
<?php else: ?>
	<div>
		<?php echo $this->translate(array("UPDATES_ %s update sent", "%s updates sent", $updatesCount), ($updatesCount)) ?>
	</div>
<?php endif; ?>

<br/><br/>

<div class="admin_home_left">
	<h3 class="sep">
			<span>
				<?php echo $this->translate("UPDATES_Detailed Referred Statistics"); ?>
			</span>
		</h3>
</div>
<div style="clear: both"></div>

<?php if ($referralCount > 0 ) :?>
	<div class='ref_tabs'>
		<a href='javascript://' id='all_ref_tab'><div class='ref_tab_left ref_tab' id='all'><?php echo $this->translate('UPDATES_All Referrals'); ?></div></a>
		<a href='javascript://' id='current_ref_tab'><div class='ref_tab_right ref_tab active_ref' id='current'><?php echo $this->translate('UPDATES_Current Month Referrals'); ?></div></a>
	</div>
	<div style='clear: both;'></div>
	<br/>
	<div id='content'>

		<?php if( count($this->link_referreds) ): ?>
			<div class="admin_home_right" style='width: 400px;'>
				<h3 class="sep">
					<span style='font-size: 10pt; letter-spacing:0px;'>
						<?php echo $this->translate('UPDATES_Top referred links') ?>
					</span>
				</h3>
				<br/><br/><br/>
				<div style='border-left: 2px solid #C3DEEC;'>
				<table class='admin_home_stats' style='padding-left: 10px;'>
					<thead>
						<tr>
							<th><?php echo $this->translate('UPDATES_Links') ?></th>
							<th align='center'><?php echo $this->translate('UPDATES_Referred') ?></th>
						</tr>
					</thead>
					<tbody id='current_ref_links'>
						<?php foreach ($this->link_referreds['current'] as $item):?>
						<tr>
								<td style='width: 400px'><a href="<?php echo $item['link']; ?>" target='blank'><?php echo $item['link'] ?></a></td>
								<td style="text-align:center"><?php echo $item['referred_count'] ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
					<tbody id='all_ref_links' style='display: none'>
						<?php foreach ($this->link_referreds['all'] as $item):?>
						<tr>
								<td style='width: 400px'><a href="$item['link']" target='blank'><?php echo $item['link'] ?></a></td>
								<td style="text-align:center"><?php echo $item['referred_count'] ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				</div>
			</div>
		<?php endif; ?>

		<div class="admin_home_middle">
			<div class="admin_home_left">
				<h3 class="sep">
						<span style='font-size: 10pt; letter-spacing:0px;'><?php echo $this->translate('UPDATES_Referred Modules'); ?></span>
				</h3>
				<div id='current_ref_chart'></div>
				<div id='all_ref_chart' style='display: none'></div>
				<br />
			</div>
		</div>
	</div>

<?php else: ?>
	<div>
   	<?php echo $this->translate(array("UPDATES_ %s referral found", "%s referrals found", ($referralCount)?$referralCount:0), (($referralCount)?$referralCount:0)) ?>
	</div>
<?php endif; ?>

</div>