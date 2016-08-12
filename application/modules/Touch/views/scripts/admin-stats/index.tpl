<?php echo $this->content()->renderWidget('touch.admin-main-menu', array('active'=>'touch_admin_main_statistics')) ?>
<!--<h3>--><?php //echo $this->translate('TOUCH_Touch-Mobile Site Wide Statistics'); ?><!--</h3>-->
<div class="touch admin_home_right">
  <?php echo $this->content()->renderWidget('touch.admin-quick-menu', array('menu_name'=>'touch_admin_stats', 'active'=>'touch_admin_stats_general')); ?>
</div>
<div class="admin_home_middle">
<?php echo $this->content()->renderWidget('touch.admin-statistics', array('show_as_chart'=> true)) ?>
</div>
