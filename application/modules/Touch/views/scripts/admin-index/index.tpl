<?php echo $this->content()->renderWidget('touch.admin-main-menu', array('active'=>'touch_admin_main_home')) ?>

<div class="admin_home_wrapper">

  <div class="admin_home_right">
    <?php echo $this->content()->renderWidget('touch.admin-statistics') ?>
  </div>

  <div class="admin_home_middle">
    <p>
      <?php echo $this->translate('TOUCH_ADMIN_DASHBOARD'); ?>
    </p>
    <br />
    <?php echo $this->content()->renderWidget('touch.admin-dashboard') ?>
    <?php echo $this->content()->renderWidget('touch.admin-iphone-simulator') ?>
  </div>

</div>
