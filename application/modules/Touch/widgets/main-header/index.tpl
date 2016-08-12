<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<!-- This is important  -->
<?php $dashboard_content = $this->touchContent('touch_index_index'); ?>
<!-- This is important  -->
<table cellpadding="0" cellspacing="0"><tr>
	<td  valign="middle" class="header_left">
	<?php if( $this->viewer->getIdentity()) :?>
		<a href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'notifications'), 'default', true) ?>" id="updates_toggle" class="touchajax notifications">0</a>
	<?php endif; ?>
	</td>

	<td  valign="middle" class="site-logo" align="center">
			<?php echo $this->content()->renderWidget('touch.menu-logo', $this->params); ?>
	</td>

	<td valign="middle" class="header_right">
		<a href="<?php echo $this->url(array('controller' => 'index'), 'touch_dashboard', true) ?>" id="dashboard" onclick="Smoothbox.openInline('global_dashboard'); return false;">
			<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/icons/dashboard.png" border="0" alt="<?php echo $this->translate('Dashboard'); ?>" style="vertical-align:bottom;"/>
		</a>
		<div id="global_dashboard">
			<div id="dashboard_elements">
        <!-- This is important  -->
        <?php echo $dashboard_content ?>
        <!-- This is important  -->
			</div>
		</div>
	</td>
</tr></table>


<script type='text/javascript'>
  var notificationUpdater;
  en4.core.runonce.add(function(){
    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdater = new NotificationUpdateHandler({
              'delay' : <?php echo $this->updateSettings;?>
            });
    notificationUpdater.start();
    window._notificationUpdater = notificationUpdater;
    <?php endif;?>
  });
</script>
