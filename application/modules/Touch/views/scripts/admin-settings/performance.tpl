<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: performance.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php echo $this->content()->renderWidget('touch.admin-main-menu', array('active'=>'touch_admin_main_settings')) ?>
<h3><?php echo $this->translate("TOUCH_Performance Settings") ?></h3>

<div class='settings'>
  <div class="touch admin_home_right">
    <?php echo $this->content()->renderWidget('touch.admin-quick-menu', array('menu_name'=>'touch_admin_settings', 'active'=>'touch_admin_settings_performance')); ?>
  </div>
  <div class="admin_home_middle">
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<div id="message" style="display:none;">
  <?php echo $this->message ?>
</div>
