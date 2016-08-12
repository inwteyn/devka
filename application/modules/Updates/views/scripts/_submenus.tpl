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

<style type='text/css'>
ul.submenu
{
	display: none; 
	position: absolute; 
	border: 1px solid #AAAAAA; 
	border-top: 0px; 
	padding: 5px 10px;	
	min-width: 160px; 
	margin-top: 5px; 
	background: #ffffff;
	z-index: 1000;
}

/*ul#tasks_submenu {
  min-width: 127px;
}*/
	
ul.submenu li div a:hover
{
	color: #739EB9;
}

a.mouse_enter
{
	-moz-border-radius:4px 4px 0 0;
	background:none repeat scroll 0 0 #FFFFFF;
	border-color:#CCCCCC #CCCCCC -moz-use-text-color;
	border-style:solid solid none;
	border-width:1px 1px medium;
	padding:5px;
}

.ref_tab_left, .ref_tab_right
{
	border: 1px solid #AAAAAA;
	width: 190px;
	float: left;
	text-align: center;
	-moz-border-radius:4px 4px 0 0;
	padding: 5px;
}

.ref_tab_left
{
	-moz-border-radius:4px 0 0 4px;
}
.ref_tab_right
{
	-moz-border-radius:0 4px 4px 0;
}

div.ref_mouseenter
{
	background-color: #ffffff;
}

div.active_ref
{
	background-color: #ffffff;
	color: #000000;
	font-weight: bold;
}

.tabs ul li
{
    display: inline;
}

</style>

<script type="text/javascript">
en4.core.runonce.add(function()
{
	$el = $$('.updates_admin_main_stats').getParent('li');
	$el.setStyle('position', 'relative');
	$el.grab($('stats_submenu_container'));
	$$('.updates_admin_main_stats').destroy();
	$('stats_submenu_container').setStyle('display', '');

	$('stats_submenu_container').addEvents({
		'mouseenter':function(){
			$('updates_admin_main_stats').addClass('mouse_enter');
			$('stats_submenu').setStyle('display', 'inline');
		 },
		'mouseleave':function(){
			$('stats_submenu').setStyle('display', 'none');
			$('updates_admin_main_stats').removeClass('mouse_enter');
		 }
	});

	$el = $$('.updates_admin_main_settings').getParent('li');
	$el.setStyle('position', 'relative');
	if ($$('.navigation')[1].getStyle('text-align') == 'right') {
    $el.setStyle('margin-right', '77px');
  }
  else {
    $el.setStyle('margin-left', '77px');
  }

	$el.grab($('layout_submenu_container'));
	$$('.updates_admin_main_settings').destroy();
	$('layout_submenu_container').setStyle('display', '');

	$('layout_submenu_container').addEvents({
		'mouseenter':function(){
			$('updates_admin_main_settings').addClass('mouse_enter');
			$('layout_submenu').setStyle('display', 'inline');
		 },
		'mouseleave':function(){
			$('layout_submenu').setStyle('display', 'none');
			$('updates_admin_main_settings').removeClass('mouse_enter');
		 }
	});

  if ($$('.navigation')[1].getStyle('text-align') == 'right') {
    $$('.updates_admin_main_services').setStyle('margin-right', '77px');
  }
  else {
    $$('.updates_admin_main_services').setStyle('margin-left', '77px');
  }
});
</script>

<span id='stats_submenu_container' style='position: absolute; display: none; top: 0px'>
<div>
<nobr>
<a href="javascript:void(0);this.blur();" id="updates_admin_main_stats">
	<?php echo $this->translate('UPDATES_View Stats'); ?>
</a>
</nobr>
</div>
<ul id='stats_submenu' class='submenu'>
  <li class='submenu_item'>
   <div style='padding: 5px 0px;'>
   	<nobr>
   	<?php echo $this->htmlLink(
							 		array('route' => 'admin_default', 'module' => 'updates', 'controller' => 'stats', 'action' => 'index'), 
   					 	 		$this->translate('UPDATES_Updates Statistics'),
   					 	 		array('style' => 'border: 0px; padding-left: 0px;')) ?>
		</nobr>
   </div>
	</li>
  <li class='submenu_item'>
   <div style='padding:5px 0px;'>
   	<nobr>
   	<?php echo $this->htmlLink(
							 		array('route' => 'admin_default', 'module' => 'updates', 'controller' => 'stats', 'action' => 'campaign'),
   					 	 		$this->translate('UPDATES_Campaign Statistics'),
   					 	 		array('style' => 'border: 0px; padding-left: 0px;')) ?>
		</nobr>
   </div>
  </li>
</ul>
</span>


<span id='layout_submenu_container' style='position: absolute; display: none; top: 0px'>
<div>
<nobr>
<a href="javascript:void(0);this.blur();" id="updates_admin_main_settings">
	<?php echo $this->translate('Settings'); ?>
</a>
</nobr>
</div>
<ul id='layout_submenu' class='submenu'>
  <li class='submenu_item'>
   <div style='padding: 5px 0px;'>
   	<nobr>
   	<?php echo $this->htmlLink(
							 		array('route' => 'admin_default', 'module' => 'updates', 'controller' => 'settings', 'action' => 'index'),
   					 	 		$this->translate('UPDATES_GLOBAL_SETTINGS'),
   					 	 		array('style' => 'border: 0px; padding-left: 0px;')) ?>
		</nobr>
   </div>
	</li>
  <li class='submenu_item'>
   <div style='padding:5px 0px;'>
   	<nobr>
   	<?php echo $this->htmlLink(
							 		array('route' => 'admin_default', 'module' => 'updates', 'controller' => 'settings', 'action' => 'level'),
   					 	 		$this->translate('UPDATES_Member Level Settings'),
   					 	 		array('style' => 'border: 0px; padding-left: 0px;')) ?>
		</nobr>
   </div>
  </li>
</ul>
</span>