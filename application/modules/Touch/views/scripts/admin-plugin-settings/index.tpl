
<?php echo $this->content()->renderWidget('touch.admin-main-menu', array('active'=>'touch_admin_main_settings')) ?>
<h3><?php echo $this->translate("TOUCH_PLUGIN_SETTINGS") ?></h3>
<?php if($this->yesrate): ?>
<div class="settings">
  <div class="touch admin_home_right">
    <?php echo $this->content()->renderWidget('touch.admin-quick-menu', array('menu_name'=>'touch_admin_settings', 'active'=>'touch_admin_settings_rate')); ?>
  </div>
  <div class="admin_home_middle">
    <?php echo $this->form->render()?>
  </div>
</div>

<?php else: ?>

<?php echo $this->translate("TOUCH_RATE_REQUIRED_DESC"); ?>

<?php endif; ?>
